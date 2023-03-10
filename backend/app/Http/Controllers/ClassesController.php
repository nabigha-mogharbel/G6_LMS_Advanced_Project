<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ClassesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function AddClass(Request $request)
    {
        $validated=Validator::make($request->all(), [
            'name' => 'required|string',
            'floor' => 'required|string',
            'color' => 'required|string|min:6|min:6'        
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()], 422);
        }
        $class = new Classes();
        $name = $request->input('name');
        $floor = $request->input('floor');
        $color =$request->input('color');
        $class->name = $name;
        $class->floor = $floor;
        $class->color = $color;
        
        $class->save();
        return response()->json([
            "message" => "Class added successfully"
        ], 201);
    }

    public function  GetClass(Request $request)
    {
        $class = Classes::paginate(5);
        return response()->json([
            "message" => $class
        ], 200);
    }

    public function getClassSort(Request $request)
    {
        $classes = Classes::orderBy('name')->paginate(5);
        return response()->json([
            "message" => $classes,
        ], 200);
    }

    public function getClassSortByFloor(Request $request)
    {
        $classes = Classes::orderBy('floor')->paginate(5);
        return response()->json([
            "message" => $classes,
        ], 200);
    }

    public function getClassById(Request $request, $id)
    {
        $Classes = Classes::where('id', $id)->get();
        if($Classes->isEmpty()){
            return response()->json([
                'message' => "Class doesn't exists",
         
            ], 400);
        }
        return response()->json([
            "message" => $Classes
        ], 200);
    }

    public function deleteClass(Request $request, $id)
    {
        $Classes = Classes::find($id);
        if(empty($Classes)){
            return response()->json([
                'message' => "Class doesn't exists",
            ], 400);
        }
        $Classes->delete();
        return response()->json([
            "message" => "Classes Deleted Successfully!"
        ], 200);
    }

    public function updateClass(Request $request, $id)
    {
        $validated=Validator::make($request->all(), [
            'name' => 'string',
            'floor' => 'string',
            'color' => 'string|min:6|min:6'
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()], 422);
        }
        $Classes = Classes::find($id);
        if(empty($Classes)){
            return response()->json([
                'message' => "Class doesn't exists",
            ], 400);
        }
        $inputs = $request->except("_method");
        if ($inputs) {
            $Classes->update($inputs);
        }
        return response()->json([
            "message" => "Classes updated successfully",
            "Classes" => $Classes
        ], 200);
    }
}
