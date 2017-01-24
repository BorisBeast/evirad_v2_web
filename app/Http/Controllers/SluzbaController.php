<?php

namespace App\Http\Controllers;

use App\Sluzba;
use Illuminate\Http\Request;

class SluzbaController extends Controller
{
    public function index()
    {
        return Sluzba::all(['id', 'ime']);
    }

    public function show($id)
    {
        return Sluzba::with('radnici')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'ime' => 'required',
        ],[
            'ime.required' => 'ime je neophodno',
        ]);

        $sluzba = Sluzba::create($request->all());
        return response(json_encode(['message'=>'Success!', 'id'=>$sluzba->id]), 200)->header('Content-Type', 'application/json');
    }
}
