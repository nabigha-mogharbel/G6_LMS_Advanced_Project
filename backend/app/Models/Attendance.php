<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;
use App\Models\Classes;
use App\Models\Student;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
        "date"

    ];

    public function section() {
        return $this->belongsTo(Section::class);
    }
    public function student() {
        return $this->belongsTo(Student::class);
    }
    public function classes() {
        return $this->belongsTo(Classes::class, "class_id", "id");
    }

}
