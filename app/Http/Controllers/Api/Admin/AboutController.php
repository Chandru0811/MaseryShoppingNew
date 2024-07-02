<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Models\About;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


class AboutController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $about = About::all();
        return $this->success('About Retrived Succesfully!', $about);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'order' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'title.required' => 'The title field is required.',
            'order.required' => 'The order field is required.',
            'image.required' => 'The image field is required.',
            'image.image' => 'The image field must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $title = str_replace(' ', '_', $request->title);
        $orderNumber = $request->order;
        $description = $request->description;


        $image = $request->file('image');
        $publicPath = public_path("assets/about/" . $title . "/" . $orderNumber);

        if (!file_exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true, true);
        }

        $imageName = $image->getClientOriginalName();
        $imageSize = $image->getSize();
        $imageExtension = $image->getClientOriginalExtension();


        $image->move($publicPath, $imageName);

        $about = About::create([
            'title' => $title,
            'description'=>$description,
            'path' => "assets/about/" . $title . "/" . $orderNumber . "/" . $imageName,
            'name' => $imageName,
            'extension' => $imageExtension,
            'size' => $imageSize,
            'order' => $orderNumber,
        ]);

        return $this->success('About Created Succesfully!', $about);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $about = About::find($id);
        if (!$about) {
            return $this->error('About Not Found.', ['error' => 'About Not Found']);
        }
        return $this->success('About Retrived Succesfully!', $about);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'order' => 'required|integer',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'title.required' => 'The title field is required.',
            'order.required' => 'The order field is required.',
            'image.image' => 'The image field must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $about = About::find($id);
        if (!$about) {
            return $this->error('About Not Found.', ['error' => 'About Not Found']);
        }

        if ($request->hasFile('image')) {
            $oldImagePath = public_path($about->path);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $image = $request->file('image');
            $title = str_replace(' ', '_', $request->title);
            $orderNumber = $request->order;
            $publicPath = public_path("assets/about/" . $title . "/" . $orderNumber);

            if (!file_exists($publicPath)) {
                File::makeDirectory($publicPath, 0777, true, true);
            }

            $imageName = $image->getClientOriginalName();
            $imageSize = $image->getSize();
            $imageExtension = $image->getClientOriginalExtension();

            $image->move($publicPath, $imageName);

            $about->update([
                'title' => $title,
                'path' => "assets/about/" . $title . "/" . $orderNumber . "/" . $imageName,
                'name' => $imageName,
                'extension' => $imageExtension,
                'size' => $imageSize,
                'order' => $orderNumber,
                'description' => $request->description,
            ]);
        } else {
            $about->update([
                'title' => $request->title,
                'order' => $request->order,
                'description' => $request->description,
            ]);
        }

        return $this->success('About Updated Successfully!', $about);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $about = About::find($id);
        if (!$about) {
            return $this->error('About Not Found.', ['error' => 'About Not Found']);
        }

        $title =$about->title;
        $orderNumber =$about->order;

        $orderFolderPath = public_path("assets/about/" . $title . "/" . $orderNumber);

        if (File::exists($orderFolderPath)) {
            File::deleteDirectory($orderFolderPath);
        }

        $titleFolderPath = public_path("assets/about/" . $title);
        if (is_dir($titleFolderPath) && count(scandir($titleFolderPath)) == 2) {
            File::deleteDirectory($titleFolderPath);
        }

       $about->delete();

        return response()->json([
            'message' => 'About Deleted Successfully!',
            'status' => 200
        ]);
    }
}
