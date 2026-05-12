<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    
   protected $table = 'books';

   protected $fillable = [
       'title',
       'author',
       'quantity',
   ];

   public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

}
