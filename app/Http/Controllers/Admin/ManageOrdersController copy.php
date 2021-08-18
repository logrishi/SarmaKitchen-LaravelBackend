<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateSubscriptionUpdate;
use App\Models\Order;
use App\User;
use App\Models\OrderSubscriptionStatus;
use Illuminate\Http\Request;

class ManageOrdersController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $orders = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
      ->where('order_status', '!=', 'Delivered')
      ->orderBy('id', 'desc')
      ->get();
    return response()->json($orders);
  }

  public function getPastOrders()
  {
    $orders = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
      ->where('order_status', '=', 'Delivered')
      ->orderBy('id', 'desc')
      ->get();
    return response()->json($orders);
  }

  public function updateOrderCourier(Request $request)
  {
    $orderId = $request->order_id;
    $delivered_by = $request->delivered_by;

    Order::where('id', $orderId)->update([
      'delivered_by' => $delivered_by,
    ]);
    return response()->json("Courier Updated");
  }

  public function updateOrderStatus(Request $request)
  {
    $orderId = $request->order_id;
    $orderStatus = $request->order_status;
    $orderStatusCode = $request->order_status_code;

    Order::where('id', $orderId)->update([
      'order_status' => $orderStatus,
      'order_status_code' => $orderStatusCode,
    ]);
    return response()->json("Order Status Updated");
  }

  public function updateSubscriptionItems(ValidateSubscriptionUpdate $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $product_delivered = $request->product_delivered;
      $today = $request->today;
      $orderId = $request->orderId;
      $orderStatus = $request->order_status;
      $delivered_by = $request->delivered_by;

      OrderSubscriptionStatus::where('order_id', $orderId)
        ->where('delivery_date', $today)
        ->update([
          'product_delivered' => $product_delivered,
          'order_status' => $orderStatus,
          'delivered_by' => $delivered_by,
        ]);

      return response()->json('Insert Successful');
    }
  }

  public function getCourier()
  {
    $courier = User::where('is_courier', '1')->get();
    return response()->json($courier);
  }

  ///// courier side code not admin side code -- below
  public function getOrdersByCourier()
  {
    // $name = auth('api')->user()->name;
    // $orderProperties = [
    //   'orders.*',
    //   'orders.id as order_id',
    //   'orders.address as order_address',
    //   'orders.total_price as order_total_price',
    //   'orders.order_status as order_order_status',
    //   'orders.delivered_by as order_delivered_by',
    //   'orders.created_at as order_created_at',

    //   'order_items.id as order_items_id',
    //   'order_items.order_id as order_items_order_id',
    //   'order_items.product_id as order_items_product_id',
    //   'order_items.product_name as order_items_product_name',
    //   'order_items.price as order_items_price',
    //   'order_items.quantity as order_items_quantity',
    //   'order_items.size as order_items_size',
    //   'order_items.note as order_items_note',

    //   'order_subscription_statuses.id as order_status_id',
    //   'order_subscription_statuses.order_id as order_status_order_id',
    //   'order_subscription_statuses.delivery_date as order_status_delivery_date',
    //   'order_subscription_statuses.order_status as order_status_order_status',
    //   'order_subscription_statuses.product_delivered as order_status_product_delivered',
    //   'order_subscription_statuses.delivered_by as order_status_delivered_by',
    //   'order_subscription_statuses.otp as order_status_otp',
    //   'order_subscription_statuses.remarks as order_status_remarks',
    // ];

    // $orders = Order::join('order_items', 'order_items.order_id', 'orders.id')
    //   ->join(
    //     'order_subscription_statuses',
    //     'order_subscription_statuses.order_id',
    //     'orders.id'
    //   )
    //   ->select($orderProperties)
    //   ->where('orders.order_delivered_by', $name)
    //   // ->where('order_subscription_statuses.delivered_by', $name)
    //   // ->orderBy('id', 'desc')
    //   ->get();

    // $arr = [];
    // foreach ($orders as $orderItems) {
    //   $arr['order_items'] = [
    //     'order_items_id' => $orderItems['order_items_id'],
    //     'order_items_order_id' => $orderItems['order_items_order_id'],
    //     'order_items_product_id' => $orderItems['order_items_product_id'],
    //   ];
    // return response()->json($orderItems);
    // }
    // return response()->json($orders);

    // $name = auth('api')->user()->name;
    // $orders = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
    //   ->where('orders.delivered_by', '=', $name)
    //   // ->where('OrderSubscriptionStatus.delivered_by', '=', $name)
    //   ->orderBy('id', 'desc')
    //   ->get();
    // return response()->json($orders);

    $name = auth('api')->user()->name;
    // $orders = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
    //   ->where('delivered_by', '=', $name)
    //   ->whereHas('OrderSubscriptionStatus', function ($query) use ($name) {
    //     $query->where('delivered_by', '=', $name);
    //   })
    //   ->get();
    $date = date("Y-m-d");

    // $orders1 = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
    $orders1 = Order::with('orderItems', 'user')
      ->where('delivered_by', $name)
      ->where('order_status', '!=', "Delivered")
      // ->whereHas('OrderSubscriptionStatus', function ($query) use ($name) {
      // $query->where('delivered_by', '=', $name);
      // })
      ->get();

    // $orders2 = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
    //   // ->where('delivered_by', '=', $name)
    //   ->whereHas('OrderSubscriptionStatus', function ($query) use ($name) {
    //     $query->where('delivered_by', $name);
    //   })
    //   ->get();

    $orders2 = Order::with('orderItems', 'user')
      ->join(
        'order_subscription_statuses',
        'order_subscription_statuses.order_id',
        'orders.id'
      )
      ->where('order_subscription_statuses.delivered_by', $name)
      ->where('order_subscription_statuses.delivery_date', $date)
      ->where('order_subscription_statuses.order_status', '!=', 'Delivered')
      ->get();

    // $orders2 = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
    //   ->whereHas('OrderSubscriptionStatus', function ($query) use ($name) {
    //     $query->where('delivered_by', $name);
    //   })
    //   ->whereHas('OrderSubscriptionStatus', function ($query) use ($date) {
    //     $query->where('delivery_date', $date);
    //   })
    //   ->get();

    // $orders2 = Order::whereHas('OrderSubscriptionStatus', function (
    //   $query
    // ) use ($name) {
    //   $query->where('delivered_by', $name);
    // })
    // ->whereHas('OrderSubscriptionStatus', function ($query) use ($date) {
    //   $query->where('delivery_date', $date);
    // })->get();

    // $orders2 = Order::whereHas('OrderSubscriptionStatus', function (
    //   $query
    // ) use ($name) {
    //   $query->where('delivered_by', $name);
    // })
    //   ->whereHas('OrderSubscriptionStatus', function ($query) use (
    //     $date,
    //     $name
    //   ) {
    //     $query->where('delivery_date', $date)->where('delivered_by', $name);
    //   })
    //   ->get();

    // with([
    //   'orderItems',
    //   'user',
    //   'OrderSubscriptionStatus' => function ($query) use ($name, $date) {
    //     return $query->where('delivered_by', $name);
    //     // ->where('delivery_date', $date);
    //   },
    // ])->get();

    $collection = $orders1->merge($orders2);
    // return $orders2;
    return response()->json($collection);
    // return response()->json([
    //   'singleOrder' => $orders1,
    //   'subscriptionOrder' => $orders2,
    // ]);

    // $name = auth('api')->user()->name;
    // $orders = Order::with('orderItems', 'orderSubscriptionStatus', 'user')
    //     'order_subscription_statuses.order_id',
    //   ->join(
    //     'order_subscription_statuses',
    //     'orders.id'
    //   )
    //   ->where('orders.delivered_by', '=', $name)
    //   // ->where('order_subscription_statuses.delivered_by', '=', $name)
    //   ->get();
    // return response()->json($orders2);
  }

  public function verifyOtp(Request $request)
  {
    $name = auth('api')->user()->name;
    $orderId = $request->orderId;
    $today = $request->today;
    $subscriptionDuration = $request->subscriptionDuration;
    $otp = $request->otp;

    if ($subscriptionDuration == 1) {
      $storedOtp = Order::where('id', $orderId)->get('otp');

      if ($storedOtp[0]['otp'] == $otp) {
        Order::where('id', $orderId)->update([
          'order_status' => 'Delivered',
          'order_status_code' => 3,
        ]);
        return response()->json('Insert Successful');
      } else {
        return response()->json(['OtpError' => "OTP doesn't match"]);
      }
    } else {
      $storedOtp = OrderSubscriptionStatus::where('order_id', $orderId)
        ->where('delivery_date', $today)
        ->where('delivered_by', $name)
        ->get('otp');

      if ($storedOtp[0]['otp'] == $otp) {
        OrderSubscriptionStatus::where('order_id', $orderId)
          ->where('delivery_date', $today)
          ->where('delivered_by', $name)
          ->update([
            'order_status' => 'Delivered',
          ]);
        return response()->json('Insert Successful');
      } else {
        return response()->json(['OtpError' => "OTP doesn't match"]);
      }
    }
  }

  public function getDeliveredOrdersByCourier()
  {
    $name = auth('api')->user()->name;
    $orders1 = Order::with('orderItems', 'user')
      ->where('delivered_by', $name)
      ->where('order_status', "Delivered")
      ->get();
    $orders2 = Order::with('orderItems', 'user')
      ->join(
        'order_subscription_statuses',
        'order_subscription_statuses.order_id',
        'orders.id'
      )
      ->where('order_subscription_statuses.delivered_by', $name)
      ->where('order_subscription_statuses.order_status', 'Delivered')
      ->get();
    $collection = $orders1->merge($orders2);
    return response()->json($collection);
  }
  ///// courier side code not admin side code -- above

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