<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;

class Detail extends Model
{

    protected $fillable = [
        'type',
        'requirements',
    ];


    use HasFactory;

    public function section() {
        return $this->hasMany(Section::class,"id","detail_id");
    }


}
