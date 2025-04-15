<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableReservation extends Model
{
    protected $fillable = ['client_id', 'table_number', 'reservation_date'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
