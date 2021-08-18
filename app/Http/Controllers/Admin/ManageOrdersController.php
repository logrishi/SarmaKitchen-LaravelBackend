<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateDeliveryOtp;
use App\Http\Requests\ValidateUpdateOrderCourier;
use App\Http\Requests\ValidateUpdateOrderStatus;
use App\Http\Requests\ValidateUpdateSubscriptionItems;
use App\Models\Order;
use App\User;
use App\Models\OrderSubscriptionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

  public function updateOrderCourier(ValidateUpdateOrderCourier $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $orderId = $validated['order_id'];
      $delivered_by = $validated['delivered_by'];

      Order::where('id', $orderId)->update([
        'delivered_by' => $delivered_by,
      ]);
      $orders = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
        ->where('order_status', '!=', 'Delivered')
        ->orderBy('id', 'desc')
        ->get();
      // return response()->json("Courier Updated");
      return response()->json($orders);
    }
  }
  public function updateOrderStatus(ValidateUpdateOrderStatus $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $orderId = $validated['order_id'];
      $orderStatus = $validated['order_status'];
      $orderStatusCode = $validated['order_status_code'];

      Order::where('id', $orderId)->update([
        'order_status' => $orderStatus,
        'order_status_code' => $orderStatusCode,
      ]);
      return response()->json("Order Status Updated");
    }
  }

  public function updateSubscriptionItems(
    ValidateUpdateSubscriptionItems $request
  ) {
    $validated = $request->validated();
    if ($validated) {
      $product_delivered = $validated['product_delivered'];
      $today = $validated['today'];
      $orderId = $validated['orderId'];
      $orderStatus = $validated['order_status'];
      $delivered_by = $validated['delivered_by'];

      OrderSubscriptionStatus::where('order_id', $orderId)
        ->where('delivery_date', $today)
        ->update([
          'product_delivered' => $product_delivered,
          'order_status' => $orderStatus,
          'delivered_by' => $delivered_by,
        ]);

      $orders = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
        ->where('order_status', '!=', 'Delivered')
        ->orderBy('id', 'desc')
        ->get();
      return response()->json($orders);
      // return response()->json('Insert Successful');
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
    $date = date("Y-m-d");
    $name = 'Boy1';
    // $orders1 = Order::with('orderItems', 'OrderSubscriptionStatus', 'user')
    $orders = Order::with('orderItems', 'user', 'orderSubscriptionStatus')
      ->where('delivered_by', 'Boy1')
      ->where('order_status', '!=', "Delivered")
      ->orWhereHas('orderSubscriptionStatus', function ($query) use (
        $name,
        $date
      ) {
        $query->where('delivered_by', 'Boy1');
        $query->where('delivery_date', '2020-11-28');
      })
      ->get();

    // foreach($orders as $order){

    // }

    // $orders2 = Order::with('orderItems', 'user')
    //   ->join(
    //     'order_subscription_statuses',
    //     'order_subscription_statuses.order_id',
    //     'orders.id'
    //   )
    //   ->where('order_subscription_statuses.delivered_by', $name)
    //   ->where('order_subscription_statuses.delivery_date', $date)
    //   ->where('order_subscription_statuses.order_status', '!=', 'Delivered')
    //   ->get();

    $orders2Ids = OrderSubscriptionStatus::where('delivered_by', 'Boy1')
      ->where('order_status', '!=', "Delivered")
      ->pluck('order_id')
      ->toArray();

    $orders4 = Order::with('orderItems', 'user')
      ->whereIn('id', $orders2Ids)
      // ->where('order_status', '!=', "Delivered")
      ->get();
    // return $orders;

    $orders3 = DB::select(
      "select * from `orders` o inner join `order_subscription_statuses` oss on oss.order_id = o.id 
      LEFT JOIN (select oi.order_id, 
      json_arrayagg(json_object ('id', oi.id, 'product_id', oi.product_id, 'product_name', oi.product_name, 'price', oi.price, 'quantity', oi.quantity,
      'price', oi.price, 'size', oi.size, 'note', oi.note, 'is_veg', oi.is_veg, 'rating', oi.rating, 'meal_type', oi.meal_type, 
      'subscription_duration', oi.subscription_duration, 'num_persons', oi.num_persons, 'start_date', oi.start_date, 'end_date', oi.end_date, 
      'subscription_menu', oi.subscription_menu, 'created_at', oi.created_at)) order_items from order_items oi
      GROUP BY 
      oi.order_id)
      
      ois on ois.order_id = o.id where oss.delivered_by = 'Boy1' and oss.delivery_date ='2021-05-16' and oss.order_status != 'Delivered'"
    );

    $orderNew = DB::select(
      `SELECT orders.id, orders.address, orders.total_price, orders.order_status, orders.delivered_by, JSON_ARRAYAGG(json_object(
    'order_items_id', order_items.id, 
    'order_items_order_id', order_items.order_id, 
    'order_items_product_name', order_items.product_name, 
    'order_items_subscription_duration', order_items.subscription_duration))  AS 'order_items',
JSON_ARRAYAGG(json_object(
    'order_subscription_statuses_id', order_subscription_statuses.id,
    'order_subscription_statuses_order_id', order_subscription_statuses.order_id,
    'order_subscription_statuses_order_status', order_subscription_statuses.order_status,
    'order_subscription_statuses_product_delivered', order_subscription_statuses.product_delivered,
    'order_subscription_statuses_delivered_by', order_subscription_statuses.delivered_by))  AS 'order_subscription_statuses'
FROM 'orders'
INNER JOIN order_items ON orders.id = order_items.order_id
LEFT JOIN order_subscription_statuses ON orders.id = order_subscription_statuses.order_id
WHERE orders.delivered_by = 'Boy1' OR order_subscription_statuses.delivered_by = 'Boy1'
GROUP BY orders.id`
    );
    return $orderNew;
    //  LEFT JOIN (select oi.order_id,
    //       json_arrayagg(json_object ('id', u.id, 'name', u.name, 'email', u.email, 'phone', u.phone, 'is_admin', u.is_admin,
    //       'is_courier', u.is_courier,)) users from users u

    // $collection = $orders1->merge($orders2);
    // return $orders1;
    // return response()->json($collection);
    // return response()->json([
    //   'singleOrder' => $orders1,
    //   'subscriptionOrder' => $orders2,
    // ]);
  }

  public function verifyOtp(ValidateDeliveryOtp $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $name = auth('api')->user()->name;
      $orderId = $validated['order_id'];
      $today = $validated['today'];
      $subscriptionDuration = $validated['subscription_duration'];
      $otp = $validated['otp'];

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
          ->toSql();
        // ->get('otp');
        return $storedOtp;

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