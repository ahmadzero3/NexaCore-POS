<?php

namespace App\Http\Controllers\Party;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Traits\FormatNumber;
use App\Traits\FormatsDateInputs;
use App\Enums\General;
use App\Models\Sale\Sale;
use App\Models\Sale\SaleReturn;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseReturn;
use App\Models\Party\Party;
use App\Models\Items\ItemTransaction;
use App\Models\PaymentTransaction;
use App\Models\Party\PartyTransaction;
use App\Models\Party\PartyPaymentAllocation;
use App\Models\Party\PartyPayment;
use App\Models\PartyBalanceAfterAdjustment;
use App\Models\Sale\Quotation;
use App\Services\PartyService;
use App\Services\PaymentTransactionService;
use Mpdf\Mpdf;

class PartyTransactionController extends Controller
{
    use FormatsDateInputs;

    use FormatNumber;

    private $partyService;

    private $paymentTransactionService;

    public function __construct(PartyService $partyService, PaymentTransactionService $paymentTransactionService)
    {
        $this->partyService = $partyService;
        $this->paymentTransactionService = $paymentTransactionService;
    }
    public function list($partyType, $id) : View {
        $party = Party::findOrFail($id);

        $balance = $this->partyService->getPartyBalance($party->id);
        $partyData = [
                        'balance' => $balance['balance'],
                        'party_type' => ($party->party_type == 'customer') ? __('customer.customers') : __('supplier.suppliers'),
                        'balance_message' => ($balance['status'] == 'no_balance')
                                            ? __('payment.no_balance')
                                            : (($balance['status'] == 'you_pay')
                                                ? __('payment.you_pay')
                                                : __('payment.you_collect')),
                    ];

        return view('party.transaction.list', compact('party', 'partyData'));
    }

    public function datatableList(Request $request)
    {

        $rules = [
            'from_date' => ['nullable', 'date_format:' . implode(',', $this->getDateFormats())],
            'to_date'   => ['nullable', 'date_format:' . implode(',', $this->getDateFormats())],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $fromDate = $this->toSystemDateFormat($request->input('from_date'));
        $toDate = $this->toSystemDateFormat($request->input('to_date'));

        $party = Party::findOrFail($request->party_id);

        // Collect all transactions
        $allTransactions = collect();

        // 1. Opening Balance
        $openingBalances = $party->transaction;
        if ($openingBalances) {
            foreach ($openingBalances as $openingBalance) {
              $status = $openingBalance->to_pay > 0 ? 'To Pay' : ($openingBalance->to_receive > 0 ? 'To Receive' : '');
              $allTransactions->push([
                    'transaction_type' => 'Opening Balance',
                    'transaction_date' => $openingBalance->transaction_date,
                    'status' => $status,
                    'payment_type' => '',
                    'amount' => $openingBalance->to_pay - $openingBalance->to_receive,
                    'created_by' => $openingBalance->created_by,
                    'created_at' => $openingBalance->created_at,
                    'note'=>'',
                ]);
            }
        }

        // 2. Party Balance Adjustments
        $partyPayments = PartyPayment::with('partyPaymentAllocation.paymentTransaction') // Ensure to load related paymentTransaction
            ->where('party_id', $party->id)
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->when($fromDate, function ($q) use ($fromDate) {
                    return $q->where('transaction_date', '>=', $fromDate);
                })
                ->when($toDate, function ($q) use ($toDate) {
                    return $q->where('transaction_date', '<=', $toDate);
                });
            })
            ->get();


        foreach ($partyPayments as $partyPayment) {
            // Push main party payment transaction
            $allTransactions->push([
                'transaction_type' => 'Party Payment(Manual)',
                'transaction_date' => $partyPayment->transaction_date,
                'status' => 'Completed',
                'payment_type' => $partyPayment->paymentType->name,
                'amount' => $partyPayment->amount,
                'created_by' => $partyPayment->created_by,
                'created_at' => $partyPayment->created_at,
                'note'=> $partyPayment->note,
            ]);

            // Process related payment allocations
            if ($partyPayment->partyPaymentAllocation->isNotEmpty()) {
                foreach ($partyPayment->partyPaymentAllocation as $adjustment) {
                    $allTransactions->push([
                        'transaction_type' => $adjustment->paymentTransaction()->first()->transaction instanceof \App\Models\Purchase\Purchase
                            ? 'Balance Adjusted to Purchase Payment (' . $adjustment->paymentTransaction()->first()->transaction->purchase_code . ')'
                            : 'Balance Adjusted to Sale Payment (' . $adjustment->paymentTransaction()->first()->transaction->sale_code . ')',
                        'transaction_date' => $partyPayment->transaction_date,
                        'status' => 'Completed',
                        'payment_type' => $partyPayment->paymentType->name,
                        'amount' => -$adjustment->paymentTransaction->amount, // Negative as it's a payment
                        'created_by' => $adjustment->created_by,
                        'created_at' => $adjustment->paymentTransaction()->first()->created_at,
                        'note'=>'Adjusted from Party Manual Payment',
                    ]);
                }
            }
        }


