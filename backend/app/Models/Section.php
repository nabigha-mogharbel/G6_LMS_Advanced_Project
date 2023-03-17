<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classes;
class Section extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'capacity'
    ];

public function Class() {
    return $this->belongsTo(Classes::class, "class_id", "id");
}

public function Student() {
    return $this->hasMany(Student::class);
}
public function Attendance() {
    return $this->hasMany(Student::class);
}
}
