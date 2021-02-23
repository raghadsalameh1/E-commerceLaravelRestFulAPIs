<?php
namespace  App\Http\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
trait ApiResponser
{
    public function successResponse($data,$code)
    {
      return response()->json($data, $code);
    }

    public function errorResponse($message,$code)
    {
        return response()->json(['error' => $message,'code' => $code], $code);
    }

    public function ShowAll(Collection $collection, $code=200)
    {
        return response()->json(['data' => $collection], $code);
    }

    public function ShowOne(Model $model, $code = 200)
    {
        return response()->json(['data' => $model], $code);
    }
}
