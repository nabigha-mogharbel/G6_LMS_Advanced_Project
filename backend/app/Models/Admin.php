<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{

    protected $fillable = [
        'name',
        'email',
        'password',
        
    ];



    use HasFactory;

public function Admin() {
    return $this->belongsTo(Admin::class);
}

public function Admin_child() {
    return $this->hasOne(Admin::class);
}

}