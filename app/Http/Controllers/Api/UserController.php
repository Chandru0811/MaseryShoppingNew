<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Models\Banner;
use App\Models\About;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponses;

    public function getbanner()
    {
        $banners = Banner::all();
        return $this->success('Banners Retrived Successfully!', $banners);
    }
    
    public function sitedetails()
    {
        $about = About::orderBy('order', 'asc')->get();
        return $this->success('AboutDetails Retrived Successfully!', $about);
    }
    
    public function contact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required'
        ], [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field must be a valid email address.',
            'phone.required' => 'The phone field is required.'
        ]);

        if($validator->fails()){
            return $this->error('Validation Error.', ['errors'=>$validator->errors()]);
        }

        $contactUs = Contact::create($request->all());
        return $this->ok('Thank You for ContactUs!');
    }
}
