<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategorySubGroupTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class CategorySubGroupTwoController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $categorySubGroupsTwo = CategorySubGroupTwo::all();
        return $this->success('Category Sub Groups Two Retrived Succesfully!', $categorySubGroupsTwo);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_sub_group_one_id' => 'required|exists:category_sub_groups_one,id',
            'name' => 'required|string|max:200|unique:category_sub_groups_two,name',
            'slug' => 'required|string|max:200|unique:category_sub_groups_two,slug',
        ], [
            'category_sub_group_one_id.required' => 'The category sub group one id field is required.',
            'category_sub_group_one_id.exists' => 'The selected category sub group one id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug field must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $categorySubGroupTwo = CategorySubGroupTwo::create($request->all());

        return $this->success('Category Sub Group Two Created Successfully!', $categorySubGroupTwo);
    }

    public function show(string $id)
    {
        $categorySubGroupTwo = CategorySubGroupTwo::with('category')->find($id);

        if (!$categorySubGroupTwo) {
            return $this->error('Category Sub Group Two Not Found.', ['error' => 'Category Sub Group Two Not Found']);
        }
        return $this->success('Category Sub Group Two Retrived Succesfully!', $categorySubGroupTwo);
    }

    public function update(Request $request, string $id)
    {
        $categorySubGroupTwo = CategorySubGroupTwo::find($id);

        if (!$categorySubGroupTwo) {
            return $this->error('Category Sub Group Two Not Found.', ['error' => 'Category Sub Group Two Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'category_sub_group_one_id' => 'required|exists:category_sub_groups_one,id',
            'name' => 'required|string|max:200|unique:category_sub_groups_two,name,' . $id,
            'slug' => 'required|string|max:200|unique:category_sub_groups_two,slug,' . $id,
            'description' => 'nullable|string',
        ], [
            'category_sub_group_one_id.required' => 'The category sub group one id field is required.',
            'category_sub_group_one_id.exists' => 'The selected category sub group one id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $categorySubGroupTwo->update($validatedData);

        return $this->success('Category Sub Group Two Updated Successfully!', $categorySubGroupTwo);
    }

    public function destroy($id)
    {
        $categorySubGroupTwo = CategorySubGroupTwo::find($id);

        if (!$categorySubGroupTwo) {
            return $this->error('Category Sub Group Two Not Found.', ['error' => 'Category Sub Group Two Not Found']);
        }

        $categorySubGroupTwo->delete();

        return $this->ok('Category Sub Group Two Deleted Successfully!');
    }

    public function restore($id)
    {
        $categorySubGroupTwo = CategorySubGroupTwo::onlyTrashed()->find($id);

        if (!$categorySubGroupTwo) {
            return $this->error('Category Sub Group Two Not Found.', ['error' => 'Category Sub Group Two Not Found']);
        }

        $categorySubGroupTwo->restore();

        return $this->success('Category Sub Group Two Restored Successfully!', $categorySubGroupTwo);
    }

}
