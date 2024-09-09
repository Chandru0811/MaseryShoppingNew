<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class SubcategoryController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $subcategories = Subcategory::with('category')->get();
        return $this->success('Subcategories Retrieved Successfully!', $subcategories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:sub_categories,name',
            'slug' => 'required|unique:sub_categories,slug',
            'category_id' => 'required|exists:categories,id',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category does not exist.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', ['errors' => $validator->errors()]);
        }

        $subcategory = Subcategory::create($request->all());
        return $this->success('Subcategory Created Successfully!', $subcategory);
    }

    public function show(string $id)
    {
        $subcategory = Subcategory::with('category')->find($id);
        if (!$subcategory) {
            return $this->error('Subcategory Not Found.', ['error' => 'Subcategory Not Found'], 404);
        }
        return $this->success('Subcategory Retrieved Successfully!', $subcategory);
    }

    public function update(Request $request, string $id)
    {
        $subcategory = Subcategory::find($id);
        if (!$subcategory) {
            return $this->error('Subcategory Not Found.', ['error' => 'Subcategory Not Found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:sub_categories,name,' . $id,
            'slug' => 'required|unique:sub_categories,slug,' . $id,
            'category_id' => 'required|exists:categories,id',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category does not exist.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', ['errors' => $validator->errors()]);
        }

        $subcategory->update($request->all());
        return $this->success('Subcategory Updated Successfully!', $subcategory);
    }

    public function destroy(string $id)
    {
        $subcategory = Subcategory::find($id);
        if (!$subcategory) {
            return $this->error('Subcategory Not Found.', ['error' => 'Subcategory Not Found'], 404);
        }
        $subcategory->delete();
        return $this->ok('Subcategory Deleted Successfully!');
    }
}
