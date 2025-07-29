<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Expenses\ExpenseSubcategory;
use App\Http\Requests\ExpenseSubcategoryRequest;

class ExpenseSubcategoryController extends Controller
{

    /**
     * Create a new subcategory.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View  {

        return view('expenses.subcategory.create');

    }

    /**
     * List the Accounts
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('expenses.subcategory.list');
    }

     /**
     * Edit a expenses.
     *
     * @param int $id The ID of the expense to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $expense = ExpenseSubcategory::find($id);

        return view('expenses.subcategory.edit', compact('expense'));
    }

    /**
     * Return JsonResponse
     * */
    public function store(ExpenseSubcategoryRequest $request) : JsonResponse  {

        $filename = null;

        // Get the validated data from the subcategoryRequest
        $validatedData = $request->validated();

        // Create a new subcategory record using Eloquent and save it
        $newExpense = ExpenseSubcategory::create($validatedData);

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
    public function update(ExpenseSubcategoryRequest $request) : JsonResponse {
        $validatedData = $request->validated();

        // Save the subcategory details
        $updateExpense = ExpenseSubcategory::where('id', $validatedData['id'])->update($validatedData);

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function datatableList(Request $request){

        $data = ExpenseSubcategory::with('user');

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username??'';
                    })

                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('expense.subcategory.edit', ['id' => $id]);
                            $deleteUrl = route('expense.subcategory.delete', ['id' => $id]);


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
            $record = ExpenseSubcategory::find($recordId);
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
            ExpenseSubcategory::whereIn('id', $selectedRecordIds)->delete();
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
