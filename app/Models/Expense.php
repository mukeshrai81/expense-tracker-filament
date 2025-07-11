<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    protected $casts = [
        'tags' => 'array',
    ];

    protected $fillable = [
        'title',
        'title',
        'amount',
        'date',
        'description',
        'category_id',
        'tags'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
