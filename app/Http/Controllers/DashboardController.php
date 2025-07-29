<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Order;
use App\Models\Sale\SaleOrder;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Customer;
use App\Models\OrderPayment;
use App\Models\OrderedProduct;
use App\Traits\FormatNumber;

use Illuminate\Support\Number;

use App\Models\Sale\Sale;
use App\Models\Sale\SaleReturn;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseReturn;
use App\Models\Party\Party;
use App\Models\Party\PartyTransaction;
use App\Models\Party\PartyPayment;
use App\Models\Expenses\Expense;
use App\Models\Items\ItemTransaction;
use App\Models\PaymentTransaction;


class DashboardController extends Controller
{
    use formatNumber;


    public function index()
    {

        $pendingSaleOrders          = SaleOrder::whereDoesntHave('sale')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->count();
        // Update the logic to count sales with invoice_status 'finished'
        $totalCompletedSaleOrders = Sale::where('invoice_status', 'finished')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->count();

        $partyBalance               = $this->paymentReceivables();
        $totalPaymentReceivables    = $this->formatWithPrecision($partyBalance['receivable']);

        $totalSuppliers = Party::where('party_type', 'supplier')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->count();

        $totalPaidAmountForFinishedInvoices = Sale::where('invoice_status', 'finished')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->sum('paid_amount');

        $totalPurchaseOrders = Purchase::when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
            return $query->where('created_by', auth()->user()->id);
        })
            ->count();

        $pendingPurchaseOrders          = PurchaseOrder::whereDoesntHave('purchase')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->count();

        $totalCompletedPurchaseOrders   = PurchaseOrder::whereHas('purchase')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->count();

        $pendingInvoices = Sale::where('invoice_status', 'pending')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->count();

        $totalCustomers       = Party::where('party_type', 'customer')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->count();

        $totalExpense = PaymentTransaction::where('transaction_type', 'Purchase')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($q) {
                return $q->where('created_by', auth()->user()->id);
            })
            ->sum('amount');

        // format with your precision helper
        $totalExpense = $this->formatWithPrecision($totalExpense);

        $recentInvoices       = Sale::when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
            return $query->where('created_by', auth()->user()->id);
        })
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $saleVsPurchase       = $this->saleVsPurchase();
        $trendingItems        = $this->trendingItems();


        return view('dashboard', compact(
            'pendingSaleOrders',
            'pendingPurchaseOrders',

            'totalCompletedSaleOrders',
            'totalCompletedPurchaseOrders',

            'totalCustomers',
            'totalPaymentReceivables',
            'totalSuppliers',
            'totalExpense',

            'saleVsPurchase',
            'trendingItems',
            'recentInvoices',
            'pendingInvoices',
            'totalPaidAmountForFinishedInvoices',
            'totalPurchaseOrders',
            'totalPaidAmountForFinishedInvoices'
        ));
    }

    public function saleVsPurchase()
    {
        $labels = [];
        $sales = [];
        $purchases = [];

        $now = now();
        for ($i = 0; $i < 6; $i++) {
            $month = $now->copy()->subMonths($i)->format('M Y');
            $labels[] = $month;

            // Get value for this month, e.g. from database
            $sales[] = Sale::whereMonth('sale_date', $now->copy()->subMonths($i)->month)
                ->whereYear('sale_date', $now->copy()->subMonths($i)->year)
                ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                    return $query->where('created_by', auth()->user()->id);
                })
                ->count();

            $purchases[] = Purchase::whereMonth('purchase_date', $now->copy()->subMonths($i)->month)
                ->whereYear('purchase_date', $now->copy()->subMonths($i)->year)
                ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                    return $query->where('created_by', auth()->user()->id);
                })
                ->count();
        }

        $labels = array_reverse($labels);
        $sales = array_reverse($sales);
        $purchases = array_reverse($purchases);

        $saleVsPurchase = [];

        for ($i = 0; $i < count($labels); $i++) {
            $saleVsPurchase[] = [
                'label'     => $labels[$i],
                'sales'     => $sales[$i],
                'purchases' => $purchases[$i],
            ];
        }

        return $saleVsPurchase;
    }

    public function trendingItems(): array
    {
        // Get top 4 trending items (adjust limit as needed)
        return ItemTransaction::query()
            ->select([
                'items.name',
                DB::raw('SUM(item_transactions.quantity) as total_quantity')
            ])
            ->join('items', 'items.id', '=', 'item_transactions.item_id')
            ->where('item_transactions.transaction_type', getMorphedModelName(Sale::class))
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('item_transactions.created_by', auth()->user()->id);
            })
            ->groupBy('item_transactions.item_id', 'items.name')
            ->orderByDesc('total_quantity')
            ->limit(4)
            ->get()
            ->toArray();
    }


    public function paymentReceivables()
    {
        // Calculate opening receivable (amounts owed to the company)
        $openingReceivable = PartyTransaction::whereIn('transaction_id', Party::select('id')->where('party_type', 'customer')->pluck('id'))
            ->where('transaction_type', 'Party Opening')
            ->selectRaw('COALESCE(SUM(to_receive) - SUM(to_pay), 0) as opening_payable')
            ->where('created_by', 3)
            ->first()
            ->opening_payable ?? 0;

        $openingPayable = PartyTransaction::whereIn('transaction_id', Party::select('id')->where('party_type', 'supplier')->pluck('id'))
            ->where('transaction_type', 'Party Opening')
            ->selectRaw('COALESCE(SUM(to_pay) - SUM(to_receive), 0) as opening_payable')
            ->where('created_by', 3)
            ->first()
            ->opening_payable ?? 0;

        // Total payments received from customers (Sale Adjustments)
        $partyPaymentReceiveSum = PartyPayment::where('payment_direction', 'receive')
            ->selectRaw('
                (SELECT SUM(amount) FROM party_payments
                WHERE payment_direction = \'receive\'' .  // Fix: Use single quotes around 'receive'
                (auth()->user()->can('dashboard.can.view.self.dashboard.details.only') ? ' AND created_by = ' . auth()->user()->id : '') . ')
                -
                COALESCE(
                    (SELECT SUM(payment_transactions.amount)
                    FROM payment_transactions
                    INNER JOIN party_payment_allocations
                        ON payment_transactions.id = party_payment_allocations.payment_transaction_id
                    WHERE party_payment_allocations.party_payment_id IN
                        (SELECT id FROM party_payments
                        WHERE payment_direction = \'receive\'' . // Fix: Use single quotes around 'receive'
                (auth()->user()->can('dashboard.can.view.self.dashboard.details.only') ? ' AND created_by = ' . auth()->user()->id : '') . ')
                    ), 0)
                AS total_amount')
            ->value('total_amount') ?? 0;

        // Total payments made to suppliers (Purchase Adjustments)
        $partyPaymentPaySum = PartyPayment::where('payment_direction', 'pay')
            ->selectRaw('
                (SELECT SUM(amount) FROM party_payments
                WHERE payment_direction = \'pay\'' .  // Fix: Use single quotes around 'pay'
                (auth()->user()->can('dashboard.can.view.self.dashboard.details.only') ? ' AND created_by = ' . auth()->user()->id : '') . ')
                -
                COALESCE(
                    (SELECT SUM(payment_transactions.amount)
                    FROM payment_transactions
                    INNER JOIN party_payment_allocations
                        ON payment_transactions.id = party_payment_allocations.payment_transaction_id
                    WHERE party_payment_allocations.party_payment_id IN
                        (SELECT id FROM party_payments
                        WHERE payment_direction = \'pay\'' . // Fix: Use single quotes around 'pay'
                (auth()->user()->can('dashboard.can.view.self.dashboard.details.only') ? ' AND created_by = ' . auth()->user()->id : '') . ')
                    ), 0)
                AS total_amount')
            ->value('total_amount') ?? 0;

        // Outstanding sale balance (grand_total - paid_amount)
        $saleBalance = Sale::selectRaw('COALESCE(SUM(grand_total - paid_amount), 0) as total')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->value('total');

        // Outstanding sale return balance
        $saleReturnBalance = SaleReturn::selectRaw('COALESCE(SUM(grand_total - paid_amount), 0) as total')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->value('total');

        // Outstanding purchase balance with shipping charge adjustment
        $purchaseBalance = Purchase::selectRaw('COALESCE(SUM((grand_total - shipping_charge) - 
                                    CASE
                                        WHEN paid_amount >= (grand_total - shipping_charge)
                                        THEN paid_amount - shipping_charge
                                        ELSE paid_amount
                                    END), 0) as total')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->value('total');

        // Outstanding purchase return balance
        $purchaseReturnBalance = PurchaseReturn::selectRaw('COALESCE(SUM(grand_total - paid_amount), 0) as total')
            ->when(auth()->user()->can('dashboard.can.view.self.dashboard.details.only'), function ($query) {
                return $query->where('created_by', auth()->user()->id);
            })
            ->value('total');

        // Calculate total receivable (amounts owed to the company)
        $partyReceivable = $openingReceivable + $saleBalance - $saleReturnBalance - $partyPaymentReceiveSum;

        // Calculate total payable (amounts the company owes, now including opening balance payable)
        $partyPayable = $openingPayable + $purchaseBalance - $purchaseReturnBalance - $partyPaymentPaySum;

        // Return non-negative values for display
        return [
            'payable' => abs($partyPayable),
            'receivable' => abs($partyReceivable),
        ];
    }
}
