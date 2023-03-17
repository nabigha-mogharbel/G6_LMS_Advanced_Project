<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;
use App\Models\Attendance;

class Classes extends Model
{

    protected $fillable = [
        'name',
        'floor',
        'color'
    ];
    use HasFactory;



    public function Section() {
        return $this->hasMany(Section::class, "id", "class_id");
    }
    public function Attendance() {
        return $this->hasMany(Attendance::class, "id", "class_id");
    }
}
