<?php

namespace App\Http\Controllers;

use App\Grupa;
use App\Interval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupaController extends Controller
{
    public function index()
    {
        return Grupa::all(['id', 'ime']);
    }

    public function show($id)
    {
        return Grupa::with(['intervali', 'intervali.zone'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'ime' => 'required',
        ],[
            'ime.required' => 'ime je neophodno',
        ]);
        $id=0;

        DB::transaction(function () use(&$id, $request) {    //TODO: throw u slucaju greske
            $grupa = Grupa::create($request->all());

            $intervaliArray = $request->input('intervali');
            if(is_null($intervaliArray)) $intervaliArray=[];
            foreach ($intervaliArray as $interval)
            {
                $intervalModel = new Interval($interval);
                $grupa->intervali()->save($intervalModel);
                if(!array_key_exists('zone',$interval)) $interval['zone']=[];
                $intervalModel->zone()->sync($interval['zone']);
            }

            $id = $grupa->id;
        });

        return response(json_encode(['message'=>'Success!', 'id'=>$id]), 200)->header('Content-Type', 'application/json');
    }
}