        // 3. Sales
        $sales = Sale::with(['paymentTransaction' => function ($query) use ($fromDate, $toDate) {
                        $query->when($fromDate, function ($q) use ($fromDate) {
                            return $q->where('transaction_date', '>=', $fromDate);
                        })
                        ->when($toDate, function ($q) use ($toDate) {
                            return $q->where('transaction_date', '<=', $toDate);
                        });
                    }])
                    ->where('party_id', $party->id)
                    ->where(function ($query) use ($fromDate, $toDate) {
                        $query->when($fromDate, function ($q) use ($fromDate) {
                            return $q->where('sale_date', '>=', $fromDate)
                                     ->orWhereHas('paymentTransaction', function ($q) use ($fromDate) {
                                         $q->where('transaction_date', '>=', $fromDate);
                                     });
                        })
                        ->when($toDate, function ($q) use ($toDate) {
                            return $q->where('sale_date', '<=', $toDate)
                                     ->orWhereHas('paymentTransaction', function ($q) use ($toDate) {
                                         $q->where('transaction_date', '<=', $toDate);
                                     });
                        });
                    })
                    ->get();
        foreach ($sales as $sale) {
            $status = match (true) {
                            $sale->paid_amount == 0 => 'Unpaid',
                            $sale->paid_amount < $sale->grand_total => 'Partial',
                            default => 'Paid',
                        };

            $allTransactions->push([
                'transaction_type' => "Sale ({$sale->sale_code})",
                'transaction_date' => $sale->sale_date,
                'status' => $status,
                'payment_type' => '',
                'amount' => $sale->grand_total,
                'created_by' => $sale->created_by,
                'created_at' => $sale->created_at,
                'note'=> '',
            ]);

            // 4. Sales Payments
            foreach ($sale->paymentTransaction as $payment) {
                $allTransactions->push([
                    'transaction_type' => 'Sale Payment ('. $payment->transaction->sale_code.')',
                    'transaction_date' => $payment->transaction_date,
                    'status' => 'Completed',
                    'payment_type' => $payment->paymentType->name,
                    'amount' => -$payment->amount, // Negative as it's a payment
                    'created_by' => $payment->created_by,
                    'created_at' => $payment->created_at,
                    'note'=> $payment->note,
                ]);
            }
        }

        // 5. Sales Returns
        $saleReturns = SaleReturn::with(['paymentTransaction' => function ($query) use ($fromDate, $toDate) {
                                    $query->when($fromDate, function ($q) use ($fromDate) {
                                        return $q->where('transaction_date', '>=', $fromDate);
                                    })
                                    ->when($toDate, function ($q) use ($toDate) {
                                        return $q->where('transaction_date', '<=', $toDate);
                                    });
                                }])
                                ->where('party_id', $party->id)
                                ->where(function ($query) use ($fromDate, $toDate) {
                                    $query->when($fromDate, function ($q) use ($fromDate) {
                                        return $q->where('return_date', '>=', $fromDate)
                                                 ->orWhereHas('paymentTransaction', function ($q) use ($fromDate) {
                                                     $q->where('transaction_date', '>=', $fromDate);
                                                 });
                                    })
                                    ->when($toDate, function ($q) use ($toDate) {
                                        return $q->where('return_date', '<=', $toDate)
                                                 ->orWhereHas('paymentTransaction', function ($q) use ($toDate) {
                                                     $q->where('transaction_date', '<=', $toDate);
                                                 });
                                    });
                                })
                                ->get();
        foreach ($saleReturns as $return) {
            $allTransactions->push([
                'transaction_type' => "Sale Return ({$return->return_code})",
                'transaction_date' => $return->return_date,
                'status' => $return->grand_total > $return->paid_amount ? 'Partial' : 'Paid',
                'payment_type' => '-',
                'amount' => -$return->grand_total, // Negative as it's a return
                'created_by' => $return->created_by,
                'created_at' => $return->created_at,
                'note'=>'',
            ]);

            // 5. Sales Return Payments
            foreach ($return->paymentTransaction as $payment) {
                $allTransactions->push([
                    'transaction_type' => 'Sale Return Payment',
                    'transaction_date' => $payment->transaction_date,
                    'status' => 'Completed',
                    'payment_type' => $payment->paymentType->name,
                    'amount' => $payment->amount,
                    'created_by' => $payment->created_by,
                    'created_at' => $payment->created_at,
                    'note'=> $payment->note,
                ]);
            }
        }

