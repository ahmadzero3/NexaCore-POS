<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\CashAdjustment;
use Illuminate\Support\Facades\DB;
use App\Models\Sale\Sale;
use App\Models\Expenses\Expense;
use Barryvdh\DomPDF\Facade\Pdf;

class CloseCashController extends Controller
{
    public function close(Request $request)
    {
        return response()->json(['status' => 'success', 'message' => 'Cash closed successfully']);
    }

    public function showCloseCashForm()
    {
        $today = date('Y-m-d');
        $userId = auth()->id(); // Get logged-in user ID

        // Filter all queries by the authenticated user
        $openingBalance = CashAdjustment::where('adjustment_type', 'Cash Increase')
            ->where('created_by', $userId) // Add user filter
            ->whereDate('created_at', $today)
            ->sum('amount');

        $todayIncome = Sale::where('created_by', $userId) // Add user filter
            ->whereDate('created_at', $today)
            ->sum('paid_amount');

        $totalIncome = $openingBalance + $todayIncome;

        $todayExpenses = Expense::where('created_by', $userId) // Add user filter
            ->whereDate('created_at', $today)
            ->sum('paid_amount');

        return view('transaction.close-cash', compact('openingBalance', 'todayIncome', 'totalIncome', 'todayExpenses'));
    }

    public function printReceipt()
    {
        $today = date('Y-m-d');

        // Fetch data for Card Three table
        $openingBalance = CashAdjustment::where('adjustment_type', 'Cash Increase')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $todayIncome = Sale::whereDate('created_at', $today)->sum('paid_amount');
        $totalIncome = $openingBalance + $todayIncome;
        $todayExpenses = Expense::whereDate('created_at', $today)->sum('paid_amount');
        $closingBalance = $totalIncome - $todayExpenses;

        $data = [
            'openingBalance' => $openingBalance,
            'todayIncome' => $todayIncome,
            'totalIncome' => $totalIncome,
            'todayExpenses' => $todayExpenses,
            'closingBalance' => $closingBalance,
        ];

        return view('transaction.close-cash-print.close-cash-print', compact('data'));
    }

    public function showApplyCloseCashPrint()
    {
        $today = date('Y-m-d');

        $openingBalance = CashAdjustment::where('adjustment_type', 'Cash Increase')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $todayIncome = Sale::whereDate('created_at', $today)->sum('paid_amount');
        $totalIncome = $openingBalance + $todayIncome;
        $todayExpenses = Expense::whereDate('created_at', $today)->sum('paid_amount');
        $closingBalance = $totalIncome - $todayExpenses;

        // Fetch the user name based on created_by
        $userId   = DB::table('close_cash')->latest()->value('created_by');
        $userName = DB::table('users')->where('id', $userId)->value('username');

        $data = [
            'openingBalance' => $openingBalance,
            'todayIncome' => $todayIncome,
            'totalIncome' => $totalIncome,
            'todayExpenses' => $todayExpenses,
            'closingBalance' => $closingBalance,
            'userName' => $userName, // Pass the user name to the view
        ];

        return view('transaction.apply-close-cash-print.apply-close-cash-print', compact('data'));
    }

    public function insertCloseCashData(Request $request)
    {
        try {
            // Validate incoming request data
            $data = $request->validate([
                'opening_balance' => 'required|numeric',
                'today_income' => 'required|numeric',
                'total_income' => 'required|numeric',
                'today_expenses' => 'required|numeric',
                'balance' => 'required|numeric',
                'created_by' => 'required|exists:users,id', // Expecting created_by from request, validated
            ]);

            // Add current timestamps
            // Use 'created_by' instead of 'user_id' to match the database column
            $data['created_by'] = auth()->id(); // Override or ensure created_by is the logged-in user
            $data['created_at'] = now();
            $data['updated_at'] = now();

            // Insert data into the 'close_cash' table
            DB::table('close_cash')->insert($data);

            // Return success response
            return response()->json(['status' => 'success', 'message' => 'Data inserted successfully']);
        } catch (\Exception $e) {
            // Log the error for debugging (optional but recommended)
            // \Log::error('Close Cash Insert Error: ' . $e->getMessage());

            // Return error response
            return response()->json(['status' => 'error', 'message' => 'An error occurred while saving the data.'], 500);
        }
    }

    public function listCloseCash()
    {
        $closeCashData = DB::table('close_cash')
            ->where('created_by', auth()->id()) // Filter by current user
            ->get();

        return view('transaction.list-close-cash', compact('closeCashData'));
    }

