<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }
    public function updateAttendanceBySSD(Request $request, $student_id, $section_id, $date){
        $validated=Validator::make($request->all(), [
            'section_id' => 'numeric',
            'student_id' => 'numeric',
            'date' => 'date',
            'status' => Rule::in(["present", "absent", "late"]),
            $student_id => "numeric",
            $section_id => "numeric",
            $date => "date"
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $Attendance =  Attendance::where("student_id",$student_id)->where("section_id", $section_id)->where("date", $date)->get();
        $inputs= $request->except('_method');
        $Attendance->update($inputs);
        return response()->json([
            'message' => 'Attendance edited successfully!',
            'Attendance' => $Attendance,
    
        ]);
    }
    public function updateAttendance(Request $request, $id){
        $validated=Validator::make($request->all(), [
            'section_id' => 'numeric',
            'student_id' => 'numeric',
            'date' => 'date',
            'status' => Rule::in(["present", "absent", "late"]),
            $id => "numeric",
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $Attendance =  Attendance::find($id)->get();
        $inputs= $request->except('_method');
        $Attendance->update($inputs);
        return response()->json([
            'message' => 'Attendance edited successfully!',
            'Attendance' => $Attendance,
    
        ]);
    }
    public function addAttendance(Request $request){
        $validated=Validator::make($request->all(), [
            'section_id' => 'required|numeric',
            'student_id' => 'required|numeric',
            'date' => 'required|date',
            'status' => Rule::in(["present", "absent", "late"])
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $section_id = $request->input('section_id');
        $student_id = $request->input('student_id');
        $status=$request->input('status');
        $date=$request->input("date");
        $Attendance =  Attendance::where("student_id",$student_id)->where("section_id", $section_id)->where("date", $date)->get();
        if($Attendance->isEmpty()){
        $student=Student::find($student_id);
        $student->Attendance()->attach($section_id, ["status" => $status, "date"=>$date]);
        return response()->json(["message" => "attendance record created successfully"]);}
        else{
           // $Attendance->update(["status" => $status]);
        return response()->json([
            'message' => 'Attendance edited successfully!',
            'Attendance' => $Attendance,
    
        ]);
        }
    }

     public function getAttendanceById(Request $request, $id){
        $validated=Validator::make($id, [
            $id => 'numeric',
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $Attendance = Attendance::with("student", "section")->find($id);
        return response()->json([
            "message"=>$Attendance
        ]);
     }

     public function getAllAttendance(Request $request){
         $Attendance=Attendance::with(["student", "section"])->get();
         return response()->json([
             'message' => $Attendance,

         ]);
     }
     public function deleteAttendance(Request $request, $id){
        $Attendance = Attendance::find($id);
        $Attendance->delete();
        return response()->json([
            'message' => 'Attendance deleted Successfully!',
        ]);
    }
    public function editAttendance(Request $request, $id){
        $validated=Validator::make($request->all(), [
            'section_id' => 'numeric',
            'student_id' => 'numeric',
            'date' => 'date',
            'status' => Rule::in(["present", "absent", "late"]),
            $id => "numeric"
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $Attendance =  Attendance::find($id);
        $inputs= $request->except('_method');
        $Attendance->update($inputs);
        return response()->json([
            'message' => 'Attendance edited successfully!',
            'Attendance' => $Attendance,

        ]);
   }
   public function getAttendanceByStudent(Request $request, $id){
    $attendance=Attendance::where("student_id", $id)->with("student", "section")->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Atendance"=>$attendance
    ]);
   }
   public function getAttendanceBySection(Request $request, $id){
    $attendance=Attendance::where("section_id", $id)->with("student", "section")->get();
    return response()->json([
        "message"=> "Section Attendance Records",
        "Atendance"=>$attendance
    ]);
   }
   public function getAttendanceByClass(Request $request, $class_id){
    $classSections=Section::where("class_id", $class_id)->get();
    $attendance=[];
    foreach ($classSections as $sectionCol){
        $index=$sectionCol->id;
       array_push($attendance,Attendance::where("section_id", $index)->with("student", "section")->get() );
    };
    return response()->json([
        "message"=> "Student Attendance Records",
        "Atendance"=>$attendance
    ]);
}
   public function getAttendanceByDate(Request $request, $edate, $sdate){
    $attendance=Attendance::whereBetween("date",[$edate, $sdate])->with("student", "section")->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Atendance"=>$attendance
    ]);
}
   public function getAttendanceByOneDate(Request $request, $date){
    $attendance=Attendance::where("date", $date)->with("student", "section")->get();
    return response()->json([
        "message"=> "Date Attendance Records",
        "Atendance"=>$attendance
    ]);
}
public function getAttendanceByStudentWithDate(Request $request, $id, $edate, $sdate){
    $attendance=Attendance::where("student_id", $id)->whereBetween("date", ["2023-03-01","2023-03-05"])->with("student", "section")->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Atendance"=>$attendance
    ]);
}
public function getAttendanceBySectionWithDate(Request $request, $id, $edate, $sdate){
    $attendance=Attendance::where("section_id", $id)->whereBetween("date",[$sdate, $edate])->with("student", "section")->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Atendance"=>$attendance
    ]);
}
public function getAttendanceByClassWithDate(Request $request, $class_id, $edate, $sdate){
    $attendance=Attendance::where("section_id", $class_id)->whereBetween("date",[$sdate, $edate])->with("student", "section")->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Atendance"=>$attendance
    ]);
}
//updateAttendanceBySSD

}
