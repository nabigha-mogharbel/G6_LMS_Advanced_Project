<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Detail;

class DetailController extends Controller
{
    
    public function AddDetail(Request $request){
        $detail= new Detail();
        $type =$request->input('type');
        $requirements=$request->input('requirements');
        $detail-> type = $type;
        $detail-> requirements = $requirements;
        $detail->save();
        return response()->json([
            "message"=>"requirment added successfully"
        ]);
    }

    public function  GetDetail(Request $request){

        $class= Detail::get();
        return response()->json([
            "message"=>$class]);

    }

    public function getDetailById(Request $request, $id){
        $Detail=Detail::where('id',$id)->get();
        return response()->json([
            "message"=>$Detail
        ]);
    }


    public function getDetailByType(Request $request, $type){
        $Detail=Detail::where('type',$type)->get();
        return response()->json([
            "message"=>$Detail
        ]);
    }

    public function deleteDetail(Request $request, $id){
        $Detail=Detail::find($id);
        $Detail->delete();
        return response()->json([
            "message"=> "Detail Deleted Successfully!"
        ]);
    }


    public function editDetail(Request $request, $id){
        $Detail =  Detail::find($id);
        $inputs= $request->except('_method');
        $Detail->update($inputs);
        return response()->json([
            'message' => 'Detail edited successfully!',
            'Detail' => $Detail,
        ]);
   }
  






}
