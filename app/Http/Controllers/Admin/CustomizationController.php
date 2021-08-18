<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateCustomization;
use App\Models\Customization;
use Illuminate\Http\Request;

class CustomizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customizations = Customization::all();
        // $val = $customizations->pluck('options');
        return response()->json($customizations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidateCustomization $request)
    {
        $validated = $request->validated();
        
        if($validated){
            Customization::create($validated);
            return response()->json('Insert Successful');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customization  $customization
     * @return \Illuminate\Http\Response
     */
    public function show(Customization $customization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customization  $customization
     * @return \Illuminate\Http\Response
     */
    public function edit(Customization $customization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customization  $customization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customization $customization)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customization  $customization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customization $customization)
    {
        //
    }
}