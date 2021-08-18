<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use stdClass;

class ConfigController extends Controller
{
  public function getSubscriptions(Request $request)
  {
    $duration = $request->subscription;

    $mealBoxes[0]['id'] = 'nonVeg';
    $mealBoxes[0]['name'] = 'Non Veg Meal Box';
    $mealBoxes[0]['price'] = 1500;
    // $mealBoxes[0]['url'] = 'require \'assets/images/nonVegOnly.jpg\'';

    $mealBoxes[1]['id'] = 'veg';
    $mealBoxes[1]['name'] = 'Veg Meal Box';
    $mealBoxes[1]['price'] = 800;
    // $mealBoxes[1]['url'] = 'require \'assets/images/nonVegOnly.jpg\'';

    $mealBoxes[2]['id'] = 'mixed';
    $mealBoxes[2]['name'] = 'Mixed Meal Box';
    $mealBoxes[2]['price'] = 1200;
    // $mealBoxes[2]['url'] = 'require \'assets/images/nonVegOnly.jpg\'';

    return response()->json($mealBoxes);
  }

  public function getDates(Request $request)
  {
    $duration = $request->subscription;

    $date = date("Y-m-d");
    $date = strtotime($date);

    $tomorrow = strtotime("+1 day", $date);
    $endDate = strtotime("+$duration day", $date);

    $today = date('d-m-Y', $date);
    $endDate = date('d-m-Y', $endDate);
    $tomorrow = date('d-m-Y', $tomorrow);

    return response()->json([
      'today' => $today,
      'tomorrow' => $tomorrow,
      'endDate' => $endDate,
      'duration' => $duration,
    ]);
  }

  public function getLocationInfo()
  {
    //maxDistance in metres
    $maxDistance = 5000;
    $baseCoordinates = new stdClass(); //creates new object

    // $baseCoordinates->latitude = 26.1285;
    // $baseCoordinates->longitude = 91.9023;

    $baseCoordinates->latitude = 26.129474177619944;
    $baseCoordinates->longitude = 91.62048289552331;

    return response()->json([
      'baseCoordinates' => $baseCoordinates,
      'maxDistance' => $maxDistance,
    ]);
  }

  public function getToday(Request $request)
  {
    $startDate = $request->startDate;
    $date = date("Y-m-d");
    $date = strtotime($date);
    $today = date('d-m-Y', $date);
    // if ($startDate == $today) {
    //   // return response()->json($today);
    // }
    return response()->json($today);
  }

  public function getTime(Request $request)
  {
    $date = date("Y-m-d");
    $time = date("h:i:sa");
    $time = date("H");

    return response()->json($time);
  }

  public function index()
  {
    //
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
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
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
    //
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