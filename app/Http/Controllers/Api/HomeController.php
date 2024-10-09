<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Traits\ApiResponses;
use App\Models\Inventory;
use App\Models\About;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Header;
use App\Models\Footer;
use App\Models\ContactUs;
use App\Models\PaymentOption;
use App\Models\CategoryGroup;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\CategorySubGroup;
use App\Models\CategorySubGroupOne;
use App\Models\CategorySubGroupTwo;

class HomeController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $banners = Banner::orderBy('order', 'asc')->get()->toArray();

        $featuredProducts = Inventory::with([
            'image:path,imageable_id,imageable_type',
            'product:id,slug',
            'product.image:path,imageable_id,imageable_type', 'wishlists'
        ])->limit(10)->get();

        $categoryBasedProducts = Category::withCount('products')
            ->with(['products' => function ($query) {
                $query->with(['image' => function ($query) {
                    $query->select('path', 'imageable_id', 'imageable_type')
                        ->groupBy('path', 'imageable_id', 'imageable_type'); // Ensure only one image per product is fetched
                }]);
            }])
            ->get();

        $about = About::orderBy('order', 'asc')->get();

        $allProducts = Inventory::with([
            'image:path,imageable_id,imageable_type',
            'product:id,slug',
            'product.image:path,imageable_id,imageable_type', 'wishlists'
        ])->get();

        $recent = Inventory::with([
            'image:path,imageable_id,imageable_type',
            'product:id,slug',
            'product.image:path,imageable_id,imageable_type', 'wishlists'
        ])
            ->available()->latest()->limit(10)->get();

        $data = [
            'banners' => $banners,
            'featured_products' => $featuredProducts,
            'about' => $about,
            'recent_products' => $recent,
            'allProducts' => $allProducts,
            'categoryBasedProducts' => $categoryBasedProducts
        ];

        return $this->success('HomeScreen Datas Retrived Successfully!', $data);
    }

    public function product($slug)
    {
        $item = Inventory::where('slug', $slug)->available()->firstOrFail();

        $item->load([
            'product' => function ($q) use ($item) {
                $q->select('id', 'brand', 'model_number', 'mpn', 'gtin', 'gtin_type', 'slug', 'description', 'sale_count', 'category_id', 'created_at')
                    ->withCount('inventories');
            },
            'product.image:path,imageable_id,imageable_type',
            'wishlists', // Load the wishlists relationship
        ])->get();

        return $this->success('Product Retrived Successfully!', $item);
    }

    public function getcategory()
    {
        //$categories = Category::with('allChildren')->where('parent_id', null)->withCount('products')->get();
        $categories = CategoryGroup::with('categorySubGroup.categorySubGroupOne.categorySubGroupTwo.category')->get();
        return $this->success('Categories Retrived Successfully', $categories);
    }

    public function getbrands()
    {
        $brands = Brand::withCount('products')->get();
        return $this->success('Brands Retrived Successfully', $brands);
    }

    public function search(Request $request)
    {
        $term = $request->input('q');
        $products = Inventory::search($term)->where('active', 1)->get();
        $products->load([
            'product.image:path,imageable_id,imageable_type',
            'wishlists',
        ]);

        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');
            $products = $products->where('sale_price', '>=', $minPrice)->where('sale_price', '<=', $maxPrice);
        }
       

        if ($request->has('brand')) {
            $brandlist = explode(',', $request->input('brand'));
            $products = $products->whereIn('brand_id', $brandlist);
        }
        
        if ($request->has('ingrp')) {
           $categoryGroup = CategoryGroup::where('slug', $request->input('ingrp'))->firstOrFail();
           $products = Product::whereHas('category', function ($query) use ($categoryGroup) {
            $query->whereHas('categorySubGroupTwo', function ($q2) use ($categoryGroup) {
                $q2->whereHas('categorySubGroupOne', function ($q3) use ($categoryGroup) {
                    $q3->whereHas('categorySubGroup', function ($q4) use ($categoryGroup) {
                        $q4->where('category_group_id', $categoryGroup->id); 
                    });
                });
            });
            })->get();
        }
        else if ($request->has('insubgrp')) { 
            $categorySubGroup = CategorySubGroup::where('slug', $request->input('insubgrp'))->firstOrFail();
            $products = Product::whereHas('category', function ($query) use ($categorySubGroup) {
                $query->whereHas('categorySubGroupTwo', function ($q2) use ($categorySubGroup) {
                    $q2->whereHas('categorySubGroupOne', function ($q3) use ($categorySubGroup) {
                        $q3->where('category_sub_group_id', $categorySubGroup->id); 
                    });
                });
            })->get();
         }else if($request->has('insubgrp1')){
            $categorySubGroupOne = CategorySubGroupOne::where('slug', $request->input('insubgrp1'))->firstOrFail();

            $products = Product::whereHas('category', function ($query) use ($categorySubGroupOne) {
                $query->whereHas('categorySubGroupTwo', function ($q2) use ($categorySubGroupOne) {
                    $q2->where('category_sub_group_one_id', $categorySubGroupOne->id);
                });
            })->get();
         }
         else if($request->has('insubgrp2')){
            $categorySubGroupTwo = CategorySubGroupTwo::where('slug', $request->input('insubgrp2'))->firstOrFail();

            $products = Product::whereHas('category', function ($query) use ($categorySubGroupTwo) {
                $query->where('category_sub_group_two_id', $categorySubGroupTwo->id);
            })->get();
         }
         else if($request->has('in')){
            $category = Category::where('slug', $request->input('in'))->firstOrFail();

            $products = Product::where('category_id', $category->id)->get();
         }

         

        $productsArray = $products->values()->all();

        return $this->success('Products Retrived Successfully', $productsArray);
    }

     //Header
     public function header()
     {
         $header = Header::approved()->first();
         return $this->success('Header Details Successfully!', $header);
     }

     //Footer
     public function footer()
     {
         $footer = Footer::approved()->first();
         return $this->success('Footer Details Successfully!', $footer);
     }

     //Contact
     public function contactus()
     {
         $contact = ContactUs::approved()->first();
         return $this->success('Contact Details Successfully!', $contact);
     }

    public function getPaymentData()
    {
        $paymentOptions = PaymentOption::with(['paymentSubTypes' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('is_active', true)
            ->get();

        return $this->success('Payment option retrieved successfully.', $paymentOptions);
    }
}
