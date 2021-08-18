<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSubscriptionStatus;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Throwable;

class OrderController extends Controller
{
  public function index()
  {
    $userId = auth('api')->user()->id;
    $orders = Order::with('orderItems')
      ->with('orderSubscriptionStatus')
      ->where('user_id', '=', $userId)
      ->orderBy('order_status_code', 'asc')
      ->orderBy('id', 'desc')
      ->get();
    return response()->json($orders);
    // $orderProperties = [
    //   // 'orders.*',
    //   'orders.id as order_id',
    //   'orders.address as order_address',
    //   'orders.total_price as order_total_price',
    //   'orders.payment_id as order_payment_id',
    //   'orders.receipt as order_receipt',
    //   'orders.sub_category as order_sub_category',
    //   'orders.is_one_time_order as order_is_one_time_order',
    //   'orders.subscription_duration as order_subscription_duration',
    //   'orders.veg_only as order_veg_only',
    //   'orders.num_persons as order_num_persons',
    //   'orders.order_status as order_order_status',
    //   'orders.created_at as order_created_at',

    //   'order_items.id as order_items_id',
    //   'order_items.order_id as order_items_order_id',
    //   'order_items.product_id as order_items_product_id',
    //   'order_items.product_details_id as order_items_product_details_id',
    //   'order_items.product_name as order_items_product_name',
    //   'order_items.quantity as order_items_quantity',
    //   'order_items.price as order_items_price',
    //   'order_items.size as order_items_size',
    //   'order_items.note as order_items_note',
    //   'order_items.is_customizable as order_items_is_customizable',
    //   'order_items.selected_customizations as order_items_selected_customizations',
    // ];

    // $orders = Order::join('order_items', 'order_items.order_id', 'orders.id')
    //   ->select($orderProperties)
    //   ->where('user_id', $userId)
    //   // ->orderBy('id', 'desc')
    //   ->get();

    // $arr = [];
    // foreach ($orders as $orderItems) {
    //   $arr['order_items'] = [
    //     'order_items_id' => $orderItems['order_items_id'],
    //     'order_items_order_id' => $orderItems['order_items_order_id'],
    //     'order_items_product_id' => $orderItems['order_items_product_id'],
    //   ];
    //   // return response()->json($orderItems);
    // }
    // return response()->json($orders);
  }

  public function create()
  {
    //
  }

  public function saveRating(Request $request)
  {
    $validated = $request->validate([
      'rating' => 'required | numeric',
      'one_time_order' => 'nullable | boolean',
      'order_id' => 'required | integer',
    ]);
    if ($validated) {
      $rating = $request->rating;
      $one_time_order = $request->one_time_order;
      $order_id = $request->order_id;

      if ($one_time_order) {
        Order::where('id', $order_id)->update([
          'one_time_rating' => $rating,
        ]);
        OrderItem::where('order_id', $order_id)->update([
          'rating' => $rating,
        ]);
        return response()->json(['msg' => 'Insert Successful!']);
      } else {
        Order::where('id', $order_id)->update([
          'subscription_rating' => $rating,
        ]);
        OrderItem::where('order_id', $order_id)->update([
          'rating' => $rating,
        ]);
        return response()->json(['msg' => 'Insert Successful!']);
      }
    }
  }

  public function getRating(Request $request)
  {
    $validated = $request->validate([
      'orderId' => 'required | integer',
      // 'oneTimeOrder' => 'nullable | boolean',
      'oneTimeOrder' => 'nullable',
    ]);
    if ($validated) {
      $orderId = $request->orderId;
      $oneTimeOrder = $request->oneTimeOrder;
      $userId = auth('api')->user()->id;

      if ($oneTimeOrder !== 'null') {
        $ratings = Order::where('id', $orderId)
          ->where('user_id', $userId)
          ->get(['id', 'order_status', 'one_time_rating']);
        // return response()->json(['rating' => $ratings]);
      } else {
        $ratings = Order::where('id', $orderId)
          ->where('user_id', $userId)
          ->get(['id', 'order_status', 'subscription_rating']);
        // return response()->json(['rating' => $ratings]);
      }
      return response()->json($ratings);
    }
    // return $oneTimeOrder;
  }

