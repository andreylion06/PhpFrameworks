<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['name', 'email', 'phone'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tableReservations(): HasMany
    {
        return $this->hasMany(TableReservation::class);
    }
}
