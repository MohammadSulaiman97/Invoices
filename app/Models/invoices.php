<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoices extends Model
{
    use HasFactory;


    public function section()
    {
        return $this->belongsTo(sections::class);
        // return $this->belongsTo('App\Models\sections');
    }
}