        // 3. Purchases
        $purchases = Purchase::with(['paymentTransaction' => function ($query) use ($fromDate, $toDate) {
                                $query->when($fromDate, function ($q) use ($fromDate) {
                                    return $q->where('transaction_date', '>=', $fromDate);
                                })
                                ->when($toDate, function ($q) use ($toDate) {
                                    return $q->where('transaction_date', '<=', $toDate);
                                });
                            }])
                            ->where('party_id', $party->id)
                            ->where(function ($query) use ($fromDate, $toDate) {
                                $query->when($fromDate, function ($q) use ($fromDate) {
                                    return $q->where('purchase_date', '>=', $fromDate)
                                             ->orWhereHas('paymentTransaction', function ($q) use ($fromDate) {
                                                 $q->where('transaction_date', '>=', $fromDate);
                                             });
                                })
                                ->when($toDate, function ($q) use ($toDate) {
                                    return $q->where('purchase_date', '<=', $toDate)
                                             ->orWhereHas('paymentTransaction', function ($q) use ($toDate) {
                                                 $q->where('transaction_date', '<=', $toDate);
                                             });
                                });
                            })
                            ->get();
        foreach ($purchases as $purchase) {
            $status = match (true) {
                $purchase->paid_amount == 0 => 'Unpaid',
                $purchase->paid_amount < $purchase->grand_total => 'Partial',
                default => 'Paid',
            };

            $allTransactions->push([
                'transaction_type' => "Purchase ({$purchase->purchase_code})",
                'transaction_date' => $purchase->purchase_date,
                'status' => $status,
                'payment_type' => '',
                'amount' => $purchase->grand_total,
                'created_by' => $purchase->created_by,
                'created_at' => $purchase->created_at,
                'note'=>'',
            ]);

            // 4. Purchase Payments
            foreach ($purchase->paymentTransaction as $payment) {
                $allTransactions->push([
                    'transaction_type' => 'Purchase Payment ('. $payment->transaction->purchase_code.')',
                    'transaction_date' => $payment->transaction_date,
                    'status' => 'Completed',
                    'payment_type' => $payment->paymentType->name,
                    'amount' => -$payment->amount, // Negative as it's a payment
                    'created_by' => $payment->created_by,
                    'created_at' => $payment->created_at,
                    'note'=> $payment->note,
                ]);
            }
        }

        // 5. Purchase Returns
        $purchaseReturns = PurchaseReturn::with(['paymentTransaction' => function ($query) use ($fromDate, $toDate) {
                                            $query->when($fromDate, function ($q) use ($fromDate) {
                                                return $q->where('transaction_date', '>=', $fromDate);
                                            })
                                            ->when($toDate, function ($q) use ($toDate) {
                                                return $q->where('transaction_date', '<=', $toDate);
                                            });
                                        }])
                                        ->where('party_id', $party->id)
                                        ->where(function ($query) use ($fromDate, $toDate) {
                                            $query->when($fromDate, function ($q) use ($fromDate) {
                                                return $q->where('return_date', '>=', $fromDate)
                                                         ->orWhereHas('paymentTransaction', function ($q) use ($fromDate) {
                                                             $q->where('transaction_date', '>=', $fromDate);
                                                         });
                                            })
                                            ->when($toDate, function ($q) use ($toDate) {
                                                return $q->where('return_date', '<=', $toDate)
                                                         ->orWhereHas('paymentTransaction', function ($q) use ($toDate) {
                                                             $q->where('transaction_date', '<=', $toDate);
                                                         });
                                            });
                                        })
                                        ->get();
        foreach ($purchaseReturns as $return) {
            $allTransactions->push([
                'transaction_type' => "Purchase Return ({$return->return_code})",
                'transaction_date' => $return->return_date,
                'status' => $return->grand_total > $return->paid_amount ? 'Partial' : 'Paid',
                'payment_type' => '-',
                'amount' => -$return->grand_total, // Negative as it's a return
                'created_by' => $return->created_by,
                'created_at' => $return->created_at,
                'note'=>'',
            ]);

            // 5. Purchase Return Payments
            foreach ($return->paymentTransaction as $payment) {
                $allTransactions->push([
                    'transaction_type' => 'Purchase Return Payment',
                    'transaction_date' => $payment->transaction_date,
                    'status' => 'Completed',
                    'payment_type' => $payment->paymentType->name,
                    'amount' => $payment->amount,
                    'created_by' => $payment->created_by,
                    'created_at' => $payment->created_at,
                    'note'=> $payment->note,
                ]);
            }
        }

