<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Detail;

class Section extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'capacity',
        'content',
    ];

public function classes() {
    return $this->belongsTo(Classes::class,"class_id","id");
}

public function students() {
    return $this->hasMany(Student::class);
}

public function details(){
    return $this->belongsTo(Detail::class,"detail_id","id");
}

}