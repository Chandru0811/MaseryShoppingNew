<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategorySubGroupOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class CategorySubGroupOneController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $categorySubGroupsOne = CategorySubGroupOne::all();
        return $this->success('Category Sub Groups One Retrived Succesfully!', $categorySubGroupsOne);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_sub_group_id' => 'required|exists:category_sub_groups,id',
            'name' => 'required|string|max:200|unique:category_sub_groups_one,name',
            'slug' => 'required|string|max:200|unique:category_sub_groups_one,slug',
        ], [
            'category_group_id.required' => 'The category sub group id field is required.',
            'category_group_id.exists' => 'The selected category sub group id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug field must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $categorySubGroupOne = CategorySubGroupOne::create($request->all());

        return $this->success('Category Sub Group One Created Successfully!', $categorySubGroupOne);
    }

    public function show(string $id)
    {
        $categorySubGroupOne = CategorySubGroupOne::find($id);

        if (!$categorySubGroupOne) {
            return $this->error('Category Sub Group One Not Found.', ['error' => 'Category Sub Group One Not Found']);
        }
        return $this->success('Category Sub Group One Retrived Succesfully!', $categorySubGroupOne);
    }

    public function update(Request $request, string $id)
    {
        $categorySubGroupOne = CategorySubGroupOne::find($id);

        if (!$categorySubGroupOne) {
            return $this->error('Category Sub Group One Not Found.', ['error' => 'Category Sub Group One Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'category_sub_group_id' => 'required|exists:category_sub_groups,id',
            'name' => 'required|string|max:200|unique:category_sub_groups_one,name,' . $id,
            'slug' => 'required|string|max:200|unique:category_sub_groups_one,slug,' . $id,
            'description' => 'nullable|string',
        ], [
            'category_sub_group_id.required' => 'The category sub group id field is required.',
            'category_sub_group_id.exists' => 'The selected category sub group id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $categorySubGroupOne->update($validatedData);

        return $this->success('Category Sub Group One Updated Successfully!', $categorySubGroupOne);
    }

    public function destroy($id)
    {
        $categorySubGroupOne = CategorySubGroupOne::find($id);

        if (!$categorySubGroupOne) {
            return $this->error('Category Sub Group One Not Found.', ['error' => 'Category Sub Group One Not Found']);
        }

        $categorySubGroupOne->delete();

        return $this->ok('Category Sub Group One Deleted Successfully!');
    }

    public function restore($id)
    {
        $categorySubGroupOne = CategorySubGroupOne::onlyTrashed()->find($id);

        if (!$categorySubGroupOne) {
            return $this->error('Category Sub Group One Not Found.', ['error' => 'Category Sub Group One Not Found']);
        }
        
        $categorySubGroupOne->restore();

        return $this->success('Category Sub Group One Restored Successfully!', $categorySubGroupOne);
    }

}