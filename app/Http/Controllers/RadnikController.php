<?php

namespace App\Http\Controllers;

use App\Kartica;
use App\Radnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RadnikController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Radnik::all(['id','ime','prezime','komentar','created_at']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Radnik::with('kartice')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'ime' => 'required_if:prezime,',
            'prezime' => 'required_if:ime,',
            'sluzba' => 'required'   //TODO: dodaj i druge validacije!!!
        ],[
            'ime.required_if' => 'ime ili prezime su neophodni',
            'prezime.required_if' => 'ime ili prezime su neophodni',
            'sluzba.required' => 'sluzba je neophodna'
        ]);
        $id=0;

        DB::transaction(function () use(&$id, $request) {    //TODO: throw u slucaju greske
            $radnik = Radnik::create($request->all());

            $karticeArray = $request->input('kartice');
            if(is_null($karticeArray)) $karticeArray=[];
            $karticeModels = [];
            foreach ($karticeArray as $kartica)
            {
                $karticeModels[] = new Kartica($kartica);
            }

            $radnik->kartice()->saveMany($karticeModels);

            $id = $radnik->id;
        });

        return response(json_encode(['message'=>'Success!', 'id'=>$id]), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'ime' => 'required_if:prezime,',
            'prezime' => 'required_if:ime,',
            'sluzba' => 'required'   //TODO: dodaj i druge validacije!!!
            //'kartice.*.kod' => 'required_unless:kartice.*.id,'
        ]/*,[
            'required_if' => ':attribute je neophodno ako :other nije upisano',
        ]*/,[
            'ime.required_if' => 'ime ili prezime su neophodni',
            'prezime.required_if' => 'ime ili prezime su neophodni',
            'sluzba.required' => 'sluzba je neophodna'
            //'kartice.*.kod.required_unless' => 'kod ne smije biti prazan'
        ]);

        $radnik = Radnik::find($id);
        if(!$radnik) return response(json_encode(['message'=>'Not Found!']), 404)->header('Content-Type', 'application/json');

        DB::beginTransaction();

        $success = $radnik->update($request->all());

        $karticeArray = $request->input('kartice');
        if(is_null($karticeArray)) $karticeArray=[];
        $karticeModels = [];

        foreach ($karticeArray as $kartica)
        {
            if(!$kartica['id'] && ($kartica['kod'] != '')) {
                $karticeModels[] = new Kartica($kartica);
            }
            else if($kartica['kod'] != '')
            {
                $karticaModel = Kartica::find($kartica['id']);
                //TODO: postoji li kartica?
                //TODO: pripada li ovom radniku?
                $success = $success && $karticaModel->update($kartica);
                //TODO: touch parent timestamps??
            }
        }

        $radnik->kartice()->saveMany($karticeModels);

        //TODO: rollback u slucaju greske

        DB::commit();

        if($success) return response(json_encode(['message'=>'Success!']), 200)->header('Content-Type', 'application/json');
        else return response(json_encode(['message'=>'Failed!']), 500)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
