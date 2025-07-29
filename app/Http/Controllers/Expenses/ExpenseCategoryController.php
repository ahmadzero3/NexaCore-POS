<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Expenses\ExpenseCategory;
use App\Http\Requests\ExpenseCategoryRequest;
use App\Models\Accounts\Account;

class ExpenseCategoryController extends Controller
{

    /**
     * Create a new category.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View  {

        return view('expenses.category.create');

    }

    /**
     * List the Accounts
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('expenses.category.list');
    }

     /**
     * Edit a expenses.
     *
     * @param int $id The ID of the expense to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $expense = ExpenseCategory::find($id);

        return view('expenses.category.edit', compact('expense'));
    }

    /**
     * Return JsonResponse
     * */
    public function store(ExpenseCategoryRequest $request) : JsonResponse  {

        $filename = null;

        // Get the validated data from the categoryRequest
        $validatedData = $request->validated();

        // Create a new category record using Eloquent and save it
        $newExpense = ExpenseCategory::create($validatedData);

        /**
         * Create Account
         * */
        if($newExpense){
            Account::create([
                'name'  => $validatedData['name'],
                'group_id'  => $validatedData['account_group_id'],
                'description'  => $validatedData['description'],
                'expense_category_id'  => $newExpense->id,
                'is_deletable'  => 0,
            ]);
        }

        return response()->json([
            'status'    => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $newExpense->id,
                'name' => $newExpense->name,
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(ExpenseCategoryRequest $request) : JsonResponse {
        $validatedData = $request->validated();

        // Save the category details
        $updateExpense = ExpenseCategory::where('id', $validatedData['id'])->update($validatedData);

        /**
         * Create Account
         * */
        if($updateExpense){
            Account::where('expense_category_id', $validatedData['id'])
                    ->update([
                            'name'  => $validatedData['name'],
                            'group_id'  => $validatedData['account_group_id'],
                            'description'  => $validatedData['description'],
                        ]);
        }

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function datatableList(Request $request){

        $data = ExpenseCategory::with('user');

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username??'';
                    })
                    ->addColumn('group_name', function ($row) {
                        return $row->group->name;
                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('expense.category.edit', ['id' => $id]);
                            $deleteUrl = route('expense.category.delete', ['id' => $id]);


                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="bi bi-trash"></i><i class="bx bx-edit"></i> '.__('app.edit').'</a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger deleteRequest" data-delete-id='.$id.'><i class="bx bx-trash"></i> '.__('app.delete').'</button>
                                </li>
                            </ul>
                        </div>';
                            return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    public function delete(Request $request) : JsonResponse{

        $selectedRecordIds = $request->input('record_ids');

        // Perform validation for each selected record ID
        foreach ($selectedRecordIds as $recordId) {
            $record = ExpenseCategory::find($recordId);
            if (!$record) {
                // Invalid record ID, handle the error (e.g., show a message, log, etc.)
                return response()->json([
                    'status'    => false,
                    'message' => __('app.invalid_record_id',['record_id' => $recordId]),
                ]);

            }
            // You can perform additional validation checks here if needed before deletion
        }

        /**
         * All selected record IDs are valid, proceed with the deletion
         * Delete all records with the selected IDs in one query
         * */


        try {
            // Attempt deletion (as in previous responses)
            ExpenseCategory::whereIn('id', $selectedRecordIds)->delete();
            return response()->json([
                'status'    => true,
                'message' => __('app.record_deleted_successfully'),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'status'    => false,
                    'message' => __('app.cannot_delete_records'),
                ],409);
            }
        }
    }
}