        // 6. Quotation
        $quotations = Quotation::with(['paymentTransaction' => function ($query) use ($fromDate, $toDate) {
            $query->when($fromDate, function ($q) use ($fromDate) {
                return $q->where('transaction_date', '>=', $fromDate);
            })
            ->when($toDate, function ($q) use ($toDate) {
                return $q->where('transaction_date', '<=', $toDate);
            });
        }])
        ->where('party_id', $party->id)
        ->where(function ($query) use ($fromDate, $toDate) {
            $query->when($fromDate, function ($q) use ($fromDate) {
                return $q->where('quotation_date', '>=', $fromDate)
                         ->orWhereHas('paymentTransaction', function ($q) use ($fromDate) {
                             $q->where('transaction_date', '>=', $fromDate);
                         });
            })
            ->when($toDate, function ($q) use ($toDate) {
                return $q->where('quotation_date', '<=', $toDate)
                         ->orWhereHas('paymentTransaction', function ($q) use ($toDate) {
                             $q->where('transaction_date', '<=', $toDate);
                         });
            });
        })
        ->get();
        foreach ($quotations as $quotation) {
            $allTransactions->push([
                'transaction_type' => "Quotation ({$quotation->quotation_code})",
                'transaction_date' => $quotation->quotation_date,
                'status' => '-',
                'payment_type' => '-',
                'amount' => $quotation->grand_total,
                'created_by' => $quotation->created_by,
                'created_at' => $quotation->created_at,
                'note'=>'',
            ]);

            // 6. Quotation Payments
            // foreach ($quotation->paymentTransaction as $payment) {
            //     $allTransactions->push([
            //     'transaction_type' => 'Quotation Payment',
            //     'transaction_date' => $payment->transaction_date,
            //     'status' => 'Completed',
            //     'payment_type' => $payment->paymentType->name,
            //     'amount' => $payment->amount,
            //     'created_by' => $payment->created_by,
            //     'created_at' => $payment->created_at,
            //     ]);
            // }
        }

        // Sort all transactions by date in descending order
        $sortedTransactions = $allTransactions->sortByDesc('transaction_date');

