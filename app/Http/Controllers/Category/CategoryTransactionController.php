<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $transaction = $category->products()
        ->whereHas('transactions')
        ->with('transactions')->get()->pluck('transactions')->collapse();
        return $this->ShowAll($transaction);
    }
}
