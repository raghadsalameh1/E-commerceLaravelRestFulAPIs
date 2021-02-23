<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Product;
use App\Seller;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products()
            ->whereHas('transactions')
            ->with('transactions.product')->get()
            ->pluck('transactions')
            ->collapse()
            ->pluck('product')
            ->unique('id')
            ->values();
        return $this->ShowAll($products);
    }

    /**
     * Undocumented function
     *
     * @param \Illuminate\Http\Request $request
     * @param User $seller
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|int|min:1',
            'image' => 'required|image'
        ];

        $this -> validate($request,$rules);
        $data = $request->all();
        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] ='1.jpg';
        $data['seller_id'] = $seller->id;
        $product = Product::create($data);
        return $this->ShowOne($product);
    }


    /**
     * Update product info for specific user
     *
     * @param \Illuminate\Http\Request  $request
     * @param Seller $seller
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller ,Product $product)
    {
        $rules = [
            'quantity' => 'int|min:1',
            'status' => 'in:'. Product::UNAVAILABLE_PRODUCT.','. Product::AVAILABLE_PRODUCT,
            'image' => 'image'
        ];

        $this->validate($request, $rules);
        $this->checkSeller($seller,$product);
        $data = $request->all();
        $product->fill($request->only(['name', 'description', 'quantity']));
        if($request->has('status'))
        {
          $product->status = $request->status;
          if($product->IsAvailable()&& $product->categories()->count()==0)
          return $this->errorResponse("An active product must have at least one Gategory",409);
        }
        if($product->isClean())
        {
            return $this->errorResponse("You need to specify a different values to update", 422);
        }
        $product->save();
        return $this->ShowOne($product);
    }

    function checkSeller(Seller $seller,Product $product)
    {
      if($seller->id != $product->seller_id)
        throw new  HttpException(422, 'The specified seller isnt the actual seller of the product');        
    }


    /**
     *Remove the specified resource from storage.
     *
     * @param Seller $seller
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);
        $product->delete();
        return $this->successResponse('Product is deleted successfully', 200);
    }
}
