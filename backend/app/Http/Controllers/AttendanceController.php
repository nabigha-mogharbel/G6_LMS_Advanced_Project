<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Classes;

class AttendanceController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }
    public function updateAttendanceBySSD(Request $request, $student_id, $section_id, $date){
        $validated=Validator::make($request->all(), [
            'section_id' => 'numeric',
            'student_id' => 'numeric',
            "class_id" =>"numeric",
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
        $Attendance[0]->update($inputs);
        return response()->json([
            'message' => 'Attendance edited successfully!',
            'Attendance' => $Attendance,
    
        ]);
    }
    public function generateAttendance(Request $request, $date){
        $students=Student::get();
        foreach($students as $student){
            $attendance=new Attendance;
            $section=Section::where("id",$student->section_id)->with(["Class"])->get();
            $attendance->date=$date;
            $attendance->status="null";
            $attendance->section()->associate($section[0]);
            $attendance->student()->associate($student);
            $attendance->classes()->associate($section[0]->class_id);
            $attendance->save();
        }
        $attendance=Attendance::where("date", $date)->with(["student", "section", "classes"])->get();
        $sections=Section::with(["Class"])->get();
        $classes=Classes::get();
    return response()->json([
        "message"=> "Date Attendance Records",
        "Atendance"=>$attendance,
        "section_filters"=>$sections,
        "class_filters"=>$classes
    ]);
    }
    public function updateAttendance(Request $request, $id){
        $validated=Validator::make($request->all(), [
            'status' => Rule::in(["present", "absent", "late","null"]),
            $id => "numeric",
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $Attendance =  Attendance::where("id",$id)->get();
        //$inputs= $request->except('_method');
        $Attendance[0]->update(["status"=> $request->input("status")]);
        return response()->json([
            'message' => 'Attendance edited successfully!',
            'Attendance' => $Attendance,
    
        ]);
    }
    public function dashboardData(Request $request, $date){
        $attendance=Attendance::where("date", $date);
        $late=$attendance->where("status", "late")->count();
        $absent=$attendance->where("status", "absent")->count();
        $bySection=Attendance::where("date", $date)->get()->groupBy("section_id");
        $sectionsStat=[];
        $nbOfSections=$bySection->count();
        foreach($bySection as $key => $val){
            $presentBySec=Attendance::where("date", $date)->where("section_id", $key)->where("status", "present")->count();
            $lateBySec=Attendance::where("date", $date)->where("section_id", $key)->where("status", "late")->count();
            $absentBySec=Attendance::where("date", $date)->where("section_id", $key)->where("status", "absent")->count();
            $nullBySec=Attendance::where("date", $date)->where("section_id", $key)->where("status", "null")->count();
            $stat=["present"=>$presentBySec, "late"=>$lateBySec, "absent" => $absentBySec, "null"=>$nullBySec];
            $sectionsStat[$key]=$stat;
        }
        $byClass=Attendance::where("date", $date)->get()->groupBy("class_id");
        $classStat=[];
        $nbOfClass=$byClass->count();
        foreach($byClass as $key => $val){
            $presentByCla=Attendance::where("date", $date)->where("class_id", $key)->where("status", "present")->count();
            $lateByCla=Attendance::where("date", $date)->where("class_id", $key)->where("status", "late")->count();
            $absentByCla=Attendance::where("date", $date)->where("class_id", $key)->where("status", "absent")->count();
            $nullByCla=Attendance::where("date", $date)->where("class_id", $key)->where("status", "null")->count();
            $stat=["present"=>$presentByCla, "late"=>$lateByCla, "absent" => $absentByCla, "null"=>$nullByCla];
            $classStat[$key]=$stat;
        }
       
        return response()->json(["late"=>$late, "absent"=>$absent, "groupedBySection"=>$sectionsStat, "hh"=>$classStat]);
    }
    public function addAttendance(Request $request){
        $validated=Validator::make($request->all(), [
            'section_id' => 'required|numeric',
            'student_id' => 'required|numeric',
            'date' => 'required|date',
            'status' => Rule::in(["present", "absent", "late", "null"])
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $section_id = $request->input('section_id');
        $student_id = $request->input('student_id');
        $status=$request->input('status');
        $class_id=$request->input('class_id');
        $date=$request->input("date");
        $Attendance =  Attendance::where("student_id",$student_id)->where("section_id", $section_id)->where("date", $date)->get();
        if($Attendance->isEmpty()){
        $student=Student::find($student_id);
        $section=Section::find($section_id);
        $class=Classes::find($section->class_id);
        $attendance=new Attendance;
        $attendance->date=$date;
        $attendance->status=$status;
        $attendance->section()->associate($section);
        $attendance->student()->associate($student);
        $attendance->classes()->associate($class);
        $attendance->save();
        return response()->json(["message" => "attendance record created successfully"]);}
        else{
        return response()->json([
            'message' => 'Attendance already exist!',    
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
        $Attendance = Attendance::with(["student", "section"])->find($id);
        return response()->json([
            "message"=> "attendance record by id",
            "Attendance" => $Attendance
        ]);
     }

     public function getAllAttendance(Request $request){
         $Attendance=Attendance::with(["student", "section"])->get();
         return response()->json([
             'message' => "all attendance records",
             "Attendance"=>$Attendance

         ]);
     }
     public function deleteAttendance(Request $request, $id){
        $Attendance = Attendance::find($id);
        $Attendance->delete();
        return response()->json([
            'message' => 'Attendance deleted Successfully!',
        ]);
    }
    /*public function editAttendance(Request $request, $id){
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
        $Attendance =  Attendance::find($id)->get();
        $inputs= $request->except('_method');
        $Attendance->update($inputs);
        return response()->json([
            'message' => 'Attendance edited successfully!',
            'Attendance' => $Attendance,

        ]);
   }*/
   public function getAttendanceByStudent(Request $request, $id){
    $attendance=Attendance::where("student_id", $id)->with(["student", "section", "classes"])->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$attendance
    ]);
   }
   public function getAttendanceBySection(Request $request, $id){
    $attendance=Attendance::where("section_id", $id)->with(["student", "section", "classes"])->get();
    return response()->json([
        "message"=> "Section Attendance Records",
        "Attendance"=>$attendance
    ]);
   }
   public function getAttendanceByClass(Request $request, $class_id){
    $classSections=Section::where("class_id", $class_id)->get();
    $attendance=[];
    foreach ($classSections as $sectionCol){
        $index=$sectionCol->id;
       array_push($attendance,Attendance::where("section_id", $index)->with(["student", "section", "classes"])->get() );
    };
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$attendance
    ]);
}
   public function getAttendanceByDate(Request $request, $edate, $sdate){
    $attendance=Attendance::whereBetween("date",[$edate, $sdate])->with(["student", "section", "classes"])->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$attendance
    ]);
}
   public function getAttendanceByOneDate(Request $request, $date){
    $attendance=Attendance::where("date", $date)->with(["student", "section", "classes"])->get();
    $sections=Section::with(["Class"])->get();
    $classes=Classes::get();
    return response()->json([
        "message"=> "Date Attendance Records",
        "Attendance"=>$attendance,
        "section_filters"=>$sections,
        "class_filters"=>$classes
    ]);
}
public function getAttendanceByClassWithToday(Request $request, $date, $id){

}
public function getAttendanceByStudentWithDate(Request $request, $id, $edate, $sdate){
    $attendance=Attendance::where("student_id", $id)->whereBetween("date", ["2023-03-01","2023-03-05"])->with(["student", "section", "classes"])->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$attendance
    ]);
}
public function getAttendanceBySectionWithDate(Request $request, $id, $edate, $sdate){
    $attendance=Attendance::where("section_id", $id)->whereBetween("date",[$sdate, $edate])->with(["student", "section", "classes"])->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$attendance
    ]);
}
public function getAttendanceByClassWithDate(Request $request, $class_id, $edate, $sdate){
    $attendance=Attendance::where("class_id", $class_id)->whereBetween("date",[$sdate, $edate])->with(["student", "section", "classes"])->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$attendance
    ]);
}
//updateAttendanceBySSD

}
