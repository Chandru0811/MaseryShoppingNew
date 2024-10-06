<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class CategoryController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $categories = Category::all();
        return $this->success('Categories Retrived Succesfully!', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_sub_group_two_id' => 'required|exists:category_sub_groups_two,id',
            'name' => 'required|string|max:200|unique:categories,name',
            'slug' => 'required|string|max:200|unique:categories,slug',
            'featured' => 'nullable|boolean'
        ], [
            'category_sub_group_two_id.required' => 'The category sub group two id field is required.',
            'category_sub_group_two_id.exists' => 'The selected category sub group two id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug field must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $category = Category::create($request->all());

        return $this->success('Category Created Successfully!', $category);
    }

    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }
        return $this->success('Category Retrived Succesfully!', $category);
    }

    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'category_sub_group_two_id' => 'required|exists:category_sub_groups_two,id',
            'name' => 'required|string|max:200|unique:categories,name,' . $id,
            'slug' => 'required|string|max:200|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'featured' => 'nullable|boolean'
        ], [
            'category_sub_group_two_id.required' => 'The category sub group two id field is required.',
            'category_sub_group_two_id.exists' => 'The selected category sub group two id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $category->update($validatedData);

        return $this->success('Category Updated Successfully!', $category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }

        $category->delete();

        return $this->ok('Category Deleted Successfully!');
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->find($id);

        if (!$category) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }
        
        $category->restore();

        return $this->success('Category Restored Successfully!', $category);
    }

}