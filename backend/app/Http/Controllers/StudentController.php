<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class StudentController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }
    //
    public function addStudent(Request $request){
        $validated=Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|numeric',
            "email" => "required|email"
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $student= new Student;
        $first_name=$request->input("first_name");
        $last_name=$request->input("last_name");
        $email=$request->input("email");
        $phone_number=$request->input("phone_number");
        $picture=$request->file('picture')->store('images','public');
        $section_id=$request->input("section_id");
        $section=Section::find($section_id);
        $student->first_name=$first_name;
        $student->last_name=$last_name;
        $student->email=$email;
        $student->phone_number=$phone_number;
        $student->picture=$picture;
        $student->Section()->associate($section);
        $student->save();
        return response()->json([
            'message' => 'Student created successfully!',
     
        ]);
    }
    public function getStudents(Request $request){
        $students=Student::with(["section"])->paginate(5);
        return response()->json([
            "message"=>$students
        ]);
    }

    //Begin the sorting hby first-name, last-name, email, phone-number
    public function getStudentsSortByFirst_name(Request $request)
    {
        $students=Student::with(["section"])->orderBy('first_name')->paginate(5);
        return response()->json([
            "message"=>$students
        ]);
    }

    public function getStudentsSortByLast_name(Request $request)
    {
        $students=Student::with(["section"])->orderBy('last_name')->paginate(5);
        return response()->json([
            "message"=>$students
        ]);
    }

    public function getStudentsSortByEmail(Request $request)
        {
            $students=Student::with(["section"])->orderBy('email')->paginate(5);
            return response()->json([
                "message"=>$students
            ]);
        }

    public function getStudentsSortByPhone_number(Request $request)
        {
            $students=Student::with(["section"])->orderBy('phone_number')->paginate(5);
            return response()->json([
                "message"=>$students
            ]);
        }
    //finishing the sorting here
    

    
    public function getStudentById(Request $request, $id){
        $student=Student::find($id)->with(["Section"])->get();
        if($student->isEmpty()){
            return response()->json([
                'message' => "Student doesn't exists",
         
            ], 400);
        }
        return response()->json([
            "message"=>$student
        ]);
    }
    public function getStudentsBySection(Request $request, $section_id){
        $students=Student::where("section_id", $section_id)->with(["Section"])->get();
        return response()->json([
            "message" => $students
        ]);
    }
    public function getStudentsByClass(Request $request, $section_id){
        $students=Student::where("section_id", $section_id)->with(["Section"])->get();
        return response()->json([
            "message" => $students
        ]);
    }

    public function deleteStudentById(Request $request, $id){
        $student=Student::find($id);
        if(empty($student)){
            return response()->json([
                'message' => "Student doesn't exists",
         
            ], 400);
        }
        $student->delete();
        return response()->json([
            "message"=> "Student Deleted Successfully!"
        ]);
    }
    public function updateStudent(Request $request, $id){
         $student=Student::find($id);
         if(empty($student)){
            return response()->json([
                'message' => "Student doesn't exists",
         
            ], 400);
        }
        $inputs=$request->except("picture", "_method");
        if($inputs){ $student->update($inputs);}
        if($request->hasFile("picture")){
            Storage::delete("public/", $student->picture);
            $picture=$request->file("picture")->store("images","public");
            $student->update(["picture" => $picture]);
        }
        return response()->json([
            "message" => "Student updated successfully",
            "student" => $student
        ]);
    }
}
