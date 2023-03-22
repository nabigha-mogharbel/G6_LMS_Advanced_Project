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
    public function listClassesSections(Request $request){
        $allClases=Classes::get();
        $allSections=Section::get();
        $classRefs=[];
        $sectionRef=[];
        foreach($allClases as $class){
            $classRefs[$class->id]=$class->name;
        }
        foreach($allSections as $section){
            $sectionRef[$section->id]=["name"=>$section->name, "class_id"=>$section->class_id];
        }
        $bb=Attendance::where("date","2023-03-18")->get()->groupBy("class_id","section_id");
        return response()->json(["classes"=>$classRefs, "sections"=>$sectionRef, "bb"=>$bb]);
    }
    public function generateAttendance(Request $request, $date){
        $check=Attendance::where("date",$date)->get();
        if($check->isEmpty()){
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
        } else{
        
    return response()->json([
        "message"=> "Records exist",
        
    ]);
}
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
        $late=Attendance::where("date", $date)->where("status", "late")->count();
        $absent=Attendance::where("date", $date)->where("status", "absent")->count();
        $null=Attendance::where("date", $date)->where("status", "null")->count();
        $bySection=Attendance::where("date", $date)->get()->groupBy("section_id");
        $sectionsStat=[];
        $nbOfSections=$bySection->count();
        $template=[
            "labels"=> [],
            "datasets"=> [
              [
                "label"=> 'Present',
                "data"=> [],
                "backgroundColor"=>"#8A70D6"
              ],
              [
                "label"=> 'Late',
                "data"=> [],
                "backgroundColor"=>"#579BE4"
            ],
              [
                "label"=> 'Absent',
                "data"=>[],
                "backgroundColor"=>"#FFA600"
            ],[
                "label"=> 'Unknown',
                "data"=> [],
                "backgroundColor"=>"#c4c4c4"
            ],
            ]
    ];
    $Filtered=[
    ];
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
        $template2=[
            "labels"=> [],
            "datasets"=> [
              [
                "label"=> 'Present',
                "data"=> [],
                "backgroundColor"=>"#8A70D6"
              ],
              [
                "label"=> 'Late',
                "data"=> [],
                "backgroundColor"=>"#579BE4"
            ],
              [
                "label"=> 'Absent',
                "data"=>[],
                "backgroundColor"=>"#FFA600"
            ],[
                "label"=> 'Unknown',
                "data"=> [],
                "backgroundColor"=>"#c4c4c4"
            ],
            ]
        ];

        foreach($byClass as $key => $val){
            $name=Classes::where("id", $key)->get();
            $presentByCla=Attendance::where("date", $date)->where("class_id", $key)->where("status", "present")->count();
            $lateByCla=Attendance::where("date", $date)->where("class_id", $key)->where("status", "late")->count();
            $absentByCla=Attendance::where("date", $date)->where("class_id", $key)->where("status", "absent")->count();
            $nullByCla=Attendance::where("date", $date)->where("class_id", $key)->where("status", "null")->count();
            $stat=["present"=>$presentByCla, "late"=>$lateByCla, "absent" => $absentByCla, "null"=>$nullByCla];
            $classStat[$key]=$stat;
            array_push($template["labels"], $name[0]->name);
            array_push($template["datasets"][0]["data"], $presentByCla);
            array_push($template["datasets"][1]["data"], $lateByCla);
            array_push($template["datasets"][2]["data"], $absentByCla);
            array_push($template["datasets"][3]["data"], $nullByCla);
            $groupedbySection=Attendance::where("date", $date)->where("class_id", $key)->get()->groupBy("section_id");
            foreach($groupedbySection as $key2 => $val2){
                            $name=Section::where("id", $key2)->get();
            $presentByCla=Attendance::where("date", $date)->where("section_id", $key2)->where("status", "present")->count();
            $lateByCla=Attendance::where("date", $date)->where("section_id", $key2)->where("status", "late")->count();
            $absentByCla=Attendance::where("date", $date)->where("section_id", $key2)->where("status", "absent")->count();
            $nullByCla=Attendance::where("date", $date)->where("section_id", $key2)->where("status", "null")->count();
            array_push($template2["labels"], $name[0]->name);
            array_push($template2["datasets"][0]["data"], $presentByCla);
            array_push($template2["datasets"][1]["data"], $lateByCla);
            array_push($template2["datasets"][2]["data"], $absentByCla);
            array_push($template2["datasets"][3]["data"], $nullByCla);
            }
            array_push($Filtered, [$key=>$template2]);

        }
        $allClases=Classes::get();
        $allSections=Section::get();
        $classRefs=[];
        $sectionRef=[];
        foreach($allClases as $class){
            $classRefs[$class->id]=$class->name;
        }
        foreach($allSections as $section){
            $sectionRef[$section->id."#".$section->class_id]=["name"=>$section->name, "class_id"=>$section->class_id];
        }
        return response()->json(["late"=>$late, "absent"=>$absent,"null"=>$null, "groupedBySection"=>$sectionsStat, "groupedByClass"=>$classStat, "classRef"=>$classRefs, "sectionRef"=> $sectionRef, "chart"=>$template, "filtered"=>$Filtered]);
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
   public function getAttendanceByClass(Request $request, $class_id, $date){
    $attendance=Attendance::where("class_id", $class_id)->where("date", $date)->with(["student", "section", "classes"])->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$attendance
    ]);


}
public function getAttendanceReportByClass(Request $request, $class_id){
    $template=[
        "labels"=> [],
        "datasets"=> [
          [
            "label"=> 'Present',
            "data"=> [],
            "backgroundColor"=>"#8A70D6"
          ],
          [
            "label"=> 'Late',
            "data"=> [],
            "backgroundColor"=>"#579BE4"
        ],
          [
            "label"=> 'Absent',
            "data"=>[],
            "backgroundColor"=>"#FFA600"
        ],[
            "label"=> 'Unknown',
            "data"=> [],
            "backgroundColor"=>"#c4c4c4"
        ],
        ]
    ];
    $data=[];
    $total=[];
    $present=Attendance::where("class_id", $class_id)->where("status", "present")->count();
    $late=Attendance::where("class_id", $class_id)->where("status", "late")->count();
    $absent=Attendance::where("class_id", $class_id)->where("status", "absent")->count();
    $null=Attendance::where("class_id", $class_id)->where("status", "null")->count();
    $classRecords=Attendance::where("class_id", $class_id)->get()->groupBy("section_id");
    foreach($classRecords as $key => $val){
        $byStudents=Attendance::where("section_id",$key)->get()->groupBy("student_id");
        foreach($byStudents as $key2 => $val){
        $presentByStu=Attendance::where("student_id", $key2)->where("status", "present")->count();
        $lateByStu=Attendance::where("student_id", $key2)->where("status", "late")->count();
        $absentByStu=Attendance::where("student_id", $key2)->where("status", "absent")->count();
        $nullByStu=Attendance::where("student_id", $key2)->where("status", "null")->count();
        $totalByStu=$presentByStu+$lateByStu+$nullByStu+$absentByStu;
        $name=Student::where("id", $key2)->get();
        array_push($data, ["student_id"=> $key2, "student_name"=> $name[0]->first_name." ".$name[0]->last_name, "present"=>$presentByStu, "avrP"=> $presentByStu/$totalByStu,"absent"=> $absentByStu, "avrA"=> $absentByStu/$totalByStu, "late"=>$lateByStu, "avrL"=> $lateByStu/$totalByStu, "null"=>$nullByStu, "avrN"=> $nullByStu/$totalByStu]);
        array_push($template["datasets"][0]["data"], $presentByStu);
        array_push($template["datasets"][1]["data"], $lateByStu);
        array_push($template["datasets"][2]["data"], $absentByStu);
        array_push($template["datasets"][3]["data"], $nullByStu);
        array_push($template["labels"], $name[0]->first_name." ".$name[0]->last_name);
        }
        $avrPresent=array_sum($template["datasets"][0]["data"])/count($template["datasets"][0]["data"]);
        $avrLate=array_sum($template["datasets"][1]["data"])/count($template["datasets"][1]["data"]);
        $avrAbsent=array_sum($template["datasets"][2]["data"])/count($template["datasets"][2]["data"]);
        $avrNull=array_sum($template["datasets"][3]["data"])/count($template["datasets"][3]["data"]);
        array_push($total, ["section_id"=>$key, "present"=>$avrPresent, "absent"=>$avrAbsent, "late"=>$avrLate, "null"=>$avrNull]);
       // $stat=["present"=>$presentBySec, "late"=>$lateBySec, "absent" => $absentBySec, "null"=>$nullBySec];
        //$sectionsStat[$key]=$stat;
    }
    /*foreach ($classRecords as $key => $value){
        $index=$sectionCol->id;
       array_push($attendance,Attendance::where("section_id", $index)->with(["student", "section", "classes"])->get() );
    };*/
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$data,
        "total"=>$total
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
   /* $attendance=Attendance::where("class_id", $class_id)->whereBetween("date",[$sdate, $edate])->with(["student", "section", "classes"])->get();
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$attendance
    ]);*/

    $template=[
        "labels"=> [],
        "datasets"=> [
          [
            "label"=> 'Present',
            "data"=> [],
            "backgroundColor"=>"#8A70D6"
          ],
          [
            "label"=> 'Late',
            "data"=> [],
            "backgroundColor"=>"#579BE4"
        ],
          [
            "label"=> 'Absent',
            "data"=>[],
            "backgroundColor"=>"#FFA600"
        ],[
            "label"=> 'Unknown',
            "data"=> [],
            "backgroundColor"=>"#c4c4c4"
        ],
        ]
    ];
    $data=[];
    $total=[];
    $edate=date($edate);
    $sdate=date($sdate);
    $classRecords=Attendance::where("class_id", $class_id)->whereBetween("date", [$edate, $sdate])->get()->groupBy("section_id");
    foreach($classRecords as $key => $val){
        $byStudents=Attendance::where("section_id",$key)->whereBetween("date", [$edate,$sdate])->get()->groupBy("student_id");
        foreach($byStudents as $key2 => $val){
        $presentByStu=Attendance::where("student_id", $key2)->whereBetween("date", [$edate,$sdate])->where("status", "present")->count();
        $lateByStu=Attendance::where("student_id", $key2)->whereBetween("date", [$edate,$sdate])->where("status", "late")->count();
        $absentByStu=Attendance::where("student_id", $key2)->whereBetween("date", [$edate,$sdate])->where("status", "absent")->count();
        $nullByStu=Attendance::where("student_id", $key2)->whereBetween("date", [$edate,$sdate])->where("status", "null")->count();
        $totalByStu=$presentByStu+$lateByStu+$nullByStu+$absentByStu;
        if($totalByStu===0){$totalByStu=1;}
        $totalByStu=1;
        $name=Student::where("id", $key2)->get();
        array_push($data, ["student_id"=> $key2, "student_name"=> $name[0]->first_name." ".$name[0]->last_name, "present"=>$presentByStu, "avrP"=> $presentByStu/$totalByStu,"absent"=> $absentByStu, "avrA"=> $absentByStu/$totalByStu, "late"=>$lateByStu, "avrL"=> $lateByStu/$totalByStu, "null"=>$nullByStu, "avrN"=> $nullByStu/$totalByStu]);
        }
        /*$avrPresent=array_sum($template["datasets"][0]["data"])/count($template["datasets"][0]["data"]);
        $avrLate=array_sum($template["datasets"][1]["data"])/count($template["datasets"][1]["data"]);
        $avrAbsent=array_sum($template["datasets"][2]["data"])/count($template["datasets"][2]["data"]);
        $avrNull=array_sum($template["datasets"][3]["data"])/count($template["datasets"][3]["data"]);
        array_push($total, ["section_id"=>$key, "present"=>$avrPresent, "absent"=>$avrAbsent, "late"=>$avrLate, "null"=>$avrNull]);*/
    }
    return response()->json([
        "message"=> "Student Attendance Records",
        "Attendance"=>$data,
        "total"=>$total,
        "edate"=>$edate,
        "sdate"=>$sdate
    ]);
}

}
