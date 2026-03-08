<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'import_id',
        'type',
        'amount',
        'currency',
        'date',
        'merchant',
        'description',
        'meta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
