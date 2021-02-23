<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategorySellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $sellers = $category->products()->with('seller')->get()
        ->pluck('seller')->unique('id')->values();
        //()->with('product.seller')->get()->pluck('product.seller')->unique('id')->values();
        return $this->ShowAll($sellers);
    }
}