        return DataTables::of($sortedTransactions)
            ->addIndexColumn()
            ->addColumn('transaction_type', function ($row) {
                return $row['transaction_type'];
            })
            ->addColumn('transaction_date', function ($row) {
                return $this->toUserDateFormat($row['transaction_date']);
            })
            ->addColumn('status', function ($row) {
                return $row['status'];
            })
            ->addColumn('payment_type', function ($row) {
                return $row['payment_type'];
            })
            ->addColumn('amount', function ($row) {
                return $this->formatWithPrecision($row['amount']);
            })
            ->addColumn('created_by', function ($row) {
                return $row['created_by'];
            })
            ->addColumn('created_at', function ($row) {
                return $this->toUserDateFormat($row['created_at']);
            })
            ->make(true);
    }


    public function partyPayment($partyType, $id) : View {
        $party = Party::findOrFail($id);

        $balance = $this->partyService->getPartyBalance($party->id);
        $partyData = [
            'balance'           => $balance['balance'],
            'party'             => ($party->party_type == 'customer') ? __('customer.customer')         : __('supplier.supplier'),
            'party_type'        => ($party->party_type == 'customer') ? __('customer.customers')        : __('supplier.suppliers'),
            'adjust_message'    => ($party->party_type == 'customer') ? __('payment.adjust_invoices')   : __('payment.adjust_bills'),
            'balance_message' => ($balance['status'] == 'no_balance')
                                            ? __('payment.no_balance')
                                            : (($balance['status'] == 'you_pay')
                                                ? __('payment.you_pay')
                                                : __('payment.you_collect')),
            'auto_payment_direction' => ($balance['status'] == 'no_balance')
                                            ? 'pay'
                                            : (($balance['status'] == 'you_pay')
                                                ? 'pay'
                                                : 'collect'),
        ];
        return view('party.payment.create', compact('party', 'partyData'));
    }

    public function getDueRecords($partyId) : JsonResponse
    {
        try{
            $party = Party::findOrFail($partyId);

            $balance = $this->partyService->getPartyBalance($party->id);

            $model = $party->party_type == 'customer' ? Sale::class : Purchase::class;
            $preparedData = $model::with('party')->where('party_id', $partyId)
                                ->whereRaw('grand_total - paid_amount > 0')
                                ->get();


            if($preparedData->count() == 0){
                throw new \Exception('No Records Found!!');
            }

            $recordsArray = [];

            foreach ($preparedData as $data) {
                $transactionDate = $data->party->party_type == 'customer' ? $data->sale_date : $data->purchase_date;
                $transactionCode = $data->party->party_type == 'customer' ? $data->sale_code : $data->purchase_code;

                $recordsArray[] = [
                                    'id'                    => $data->id,
                                    'transaction_date'       => $this->toUserDateFormat($transactionDate),
                                    'invoice_or_bill_code'  => $transactionCode,
                                    'grand_total'           => $this->formatWithPrecision($data->grand_total, comma:false),
                                    'paid_amount'           => $this->formatWithPrecision($data->paid_amount, comma:false),
                                    'balance'               => $this->formatWithPrecision($data->grand_total - $data->paid_amount , comma:false),
                                ];
            }

            return response()->json([
                        'status'    => true,
                        'message' => "Records are retrieved!!",
                        'data' => $recordsArray,
                    ]);
        } catch(\Exception $e){
            return response()->json([
                        'status'    => false,
                        'message' => $e->getMessage(),
                    ]);
        }
    }

    public function storePartyPayment(Request $request)
    {

        try {
            DB::beginTransaction();

            // Validation rules

            $rules = [
                'transaction_date'  => 'required|date_format:'.implode(',', $this->getDateFormats()),
                'receipt_no'        => 'nullable|string|max:255',
                'payment_type_id'   => 'required|integer',
                'payment'           => 'required|numeric|gt:0',
                'payment_direction' => 'required|string|in:pay,receive',
            ];

            //validation message
            $messages = [
                'transaction_date.required' => 'Payment date is required.',
                'payment_type_id.required'  => 'Payment type is required.',
                'payment.required'          => 'Payment amount is required.',
                'payment.gt'                => 'Payment amount must be greater than zero.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }
            $partyId            = $request->input('party_id');
            $transactionDate    = $request->input('transaction_date');
            $receiptNo          = $request->input('receipt_no');
            $paymentTypeId      = $request->input('payment_type_id');
            $payment            = $request->input('payment');
            $paymentNote        = $request->input('payment_note');
            $record             = $request->input('record');
            $paymentDirection   = $request->input('payment_direction');


            /**
             * Get Party details
             * */
            $party = Party::find($partyId);

            /**
             * Record Payment Entry in partyPayment model
             * */
            $partyPayment = PartyPayment::create(
                    [
                        'transaction_date'          =>  $this->toSystemDateFormat($transactionDate),
                        'payment_type_id'           =>  $paymentTypeId,
                        'amount'                    =>  $payment,
                        'note'                      =>  $paymentNote,
                        'reference_no'              =>  $receiptNo??null,
                        'party_id'                  =>  $partyId,
                        'payment_direction'         =>  $paymentDirection,
                    ]
                );
            if(!$partyPayment){
                throw new \Exception(__('payment.failed_to_record_payment_transactions'));
            }

            if(!empty($record) && count($record)>0){

                $sumOfArrayAmount = array_sum($record);
                if($sumOfArrayAmount > $payment){
                    throw new \Exception(__("The adjusted amount shouldn't be greater than the paid amount!"));
                }

                /**
                 * Validate the party
                 * if is 'customer' and payment direction if 'receive' then allow to adjust invoice else though a
                 * error message this transaction is not allowed
                 * */
                if($party->party_type == 'customer'){
                    //validate it should be a receive payment transaction
                    if($paymentDirection != 'receive'){
                        throw new \Exception(__("To adjust the payment against the invoice, you should select the 'You Collect' option!"));
                    }
                }else{
                    //supplier
                    if($paymentDirection != 'pay'){
                        throw new \Exception(__("To adjust the payment against the bill, please select the 'You Pay' option!"));
                    }
                }

                foreach($record as $recordId => $amount){
                    /**
                     * Validate invoice/bill amount
                     * */
                    if(empty($amount) || $amount== 0){
                        continue;
                    }

                    if ($party->party_type == 'customer') {
                        $this->validateModel($mainModel = Sale::find($recordId), $partyId, 'Invoice', $amount);
                    } else {
                        $this->validateModel($mainModel = Purchase::find($recordId), $partyId, 'Bill', $amount);
                    }

                    //Now Record Payments
                    $paymentsArray = [
                        'transaction_date'          => $transactionDate,
                        'amount'                    => $amount,
                        'payment_type_id'           => $paymentTypeId,
                        'reference_no'              => $receiptNo,
                        'note'                      => $paymentNote,
                        'payment_from_unique_code'  => General::PARTY_INVOICE_LIST->value,//Saving from party-list page
                    ];

                    if(!$transaction = $this->paymentTransactionService->recordPayment($mainModel, $paymentsArray)){
                        throw new \Exception(__('payment.failed_to_record_payment_transactions'));
                    }

                    /**
                     * Update Sale Model
                     * Total Paid Amunt
                     * */
                    if(!$this->paymentTransactionService->updateTotalPaidAmountInModel($mainModel)){
                        throw new \Exception(__('payment.failed_to_update_paid_amount'));
                    }

                    /**
                     * Record it in PartyPaymentAllocation model
                     * */
                    $allocation = PartyPaymentAllocation::create(
                        [
                            'party_payment_id' =>  $partyPayment->id,
                            'payment_transaction_id' =>  $transaction->id,
                        ]);

                }//foreach $record
            }//$record count


            /**
             * Save Remaining Balance or unadjusted Amount in a payment_transaction table
             * */
            //First find the id of the party payment allocation
            $sumOfAdjustedAmount = PartyPaymentAllocation::where('party_payment_id', $partyPayment->id)
                ->with('paymentTransaction')
                ->get()
                ->sum('paymentTransaction.amount');

            $remainingAmount = $payment - $sumOfAdjustedAmount;

            if($remainingAmount > 0){

                $paymentsArray = [
                    'transaction_date'          => $transactionDate,
                    'amount'                    => $remainingAmount,
                    'payment_type_id'           => $paymentTypeId,
                    'reference_no'              => null,
                    'note'                      => null,
                    'payment_from_unique_code'  => General::PARTY_BALANCE_AFTER_ADJUSTMENT->value,//Saving from party-list page
                ];


                if(!$transaction = $this->paymentTransactionService->recordPayment($partyPayment, $paymentsArray)){
                    throw new \Exception(__('payment.failed_to_record_payment_transactions'));
                }

                /**
                 * Record it in PartyBalanceAfterAdjustment model
                 * */
                $allocation = PartyBalanceAfterAdjustment::create(
                    [
                        'party_payment_id' =>  $partyPayment->id,
                        'payment_transaction_id' =>  $transaction->id,
                    ]);
            }

            DB::commit();

            return response()->json([
                'status'    => true,
                'message'   => __('app.record_saved_successfully'),
                'id'        => $partyPayment->id,
            ]);


        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }

    }
    /**
     * Validate model for party and payment status.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @param int $partyId
     * @param string $documentType
     * @throws \Exception
     */
    function validateModel($model, $partyId, $documentType, $amount)
    {
        $balance = $model->grand_total - $model->paid_amount;
        if ($model) {
            if ($model->party_id != $partyId) {
                throw new \Exception($documentType . ' Code ' . $model->sale_code . ' does not belong to this party!');
            }

            if ($model->grand_total == $model->paid_amount) {
                throw new \Exception($documentType . ' Code ' . $model->sale_code . ' payment is already completed!');
            }

            if ($amount > $balance) {
                throw new \Exception($documentType . ' Code ' . $model->sale_code . ' payment exceeds the total!');
            }
        }
    }

    /**
     * Print Sale
     *
     * @param int $id, the ID of the sale
     * @return \Illuminate\View\View
     */
    public function printPartyPayment($id, $isPdf = false) : View {
        $payment = PartyPayment::with(['paymentType', 'party'])->find($id);
        $balanceData = $this->partyService->getPartyBalance($payment->party->id);
        return view('print.party-payment-receipt', compact('isPdf', 'payment', 'balanceData'));
    }

    /**
     * Generate PDF using View: print() method
     * */
    public function pdfPartyPayment($id){
        $html = $this->printPartyPayment($id, isPdf:true);

        $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 2,
                'margin_right' => 2,
                'margin_top' => 2,
                'margin_bottom' => 2,
                'default_font' => 'dejavusans',
                //'direction' => 'rtl',
            ]);

        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($html);
        /**
         * Display in browser
         * 'I'
         * Downloadn PDF
         * 'D'
         * */
        $mpdf->Output('Party-Payment-'.$id.'.pdf', 'D');
    }

    /***
     * View Payment History
     *
     * */
    public function getPartyPaymentHistory($id) : JsonResponse{

        $data = $this->getPartyPaymentHistoryData($id);

        return response()->json([
            'status' => true,
            'message' => '',
            'data'  => $data,
        ]);

    }

    function getPartyPaymentHistoryData($partyId)
    {
        try{
            $transactions = PartyPayment::with('party', 'paymentType')->where('party_id', $partyId)->get();

            // Check if the transactions collection is empty
            if ($transactions->isEmpty()) {
                throw new \Exception('No Payment (Manual) History found!!');
            }

            $firstTransaction = $transactions->first();

            $balance = $this->partyService->getPartyBalance($firstTransaction->party->id);

            $data = [
                'party_id' => $firstTransaction->party->id,
                'party_name' => $firstTransaction->party->getFullName(),
                'balance' => $balance['balance'],
                'balance_type' => $balance['status'],

                'partyPayments' => $transactions->map(function ($transaction) {
                    return [
                        'payment_id' => $transaction->id,
                        'payment_direction' => $transaction->payment_direction=='pay' ? 'You Paid' : 'You Received',
                        'color' => $transaction->payment_direction=='pay' ? 'danger' : 'success',
                        'transaction_date' => $this->toUserDateFormat($transaction->transaction_date),
                        'reference_no' => $transaction->reference_no ?? '',
                        'payment_type' => $transaction->paymentType->name,
                        'amount' => $this->formatWithPrecision($transaction->amount),
                    ];
                })->toArray(),
            ];

            return $data;
        } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }
    }

    public function deletePartyPayment($paymentId) : JsonResponse{
        try {
            DB::beginTransaction();
            $partyPayment = PartyPayment::find($paymentId);

            if(!$partyPayment){
                throw new \Exception(__('payment.failed_to_delete_payment_transactions'));
            }

            //party model allocations if exist
            $partyPaymentAllocation = PartyPaymentAllocation::where('party_payment_id', $paymentId)->get();
            if($partyPaymentAllocation->count() > 0){
                foreach($partyPaymentAllocation as $allocation){
                    //based payment_transaction_id delete the PaymentTransaction model
                    $paymentTransaction = PaymentTransaction::find($allocation->payment_transaction_id);

                    $model = $paymentTransaction->transaction;//Sale, Purchase, Return's

                    $paymentTransaction->delete();

                    if(!$this->paymentTransactionService->updateTotalPaidAmountInModel($model)){
                        throw new \Exception(__('payment.failed_to_update_paid_amount'));
                    }
                }
            }

            //Delete adjustment records
            $balanceAdjustmentArray = PartyBalanceAfterAdjustment::where('party_payment_id', $paymentId)->get();
            if($balanceAdjustmentArray->count() > 0){
                foreach($balanceAdjustmentArray as $balanceAdjustment){
                    $paymentTransaction = PaymentTransaction::find($balanceAdjustment->payment_transaction_id);
                    $paymentTransaction->delete();
                }
            }


            /**
             * It will auto delete foreign keys
             *
             * model: PartyPaymentAllocation
             *
             * */
            $partyPayment->delete();

            DB::commit();

            return response()->json([
                'status'    => true,
                'message' => __('app.record_deleted_successfully'),
                'data'  => '',
            ]);

        } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }
    }
}
