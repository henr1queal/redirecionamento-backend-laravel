<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['url', 'needs_count', 'count', 'user_id', 'redirect_id'];
    /**
     * Get the user that owns the Destination
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Redirect::class);
    }
    
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }
}
