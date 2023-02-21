<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
#use App\Models\Section;

class StudentController extends Controller
{
    //
    public function addStudent(Request $request){
        $student= new Student;
        $first_name=$request->input("first_name");
        $last_name=$request->input("last_name");
        $email=$request->input("email");
        $phone_number=$request->input("phone_number");
        $picture=$request->file('picture')->store('images','public');
        $section_id=$request->input("section_id");
        #$section=Section::find($section_id);
        $student->first_name=$first_name;
        $student->last_name=$last_name;
        $student->email=$email;
        $student->phone_number=$phone_number;
        $student->picture=$picture;
        $student->section()->associate($section_id);
        $student->save();
        return response()->json([
            'message' => 'Student created successfully!',
     
        ]);
    }
    public function getStudents(Request $request){
        $students=Student::get();
        return response()->json([
            "message"=>$students
        ]);
    }
    public function getStudentById(Request $request, $id){
        $student=Student::find($id)->with(["section"])->get();
        return response()->json([
            "message"=>$student
        ]);
    }
    public function getStudentsBySection(Request $request, $section_id){
        $students=Student::where("section_id", $section_id)->with(["sections"])->get();
        return response()->json([
            "message" => $students
        ]);
    }
    public function deleteStudentById(Request $request, $id){
        $student=Student::find($id);
        $student->delete();
        return response()->json([
            "message"=> "Student Deleted Successfully!"
        ]);
    }
}
