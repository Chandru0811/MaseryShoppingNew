<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::all();
        return $this->success('Banners Retrived Successfully!', $banners);

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

        $image = $request->file('image');
        $publicPath = public_path("assets/banner/" . $title . "/" . $orderNumber);

        if (!file_exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true, true);
        }

        $imageName = $image->getClientOriginalName();
        $imageSize = $image->getSize();
        $imageExtension = $image->getClientOriginalExtension();
        $imageTitle = $title . '_' . $orderNumber;

        $image->move($publicPath, $imageName);

        $banner = Banner::create([
            'title' => $title,
            'image_title' => $imageTitle,
            'path' => "assets/banner/" . $title . "/" . $orderNumber . "/" . $imageName,
            'name' => $imageName,
            'extension' => $imageExtension,
            'size' => $imageSize,
            'order' => $orderNumber,
        ]);

        return $this->success('Banner Created Succesfully!', $banner);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return $this->error('Banner Not Found.', ['error' => 'Banner Not Found']);
        }
        return $this->success('Banner Retrived Succesfully!', $banner);
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

        $banner = Banner::find($id);
        if (!$banner) {
            return $this->error('Banner Not Found.', ['error' => 'Banner Not Found']);
        }

        if ($request->hasFile('image')) {
            $oldImagePath = public_path($banner->path);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $image = $request->file('image');
            $title = str_replace(' ', '_', $request->title);
            $orderNumber = $request->order;
            $publicPath = public_path("assets/banner/" . $title . "/" . $orderNumber);

            if (!file_exists($publicPath)) {
                File::makeDirectory($publicPath, 0777, true, true);
            }

            $imageName = $image->getClientOriginalName();
            $imageSize = $image->getSize();
            $imageExtension = $image->getClientOriginalExtension();
            $imageTitle = $title . '_' . $orderNumber;

            $image->move($publicPath, $imageName);

            $banner->update([
                'title' => $title,
                'image_title' => $imageTitle,
                'path' => "assets/banner/" . $title . "/" . $orderNumber . "/" . $imageName,
                'name' => $imageName,
                'extension' => $imageExtension,
                'size' => $imageSize,
                'order' => $orderNumber,
            ]);
        } else {
            $banner->update([
                'title' => $request->title,
                'order' => $request->order,
            ]);
        }

        return $this->success('Banner Updated Succesfully!', $banner);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return $this->error('Banner Not Found.', ['error' => 'Banner Not Found']);
        }

        $title = $banner->title;
        $orderNumber = $banner->order;

        $orderFolderPath = public_path("assets/banner/" . $title . "/" . $orderNumber);

        if (File::exists($orderFolderPath)) {
            File::deleteDirectory($orderFolderPath);
        }

        $titleFolderPath = public_path("assets/banner/" . $title);
        if (is_dir($titleFolderPath) && count(scandir($titleFolderPath)) == 2) {
            File::deleteDirectory($titleFolderPath);
        }

        $banner->delete();

        return response()->json([
            'message' => 'Banner Deleted Successfully!',
            'status' => 200
        ]);
    }
}
