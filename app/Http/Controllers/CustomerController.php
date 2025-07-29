<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Requests\CustomerRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;

use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Create a new customer.
     *
     * @return \Illuminate\View\View
     */
    public function create() : View {
        return view('customer.create');
    }

    /**
     * Edit a customer.
     *
     * @param int $id The ID of the customer to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $customer = Customer::find($id);

        return view('customer.edit', compact('customer'));
    }
    /**
     * Return JsonResponse
     * */
    public function store(CustomerRequest $request)  {

        $filename = null;

        // Get the validated data from the CustomerRequest
        $validatedData = $request->validated();

        // Create a new customer record using Eloquent and save it
        $newCustomer = Customer::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $newCustomer->id,
                'first_name' => $newCustomer->first_name,
                'last_name' => $newCustomer->last_name??'',
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(CustomerRequest $request) : JsonResponse {
        $validatedData = $request->validated();

        // Save the customer details
        $settings = Customer::find($validatedData['id']);
        $settings->first_name   = $validatedData['first_name'];
        $settings->last_name    = $validatedData['last_name'];
        $settings->email        = $validatedData['email'];
        $settings->mobile       = $validatedData['mobile'];
        $settings->whatsapp     = $validatedData['whatsapp'];
        $settings->address      = $validatedData['address'];
        $settings->status       = $validatedData['status'];
     
        $settings->save();

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function list() : View {
        return view('customer.list');
    }

    public function datatableList(Request $request){

        $data = Customer::query();


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

                            $editUrl = route('customer.edit', ['id' => $id]);
                            $deleteUrl = route('customer.delete', ['id' => $id]);


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
            $record = Customer::find($recordId);
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
        try{
            Customer::whereIn('id', $selectedRecordIds)->delete();
        }catch (QueryException $e){
            return response()->json(['message' => __('app.cannot_delete_records')], 409);
        }

        return response()->json([
            'status'    => true,
            'message' => __('app.record_deleted_successfully'),
        ]);
    }
}
