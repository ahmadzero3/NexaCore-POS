<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Requests\TaxRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;

use App\Models\Tax;

class TaxController extends Controller
{
    /**
     * Create a new tax.
     *
     * @return \Illuminate\View\View
     */
    public function create() : View {
        return view('tax.create');
    }

    /**
     * Edit a tax.
     *
     * @param int $id The ID of the tax to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $tax = Tax::find($id);

        return view('tax.edit', compact('tax'));
    }
    /**
     * Return JsonResponse
     * */
    public function store(TaxRequest $request)  {

        $filename = null;

        // Get the validated data from the TaxRequest
        $validatedData = $request->validated();

        // Create a new tax record using Eloquent and save it
        $newTax = Tax::create($validatedData);

        return response()->json([
            'status'  => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $newTax->id,
                'name' => $newTax->name,
                'rate' => $newTax->rate,
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(TaxRequest $request) : JsonResponse {
        $validatedData = $request->validated();

        // Save the tax details
        $settings = Tax::find($validatedData['id']);
        $settings->name         = $validatedData['name'];
        $settings->rate         = $validatedData['rate'];
        $settings->status       = $validatedData['status'];
     
        $settings->save();

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function list() : View {
        return view('tax.list');
    }

    public function datatableList(Request $request){
        
        $data = Tax::query();

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

                            $editUrl = route('tax.edit', ['id' => $id]);
                            $deleteUrl = route('tax.delete', ['id' => $id]);


                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">';
                                $actionBtn .= '<li>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="bi bi-trash"></i><i class="bx bx-edit"></i> '.__('app.edit').'</a>
                                </li>';
                                $actionBtn .= ($row->is_deletable==0)? '' : '<li>
                                    <button type="button" class="dropdown-item text-danger deleteRequest " data-delete-id='.$id.'><i class="bx bx-trash"></i> '.__('app.delete').'</button>
                                </li>';
                            $actionBtn .= '</ul>
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
            $record = Tax::find($recordId);
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
            Tax::whereIn('id', $selectedRecordIds)->where('is_deletable', 1)
                ->chunk(100, function ($taxes) {
                    foreach ($taxes as $tax) {
                        $tax->delete();
                    }
                });
        }catch (QueryException $e){
            return response()->json(['message' => __('app.cannot_delete_records')], 409);
        }


        return response()->json([
            'status'    => true,
            'message' => __('app.record_deleted_successfully'),
        ]);
    }
}