  //  COD
  public function store(ValidateOrder $request)
  {
    $validated = $request->validated();
    // return $validated;
    if ($validated) {
      // $value = Order::where('id', 1)
      // ->whereJsonContains('order_items', ['product_name' => 'Chicken Thali'])
      // ->get('order_items');
      // ->update(['order_items' => ['item_rating' => 3]]);
      // $value = $value[0]->order_items;

      // $collection = $value->collect();
      // $collection = $collection->pluck(['order_items']);
      // $collection = $collection->collapse();
      // foreach ($value as $key => $val) {
      //   return $val['product_name'];
      // }
      // return $value;
      //

      // $products = Product::whereJsonContains(
      //   'details->subscriptions',
      //   $subscription
      // )
      //   ->whereJsonContains('meal_type', [
      //     'name' => $meal_type,
      //   ])
      //   ->get();
      // return 'done';
      ////////////////////////////////////////////
      $userId = auth('api')->user()->id;

      $collection = collect($validated);

      $orderCollectionFiltered = $collection->except([
        'orderItems',
        'phone_no',
      ]);

      $orderWithUserId = $orderCollectionFiltered->merge([
        'user_id' => $userId,
      ]);

      $order = $orderWithUserId->toArray();
      $order = Order::create($order);
      $orderId = $order->id;

      $orderItems = $collection->only('orderItems')->collapse();
      $orderItems = $orderItems->toArray();
      $phone = $collection->only('phone_no')->toArray();

      $duration = null;
      $startDate = null;
      for ($i = 0; $i < count($orderItems); $i++) {
        if ($orderItems[$i]['subscription_duration'] > 1) {
          $duration = $orderItems[$i]['subscription_duration'];
          $startDate = $orderItems[$i]['start_date'];
        }
      }
      // if ($duration) {
      //   $orderStatus = $orderWithUserId->get('order_status');
      //   $orderStatusCode = $orderWithUserId->get('order_status_code');

      //   $startDate = strtotime($startDate);

      //   $dates = [];
      //   for ($i = 0; $i < $duration; $i++) {
      //     $dateString[] = strtotime("+$i day", $startDate);
      //     $dates[] = date('Y-m-d', $dateString[$i]);
      //   }
      // }

      // for ($i = 0; $i < count($dates); $i++) {
      //   OrderSubscriptionStatus::create([
      //     'order_id' => $orderId,
      //     'delivery_date' => $dates[$i],
      //     'order_status' => $orderStatus,
      //     'order_status_code' => $orderStatusCode,
      //     // 'product_delivered' => 'rice',
      //     // 'remarks' => 'remarks',
      //   ]);
      // }

      // $items = [];
      // for ($i = 0; $i < count($orderItems); $i++) {
      //   $data['order_id'] = $orderId;
      //   $data['product_id'] = $orderItems[$i]['product_id'];
      //   $data['product_name'] = $orderItems[$i]['product_name'];
      //   $data['price'] = intval($orderItems[$i]['price']);
      //   $data['quantity'] = $orderItems[$i]['quantity'];
      //   $data['size'] = $orderItems[$i]['size'];
      //   $data['note'] = $orderItems[$i]['note'];
      //   $data['item_rating'] = $orderItems[$i]['item_rating'];
      //   $data['meal_type'] = $orderItems[$i]['meal_type'];
      //   $data['subscription_duration'] =
      //     $orderItems[$i]['subscription_duration'];
      //   $data['num_persons'] = $orderItems[$i]['num_persons'];
      //   $data['start_date'] = $orderItems[$i]['start_date'];
      //   $data['end_date'] = $orderItems[$i]['end_date'];
      //   $items[] = $data;
      // }
      for ($i = 0; $i < count($orderItems); $i++) {
        OrderItem::create([
          'order_id' => $orderId,
          'product_id' => $orderItems[$i]['product_id'],
          'product_name' => $orderItems[$i]['product_name'],
          'price' => intval($orderItems[$i]['price']),
          'quantity' => $orderItems[$i]['quantity'],
          'size' => $orderItems[$i]['size'],
          'note' => $orderItems[$i]['note'],
          'item_rating' => $orderItems[$i]['item_rating'],
          'meal_type' => $orderItems[$i]['meal_type'],
          'subscription_duration' => $orderItems[$i]['subscription_duration'],
          'num_persons' => $orderItems[$i]['num_persons'],
          'start_date' => date(
            "Y-m-d",
            strtotime($orderItems[$i]['start_date'])
          ),
          'end_date' => date("Y-m-d", strtotime($orderItems[$i]['end_date'])),
        ]);
      }
      // $items = collect($items);
      // Order::where('id', $userId)->update(['order_items' => $items]);
      // return $items;

      User::where('id', $userId)->update($phone);
      return response()->json(['msg' => 'Insert Successful!']);
    }
  }

  // //razorpay
  public function createOrder(Request $request)
  {
    // $api_key = 'rzp_test_nAmE4Ywn5bjLZ1';
    // $api_secret = 'i0YdqerZELIRJB9GQSYCXGDH';
    $api_key = env('RAZORPAY_API_KEY');
    $api_secret = env('RAZORPAY_SECRET_KEY');
    $api = new Api($api_key, $api_secret);

    $totalPrice = $request->amount;
    $receipt = "SK_Rec_" . date('mdYHis') . random_int(100, 999);

    $order = $api->order->create([
      'receipt' => $receipt,
      'amount' => $totalPrice * 100,
      'currency' => 'INR',
      'payment_capture' => '1',
    ]);

    $orderId = $order['id']; // Get the created Order ID
    $order = $api->order->fetch($orderId);
    // $orders = $api->order->all($options); // Returns array of order objects
    // $payments = $api->order->fetch($orderId)->payments(); // Returns array of payment objects against an order
    // return $order;
    // return response()->json($orderId);
    return response()->json([
      'rzPayOrderId' => $orderId,
      'receipt' => $receipt,
      'api_key' => $api_key,
    ]);
  }

  public function show(Order $order)
  {
    //
  }

  public function edit(Order $order)
  {
    //
  }

  public function update(Request $request, Order $order)
  {
    //
  }

  public function destroy(Order $order)
  {
    //
  }
}