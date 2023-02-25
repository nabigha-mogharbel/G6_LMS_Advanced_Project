<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Classes;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;


class SectionController extends Controller
{
    public function addSection(Request $request){
        $Section = new Section;
        $name = $request->input('name');
        $class_id = $request->input('class_id');
        $classes = Classes::find($class_id);
        $capacity=$request->input('capacity');
        $content=$request->input('content');
        $Section->name=$name;
        $Section->capacity=$capacity;
        $Section->content=$content;
        $Section->class_id=$class_id;
        $Section->classes()->associate($classes);
        $Section->save();
        return response()->json([
            'message' => 'Section created successfully!',
     
        ]);

    }


    public function getSection(Request $request, $id){
       $Section =  Section::where('id',$id)->with(['Classes'])->get();
    //    $Section =  Section::where('id',$id)->get();
        return response()->json([
            'message' => $Section,
     
        ]);
    }

    public function getAllSection(Request $request){
       # $Section =  Section::get();
        $Section =  Section::with(['Classes'])->get();
        return response()->json([
            'message' => $Section,
    
        ]);
    }

    public function deleteSection(Request $request, $id){
         
        $Section = Section::find($id);
        $Section->delete();
        return response()->json([
            'message' => 'Section deleted Successfully!',
        ]);
    }


    public function editSection(Request $request, $id){
        $Section =  Section::find($id);
        $inputs= $request->except('_method');
        $Section->update($inputs);
        return response()->json([
            'message' => 'Section edited successfully!',
            'Section' => $Section,
     
        ]);
   }
}


