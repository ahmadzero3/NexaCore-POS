<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Requests\UnitRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

use App\Models\Unit;


class UnitController extends Controller
{
    /**
     * Create a new unit.
     *
     * @return \Illuminate\View\View
     */
    public function create() : View {
        return view('unit.create');
    }

    /**
     * Edit a unit.
     *
     * @param int $id The ID of the unit to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $unit = Unit::find($id);

        return view('unit.edit', compact('unit'));
    }
    /**
     * Return JsonResponse
     * */
    public function store(UnitRequest $request) : JsonResponse {

        $filename = null;

        // Get the validated data from the UnitRequest
        $validatedData = $request->validated();
        
        // Create a new tax record using Eloquent and save it
        $newPaymentType = Unit::create($validatedData);

        return response()->json([
            'status'  => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $newPaymentType->id,
                'name' => $newPaymentType->name,
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(UnitRequest $request) : JsonResponse {
        $validatedData = $request->validated();

        // Save the tax details
        $unit = Unit::findOrFail($validatedData['id']);
        $unit->fill($validatedData);
        $unit->save(); // This will trigger the 'updated' event

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function list() : View {
        return view('unit.list');
    }

    public function datatableList(Request $request){
        
        $data = Unit::query();

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

                            $editUrl = route('unit.edit', ['id' => $id]);
                            $deleteUrl = route('unit.delete', ['id' => $id]);


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
            $record = Unit::find($recordId);
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
            Unit::whereIn('id', $selectedRecordIds)->where('is_deletable', 1)
                ->chunk(100, function ($units) {
                    foreach ($units as $unit) {
                        $unit->delete();
                    }
                });

        }catch (QueryException $e){
            return response()->json(['message' => __('app.cannot_delete_records')], 422);
        }


        return response()->json([
            'status'    => true,
            'message' => __('app.record_deleted_successfully'),
        ]);
    }

}
