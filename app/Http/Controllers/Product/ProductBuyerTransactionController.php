<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product , User $buyer)
    {
        $rules=[
            'quantity'=>'required|integer|min:1'
        ];
        $this->validate($request,$rules);
        if($buyer->id == $product->seller_id)
         return $this->errorResponse("The buyer must be different from the seller",409);
        if(!$buyer->IsVerified())
         return $this->errorResponse("The buyer must be a verified user", 409);
        if (!$product->seller()->IsVerified())
          return $this->errorResponse("The seller must be a verified user", 409);
        if (!$product->IsAvailable())
          return $this->errorResponse("The product is not available", 409);
        if($product->quantity < $request->quantity)
          return $this->errorResponse("The product does not have enough unit for this transaction", 409);

        return DB::transaction(function() use($request,$product,$buyer){
         $product->quantity -= $request->quantity;
         $product->save();
         $transaction = Transaction::create([
                'quantity'=>$request->quantity,
                'buyer_id'=>$buyer->id,
                'product_id'=>$product->id
         ]);
         return $this->ShowOne($transaction,201);
        });
            
    }

}
