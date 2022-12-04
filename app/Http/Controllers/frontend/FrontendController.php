<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class FrontendController extends Controller
{
    //
    public function index(){
        $featured_products = Product::where('trending','1')->take(15)->get();
        $trending_category = Category::where('popular','1')->take(15)->get();
        
        return view('frontend.index',compact('featured_products','trending_category'));
    }

    public function searchProduct(Request $request){
        if($request->search){
            $searchProduct = Product::where('name', 'LIKE' ,'%'.$request->search.'%')->latest()->paginate(15);
            return view('frontend.search',compact('searchProduct'));
        }else{
            return redirect()->back()->with('status','Empty Search');
        }
    }

    public function category(){
        $category = Category::where('status','1')->get();
        return view('frontend.category',compact('category'));
    }

    public function viewcategory($slug){
        if(Category::where('slug',$slug)->exists())
        {
            $category = Category::where('slug',$slug)->first();
            $products = Product::where('category_id',$category->id)->where('status','1')->get();
            return view('frontend.products.index',compact('category','products'));
        }
        else{
            return redirect('/')->with('status',"您所選的類別不存在");
        }
        
    }
    public function viewproduct($category_slug,$product_slug){
        if(Category::where('slug',$category_slug)->exists()){
            if(Product::where('slug',$product_slug)->exists()){
                $products = Product::where('slug',$product_slug)->first();
                return view('frontend.products.view',compact('products'));
            }
            else{
                return redirect('/')->with('status',"您所選的商品不存在或是已下架。");
            }
        }else{
            return redirect('/')->with('status',"您所選的類別不存在或是已下架。");
        }
        
    }
}
