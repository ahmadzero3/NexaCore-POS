<?php

namespace App\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Accounts\AccountGroup;
use App\Http\Requests\AccountGroupRequest;

class AccountGroupController extends Controller
{
    /**
     * Create a new service.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View  {

        return view('accounts.group.create');

    }

    /**
     * List the Accounts
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('accounts.group.list');
    }

     /**
     * Edit a accounts.
     *
     * @param int $id The ID of the account to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $group = AccountGroup::find($id);

        return view('accounts.group.edit', compact('group'));
    }

    /**
     * Return JsonResponse
     * */
    public function store(AccountGroupRequest $request) : JsonResponse  {

        $filename = null;

        // Get the validated data from the ServiceRequest
        $validatedData = $request->validated();

        // Create a new service record using Eloquent and save it
        $newGroup = AccountGroup::create($validatedData);

        return response()->json([
            'status'    => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $newGroup->id,
                'name' => $newGroup->name,
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(AccountGroupRequest $request) : JsonResponse {
        $validatedData = $request->validated();

        // Save the service details
        AccountGroup::where('id', $validatedData['id'])->update($validatedData);

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function datatableList(Request $request){

        $data = AccountGroup::with('user');

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at->format(app('company')['date_format']);
                    })
                    ->addColumn('parent_name', function ($row) {
                        return ($row->parent_id) ? $row->parent->name : 'Main';
                    })
                    ->addColumn('username', function ($row) {
                        return $row->user->username??'';
                    })
                    ->addColumn('action', function($row){
                            $id = $row->id;

                            $editUrl = route('account.group.edit', ['id' => $id]);
                            $deleteUrl = route('account.group.delete', ['id' => $id]);


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
            $record = AccountGroup::find($recordId);
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
            AccountGroup::whereIn('id', $selectedRecordIds)->delete();
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
