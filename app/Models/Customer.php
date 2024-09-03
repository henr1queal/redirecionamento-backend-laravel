<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];
    /**
     * Get all of the redirects for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function redirects(): HasMany
    {
        return $this->hasMany(Redirect::class);
    }
}
