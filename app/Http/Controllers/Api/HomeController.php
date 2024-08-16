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
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

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
        $category = Category::withCount('products')->get();
        return $this->success('Categories Retrived Successfully', $category);
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

        if ($request->has('categories')) {
            $categorylist = explode(',', $request->input('categories'));
            $products = $products->whereIn('category_id', $categorylist);
        }

        if ($request->has('brand')) {
            $brandlist = explode(',', $request->input('brand'));
            $products = $products->whereIn('brand_id', $brandlist);
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
        $paymentOptions = PaymentOption::with(['paymentSubTypes'])->get();

        return $this->success('Payment option retrieved successfully.', $paymentOptions);
    }
}