    public function datatableList(Request $request)
    {
        $query = DB::table('close_cash')
            ->join('users', 'close_cash.created_by', '=', 'users.id')
            ->select([
                'close_cash.id',
                'close_cash.opening_balance',
                'close_cash.today_income',
                'close_cash.total_income',
                'close_cash.today_expenses',
                'close_cash.balance',
                'users.username as created_by',
                'close_cash.created_at',
                'close_cash.updated_at',
            ]);

        if ($request->filled('from_date')) {
            $query->whereDate('close_cash.created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('close_cash.created_at', '<=', $request->to_date);
        }

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $id = $row->id;

                $actionBtn  = '<div class="dropdown ms-auto">';
                $actionBtn .=   '<a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">';
                $actionBtn .=     '<i class="bx bx-dots-vertical-rounded font-22 text-option"></i>';
                $actionBtn .=   '</a>';
                $actionBtn .=   '<ul class="dropdown-menu">';

                $actionBtn .= '<a href="' . route('close-cash.edit', $id) . '" class="dropdown-item">'
                    . '<i class="bx bx-edit"></i> ' . __('app.edit') . '</a>';

                $actionBtn .= '<a class="dropdown-item" href="' . route('close-cash.details', $id) . '"><i class="bx bx-show-alt"></i> ' . __('app.details') . '</a>';

                // Updated Print button
                $actionBtn .= '<a target="_blank" class="dropdown-item" href="' . route('close-cash.print', $id) . '"><i class="bx bx-printer"></i> ' . __('app.print') . '</a>';

                // Updated PDF button
                $actionBtn .= '<a target="_blank" class="dropdown-item" href="' . route('close-cash.print', ['id' => $id, 'type' => 'pdf']) . '"><i class="bx bxs-file-pdf"></i> ' . __('app.pdf') . '</a>';

                // Delete link
                $actionBtn .=     '<li>';
                $actionBtn .=       '<a class="dropdown-item text-danger deleteRequest" '
                    . 'data-delete-id="' . $id . '">'
                    . '<i class="bx bx-trash"></i> ' . __('app.delete') .
                    '</a>';
                $actionBtn .=     '</li>';

                $actionBtn .=   '</ul>';
                $actionBtn .= '</div>';

                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function delete($id)
    {
        try {
            DB::table('close_cash')->where('id', $id)->delete();
            return response()->json(['status' => 'success', 'message' => __('app.record_deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $closeCash = DB::table('close_cash')->find($id);
        return view('transaction.edit-list-close-cash', compact('closeCash'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'opening_balance' => 'required|numeric',
            'today_income' => 'required|numeric',
            'total_income' => 'required|numeric',
            'today_expenses' => 'required|numeric',
            'balance' => 'required|numeric',
        ]);

        $affected = DB::table('close_cash')
            ->where('id', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $affected ? 'success' : 'error',
                'message' => $affected
                    ? __('app.record_updated_successfully')
                    : __('app.no_records_updated')
            ]);
        }

        return redirect()->route('close.cash.list')
            ->with(
                $affected ? 'success' : 'error',
                $affected ? __('app.record_updated_successfully') : __('app.no_records_updated')
            );
    }

    public function showCloseCashDetails($id)
    {
        $closeCash = DB::table('close_cash')
            ->join('users', 'close_cash.created_by', '=', 'users.id')
            ->select('close_cash.*', 'users.username as created_by_name')
            ->where('close_cash.id', $id)
            ->first();

        return view('print.list-close-cash.details', compact('closeCash'));
    }

    public function printCloseCash($id, $type = 'print')
    {
        $closeCash = DB::table('close_cash')
            ->join('users', 'close_cash.created_by', '=', 'users.id')
            ->select('close_cash.*', 'users.username as created_by_name')
            ->where('close_cash.id', $id)
            ->first();

        // For PDF generation
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('print.list-close-cash.print-list-close-cash', [
                'closeCash' => $closeCash,
                'formatNumber' => new class() {
                    public function formatWithPrecision($number, $precision = 2)
                    {
                        return number_format($number, $precision);
                    }
                    public function spell($number)
                    {
                        $nf = new \NumberFormatter(config('app.locale', 'en_US'), \NumberFormatter::SPELLOUT);
                        return ucwords($nf->format($number));
                    }
                },
                'appDirection' => config('app.direction', 'ltr'),
                'isPdf' => true
            ]);

            return $pdf->download("close-cash-{$id}.pdf");
        }

        return view('print.list-close-cash.print-list-close-cash', [
            'closeCash' => $closeCash,
            'isPdf' => ($type === 'pdf'), // Determine if PDF
            'formatNumber' => new class() {
                public function formatWithPrecision($number, $precision = 2)
                {
                    return number_format($number, $precision);
                }
                public function spell($number)
                {
                    $nf = new \NumberFormatter(config('app.locale', 'en_US'), \NumberFormatter::SPELLOUT);
                    return ucwords($nf->format($number));
                }
            },
            'appDirection' => config('app.direction', 'ltr')
        ]);
    }
}
