<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponses;
use App\Models\Contact;

class AccountController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $customers = User::withCount(['orders'
        ])->get();

        return $this->success('Customer Retrived Successfully',$customers);
    }
    
    public function contactedUsers()
    {
        $contactUs = Contact::all();
        return $this->success('ContactUs Retrived Succesfully!',$contactUs);
    }
}
