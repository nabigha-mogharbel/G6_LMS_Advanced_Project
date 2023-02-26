<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Classes;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\Detail;



class SectionController extends Controller
{
    public function addSection(Request $request){
        $Section = new Section;
        $name = $request->input('name');



        $class_id = $request->input('class_id');
        $classes = Classes::find($class_id);


        $detail_id =$request->input('detail_id');
        $details =Detail::find($detail_id);

        $capacity=$request->input('capacity');

        $time_order=$request->input('time_order');

        $Section->name=$name;
        $Section->capacity=$capacity;
        $Section->time_order=$time_order;

        $Section->class_id=$class_id;
        $Section->classes()->associate($classes);

        $Section->detail_id=$detail_id;
        $Section->details()->associate($details);

        $Section->save();
        return response()->json([
            'message' => 'Section created successfully!',
     
        ]);

    }


    public function getSection(Request $request, $id){
       $Section =  Section::where('id',$id)->with(['Classes'])->get();
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


