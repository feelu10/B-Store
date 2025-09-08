<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id','name','slug','short_description','description',
        'price','stock','image_path','is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(\App\Models\ProductImage::class);
    }


    public function getFeaturedImageUrlAttribute(): ?string
    {
        $img = $this->images->first();             
        if ($img) {
            return asset('storage/'.$img->path);
        }
        if (!empty($this->image_path)) {      
            return asset('storage/'.$this->image_path);
        }
        return null;
    }

}
