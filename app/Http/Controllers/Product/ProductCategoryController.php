<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;
        return $this->ShowAll($categories);
    }

    /**
    * Attach category to existen product
    *
    * @param Request $request
    * @param Product $product
    * @param Category $category
    * @return void
    */
    public function update(Request $request,Product $product,Category $category)
    {
      $product->categories()->syncWithoutDetaching([ $category->id]);
      return $this->ShowAll($product->categories);
    }

    public function destroy(Product $product,Category $category)
    {
       if(!$product->categories()->find($category->id))
        return $this->errorResponse("The specified category isnt category of given product",404);
       $product->categories()->detach($category->id);
       return $this->ShowAll($product->categories);
    }
}
