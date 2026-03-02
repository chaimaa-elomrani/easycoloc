<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
    'colocation_id',
    'payer_id',
    'category_id',
    'title',
    'amount',
    'date',
  ];


    public function scopeForMonth($query, $month = null)
    {
        if ($month) {
            $query->whereMonth('date', $month->month)
                ->whereYear('date', $month->year);
        }
    }
  
    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
