<?php

namespace App\Http\Controllers;
use App\Models\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ClassesController extends Controller
{
    public function AddClass(Request $request){
        $class= new Classes();
        $name = $request->input('name');
        $description = $request->input('description');
        $picture=$request->file('picture')->store('images','public');
        $class-> name =$name;
        $class-> description = $description;
        $class->picture=$picture;
        $class->save();

        return response()->json([
            "message"=>"Class added successfully"
        ]);
    }

        public function  GetClass(Request $request){

            $class= Classes::get();
            return response()->json([
                "message"=>$class]);

        }

        public function getClassById(Request $request, $id){
            $Classes=Classes::where('id',$id)->get();
            return response()->json([
                "message"=>$Classes
            ]);
        }


        public function getClassByName(Request $request, $name){
            $Classes=Classes::where('name',$name)->get();
            return response()->json([
                "message"=>$Classes
            ]);
        }

        public function deleteClass(Request $request, $id){
            $Classes=Classes::find($id);
            $Classes->delete();
            return response()->json([
                "message"=> "Classes Deleted Successfully!"
            ]);
        }



        public function updateClass(Request $request, $id){
            $Classes=Classes::find($id);
           $inputs=$request->except("picture", "_method");
           if($inputs){ $Classes->update($inputs);}
           if($request->hasFile("picture")){
               Storage::delete("public/", $Classes->picture);
               $picture=$request->file("picture")->store("images","public");
               $Classes->update(["picture" => $picture]);
           }
           return response()->json([
               "message" => "Classes updated successfully",
               "Classes" => $Classes
           ]);
       }
   }

    

