<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description'
    ];

    protected $hidden = [ 'pivot' ];

    protected $date = ['deleted_at'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
