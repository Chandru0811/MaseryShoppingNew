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
            'product.image:path,imageable_id,imageable_type',
            'wishlists'
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
            'product.image:path,imageable_id,imageable_type',
            'wishlists'
        ])->get();

        $recent = Inventory::with([
            'image:path,imageable_id,imageable_type',
            'product:id,slug',
            'product.image:path,imageable_id,imageable_type',
            'wishlists'
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

        $productsQuery = Inventory::search($term)->where('active', 1);

        $productsQuery->with([
            'product.image:path,imageable_id,imageable_type',
            'wishlists',
        ]);

        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');
            $productsQuery->where('sale_price', '>=', $minPrice)->where('sale_price', '<=', $maxPrice);
        }

        if ($request->has('brand')) {
            $brandlist = explode(',', $request->input('brand'));
            $productsQuery->whereIn('brand_id', $brandlist);
        }

        if ($request->has('ingrp')) {
            $categoryGroupSlugs = explode(',', $request->input('ingrp'));
            $categoryGroups = CategoryGroup::whereIn('slug', $categoryGroupSlugs)->get();
            if ($categoryGroups->isNotEmpty()) {
                $productsQuery->whereHas('product.category', function ($query) use ($categoryGroups) {
                    $query->whereHas('categorySubGroupTwo', function ($q2) use ($categoryGroups) {
                        $q2->whereHas('categorySubGroupOne', function ($q3) use ($categoryGroups) {
                            $q3->whereHas('categorySubGroup', function ($q4) use ($categoryGroups) {
                                $q4->whereIn('category_group_id', $categoryGroups->pluck('id'));
                            });
                        });
                    });
                });
            }
        } elseif ($request->has('insubgrp')) {
            $categorySubGroupSlugs = explode(',', $request->input('insubgrp'));
            $categorySubGroups = CategorySubGroup::whereIn('slug', $categorySubGroupSlugs)->get();
            if ($categorySubGroups->isNotEmpty()) {
                $productsQuery->whereHas('product.category', function ($query) use ($categorySubGroups) {
                    $query->whereHas('categorySubGroupTwo', function ($q2) use ($categorySubGroups) {
                        $q2->whereHas('categorySubGroupOne', function ($q3) use ($categorySubGroups) {
                            $q3->whereIn('category_sub_group_id', $categorySubGroups->pluck('id'));
                        });
                    });
                });
            }
        } elseif ($request->has('insubgrp1')) {
            $categorySubGroupOneSlugs = explode(',', $request->input('insubgrp1'));
            $categorySubGroupsOne = CategorySubGroupOne::whereIn('slug', $categorySubGroupOneSlugs)->get();
            if ($categorySubGroupsOne->isNotEmpty()) {
                $productsQuery->whereHas('product.category', function ($query) use ($categorySubGroupsOne) {
                    $query->whereHas('categorySubGroupTwo', function ($q2) use ($categorySubGroupsOne) {
                        $q2->whereIn('category_sub_group_one_id', $categorySubGroupsOne->pluck('id'));
                    });
                });
            }
        } elseif ($request->has('insubgrp2')) {
            $categorySubGroupTwoSlugs = explode(',', $request->input('insubgrp2'));
            $categorySubGroupsTwo = CategorySubGroupTwo::whereIn('slug', $categorySubGroupTwoSlugs)->get();
            if ($categorySubGroupsTwo->isNotEmpty()) {
                $productsQuery->whereHas('product.category', function ($query) use ($categorySubGroupsTwo) {
                    $query->whereIn('category_sub_group_two_id', $categorySubGroupsTwo->pluck('id'));
                });
            }
        } elseif ($request->has('in')) {
            $categorySlugs = explode(',', $request->input('in'));
            $categories = Category::whereIn('slug', $categorySlugs)->get();

            if ($categories->isNotEmpty()) {
                $productsQuery->whereIn('category_id', $categories->pluck('id'));
            }
        }

        $products = $productsQuery->get();

        $productsArray = $products->values()->all();

        return $this->success('Products Retrieved Successfully', $productsArray);
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