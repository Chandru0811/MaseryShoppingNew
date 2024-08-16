<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\File;
use App\Models\Header;
use App\Models\Footer;
use App\Models\ContactUs;

class HeaderFooterAndContactController extends Controller
{
    use ApiResponses;
   //Header

   public function edit_header()
   {
    $header = Header::first();
    return $this-> success('Header Details Retrieved Succcesfully!',$header);
   }

   public function update_header(Request $request)
   {
    $validator = $request->validate([
        'header_logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if($request->hasFile('header_logo_path')){
        $file = $request->file('header_logo_path');
        $filename = time(). '_' . $file->getClientOriginalName();
        $publicPath = 'assets/header_images/' . $filename;
        $logoSize = $file->getSize();

        if (!File::exists(public_path('assets/header_images/'))){
            File::makeDirectory(public_path('assets/header_images/'), 0755, true, true);
        }

        $file->move(public_path('assets/header_images/'), $filename);

        $validator['header_logo_name'] = $filename;
        $validator['header_logo_path'] =  $publicPath;
        $validator['header_logo_size'] = $logoSize;
        $validator['header_logo_extension'] = $file->getClientOriginalExtension();    
    }

    $header = Header::first();
    if ($header) {
        $header->fill($validator);
        $header->save();
    }else{
        Header::create($validator);
    }
    return $this->success('Header Details Saved Succcesfully!', $header);

   }

   public function publish_header(Request $request)
   {
       $header = Header::first();
       if (!$header) {
           return $this->error('Header values not found', 404);
       }

       $header->approved_header_logo_name = $header->header_logo_name;
       $header->approved_header_logo_path = $header->header_logo_path;
       $header->approved_header_logo_extension = $header->header_logo_extension;
       $header->approved_header_logo_size = $header->header_logo_size;
       $header->is_approved = true;
       $header->save();

       return $this->success('Header changes published successfully', $header);
   }

   //Footer
   public function edit_footer()
   {
    $footer = Footer::first();
    return $this-> success('Footer Details Retrieved Succcesfully!',$footer);
   }

   public function update_footer(Request $request)
   {
       $validator = $request->validate([
           'footer_logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
           'about_text' => 'nullable|string',
           'whatsapp_link' => 'nullable|string',
           'facebook_link' => 'nullable|string',
           'twitter_link' => 'nullable|string',
           'instagram_link' => 'nullable|string',
           'tiktok_link' => 'nullable|string',
           'linkedin_link' => 'nullable|string',
           'googleplay_link' => 'nullable|string',
           'appstore_link' => 'nullable|string',
           'mail' => 'nullable|string',
           'phone' => 'nullable|string',
           'address' => 'nullable|string',
           'copyrights' => 'nullable|string',
       ]);
       if ($request->hasFile('footer_logo_path')) {
           $file = $request->file('footer_logo_path');
           $filename = time() . '_' . $file->getClientOriginalName();
           $publicPath = 'assets/footer_images/' . $filename;
           $logoSize = $file->getSize();


           if (!File::exists(public_path('assets/footer_images'))) {
               File::makeDirectory(public_path('assets/footer_images'), 0755, true, true);
           }

           $file->move(public_path('assets/footer_images'), $filename);

           $validator['footer_logo_path'] = $publicPath;
           $validator['footer_logo_extension'] = $file->getClientOriginalExtension();
           $validator['footer_logo_name'] = $filename;
           $validator['footer_logo_size'] = $logoSize;
       }

       $footer = Footer::first();
       if ($footer) {
           $footer->fill($validator);
           $footer->save();
       } else {
           Footer::create($validator);
       }

       return $this->success('Footer saved successfully', $footer);
   }

   public function publish_footer(Request $request)
   {
       $footer = Footer::first();
       if (!$footer) {
           return $this->error('Footer values not found', 404);
       }

       $footer->approved_footer_logo_path = $footer->footer_logo_path;
       $footer->approved_about_text = $footer->about_text;
       $footer->approved_whatsapp_link = $footer->whatsapp_link;
       $footer->approved_facebook_link = $footer->facebook_link;
       $footer->approved_twitter_link = $footer->twitter_link;
       $footer->approved_instagram_link = $footer->instagram_link;
       $footer->approved_tiktok_link = $footer->tiktok_link;
       $footer->approved_linkedin_link = $footer->linkedin_link;
       $footer->approved_googleplay_link = $footer->googleplay_link;
       $footer->approved_appstore_link = $footer->appstore_link;
       $footer->approved_mail = $footer->mail;
       $footer->approved_phone = $footer->phone;
       $footer->approved_address = $footer->address;
       $footer->approved_copyrights = $footer->copyrights;
       $footer->is_approved = true;
       $footer->save();

       return $this->success('Footer changes published successfully', $footer);
   }

   //Contact
   public function edit_contactus()
   {
    $contact = ContactUs::first();
    return $this-> success('Footer Details Retrieved Succcesfully!',$contact);
   }

   public function update_contactus(Request $request)
   {
       $validator = $request->validate([
           'phone' => 'nullable|string',
           'email' => 'nullable|string',
           'address' => 'nullable|string',
           'timing' => 'nullable|string',
           'maplink' => 'nullable|string',
           'heading' => 'nullable|string',
           'content' => 'nullable|string'
       ]);

       $contact = ContactUs::first();
       if ($contact) {
           $contact->fill($validator);
           $contact->save();
       } else {
           ContactUs::create($validator);
       }

       return $this->success('contact saved successfully', $contact);
   }

   public function publish_contactus(Request $request)
   {
       $contact = ContactUs::first();
       if (!$contact) {
           return $this->error('Contact values not found', 404);
       }

       $contact->approved_phone = $contact->phone;
       $contact->approved_email = $contact->email;
       $contact->approved_address = $contact->address;
       $contact->approved_timing = $contact->timing;
       $contact->approved_maplink = $contact->maplink;
       $contact->approved_heading = $contact->heading;
       $contact->approved_content = $contact->content;
       $contact->is_approved = true;
       $contact->save();

       return $this->success('Footer changes published successfully', $contact);
   }


}

