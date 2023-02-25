<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;

class Classes extends Model
{

    protected $fillable = [
        'name',
        'description',
        'picture',
    ];
    use HasFactory;



    public function section() {
        return $this->hasMany(Section::class,"id","class_id");
    }
}
