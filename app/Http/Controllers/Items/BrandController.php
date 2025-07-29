<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Items\Brand;
use App\Http\Requests\BrandRequest;

class BrandController extends Controller
{

    /**
     * Create a new brand.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View  {

        return view('items.brand.create');

    }

    /**
     * List the brand
     *
     * @return \Illuminate\View\View
     */
    public function list() : View {
        return view('items.brand.list');
    }

     /**
     * Edit a items.
     *
     * @param int $id The ID of the item to edit.
     * @return \Illuminate\View\View
     */
    public function edit($id) : View {

        $brand = Brand::find($id);

        return view('items.brand.edit', compact('brand'));
    }

    /**
     * Return JsonResponse
     * */
    public function store(BrandRequest $request) : JsonResponse  {

        $filename = null;

        // Get the validated data from the brandRequest
        $validatedData = $request->validated();

        // Create a new brand record using Eloquent and save it
        $newCategory = Brand::create($validatedData);

        return response()->json([
            'status'    => true,
            'message' => __('app.record_saved_successfully'),
            'data'  => [
                'id' => $newCategory->id,
                'name' => $newCategory->name,
            ]
        ]);
    }

    /**
     * Return JsonResponse
     * */
    public function update(BrandRequest $request) : JsonResponse {
        $validatedData = $request->validated();

        // Save the brand details
        Brand::where('id', $validatedData['id'])->update($validatedData);

        return response()->json([
            'message' => __('app.record_updated_successfully'),
        ]);
    }

    public function datatableList(Request $request){

        $data = Brand::with('user');

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

                            $editUrl = route('item.brand.edit', ['id' => $id]);
                            $deleteUrl = route('item.brand.delete', ['id' => $id]);


                            $actionBtn = '<div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded font-22 text-option"></i>
                            </a>
                            <ul class="dropdown-menu">';
                                $actionBtn .= '<li>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="bi bi-trash"></i><i class="bx bx-edit"></i> '.__('app.edit').'</a>
                                </li>';
                                $actionBtn .= '<li>
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
            $record = Brand::find($recordId);
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

            Brand::whereIn('id', $selectedRecordIds)->delete();

            return response()->json([
                'status'    => true,
                'message' => __('app.record_deleted_successfully'),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {

                return response()->json([
                    'status'    => false,
                    'message' => __('app.cannot_delete_records'),
                ],409);

        }
    }


    function getAjaxSearchBarList(){
        $search = request('search');

        $items = Brand::where(function($query) use ($search) {
                        $query->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($search) . '%']);
                    })
                    ->select('id', 'name')
                    ->get();

        $response = [
            'results' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name,
                ];
            })->toArray(),
        ];
        return json_encode($response);
    }


}
