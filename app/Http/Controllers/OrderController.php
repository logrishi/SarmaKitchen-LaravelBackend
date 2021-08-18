<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateCreateOrder;
use App\Http\Requests\ValidateNewOrderAdminNotification;
use App\Http\Requests\ValidateOrder;
use App\Models\DeviceToken;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSubscriptionStatus;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
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
  }

  public function create()
  {
    //
  }

  // //razorpay
  public function createOrder(ValidateCreateOrder $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $api_key = env('RAZORPAY_API_KEY');
      $api_secret = env('RAZORPAY_SECRET_KEY');
      $api = new Api($api_key, $api_secret);

      $totalPrice = $validated['amount'];
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
      // return response()->json($order);
      return response()->json([
        'rzPayOrderId' => $orderId,
        'receipt' => $receipt,
        'api_key' => $api_key,
      ]);
    }
  }

  public function saveRating(Request $request)
  {
    $validated = $request->validate([
      'rating' => 'required | numeric',
      'orderItemId' => 'required | integer | exists:order_items,id',
    ]);
    if ($validated) {
      $rating = $validated['rating'];
      $id = $validated['orderItemId'];
      OrderItem::where('id', $id)->update([
        'rating' => $rating,
      ]);
      return response()->json(['msg' => 'Insert Successful!']);
    }
  }

  public function getRating(Request $request)
  {
    $validated = $request->validate([
      'ratedOrderItemId' => 'required | numeric | exists:order_items,id',
    ]);
    if ($validated) {
      $id = $validated['ratedOrderItemId'];

      $ratings = OrderItem::where('id', $id)->get('rating');
      return response()->json($ratings);
    }
  }

  //  COD
  public function store(ValidateOrder $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $userId = auth('api')->user()->id;
      $otp = mt_rand(10000, 99999);

      $collection = collect($validated);

      //verify payment signature
      $paymentCollection = $collection->only('payment');
      $arr = $paymentCollection->toArray();

      $razorpaySignature = $arr['payment'][0]['razorpay_signature'];
      $razorpayPaymentId = $arr['payment'][0]['razorpay_payment_id'];
      $razorpay_order_id = $arr['payment'][0]['razorpay_order_id'];

      $api_secret = env('RAZORPAY_SECRET_KEY');
      $api_key = env('RAZORPAY_API_KEY');

      $api = new Api($api_key, $api_secret);
      $attributes = [
        'razorpay_signature' => $razorpaySignature,
        'razorpay_payment_id' => $razorpayPaymentId,
        'razorpay_order_id' => $razorpay_order_id,
      ];

      $verified = false;
      try {
        $api->utility->verifyPaymentSignature($attributes);
        $verified = true;
      } catch (SignatureVerificationError $e) {
        $verified = false;
      }
      if ($verified) {
        $orderCollectionFiltered = $collection->except([
          'orderItems',
          'phone_no',
        ]);
        $orderWithUserId = $orderCollectionFiltered->merge([
          'user_id' => $userId,
          'otp' => $otp,
        ]);

        $order = $orderWithUserId->toArray();
        //Orders table
        $order = Order::create($order);
        $orderId = $order->id;

        $orderItems = $collection->only('orderItems')->collapse();
        $orderItems = $orderItems->toArray();
        $phone = $collection->only('phone_no')->toArray();

        //OrderSubscriptionStatus table
        $orderStatus = $orderWithUserId->get('order_status');
        $orderStatusCode = $orderWithUserId->get('order_status_code');

        $duration = null;
        $startDate = null;
        $dates = [];
        for ($i = 0; $i < count($orderItems); $i++) {
          if ($orderItems[$i]['subscription_duration'] > 1) {
            $duration = $orderItems[$i]['subscription_duration'];
            $startDate = $orderItems[$i]['start_date'];
          }
        }
        if ($duration) {
          $startDate = strtotime($startDate);
          for ($i = 0; $i < $duration; $i++) {
            $dateString[] = strtotime("+$i day", $startDate);
            $dates[] = date('Y-m-d', $dateString[$i]);
          }
        }

        for ($i = 0; $i < count($dates); $i++) {
          $otps = mt_rand(10000, 99999);
          OrderSubscriptionStatus::create([
            'order_id' => $orderId,
            'delivery_date' => $dates[$i],
            'order_status' => $orderStatus,
            'order_status_code' => $orderStatusCode,
            'otp' => $otps,
            // 'product_delivered' => 'rice',
            // 'remarks' => 'remarks',
          ]);
        }

        //order items table
        for ($i = 0; $i < count($orderItems); $i++) {
          OrderItem::create([
            'order_id' => $orderId,
            'product_id' => $orderItems[$i]['product_id'],
            'product_name' => $orderItems[$i]['product_name'],
            'price' => intval($orderItems[$i]['price']),
            'quantity' => $orderItems[$i]['quantity'],
            'size' => $orderItems[$i]['size'],
            'note' => $orderItems[$i]['note'],
            'is_veg' => $orderItems[$i]['is_veg']
              ? ($orderItems[$i]['is_veg'] == 0 || 1
                ? $orderItems[$i]['is_veg']
                : null)
              : null,
            'rating' => null,
            'meal_type' => $orderItems[$i]['meal_type'],
            'subscription_duration' => $orderItems[$i]['subscription_duration'],
            'num_persons' => $orderItems[$i]['num_persons'],
            'start_date' => $orderItems[$i]['start_date']
              ? date("Y-m-d", strtotime($orderItems[$i]['start_date']))
              : null,
            'end_date' => $orderItems[$i]['end_date']
              ? date("Y-m-d", strtotime($orderItems[$i]['end_date']))
              : null,
            'subscription_menu' => $orderItems[$i]['subscription_menu']
              ? $orderItems[$i]['subscription_menu']
              : null,
            // 'order_status' => $orderStatus,
            // 'order_status_code' => $orderStatusCode,
          ]);
        }

        //update phone Users Table
        User::where('id', $userId)->update($phone);
        return response()->json(['msg' => 'Insert Successful!']);
      }
    }
  }

  // public function fcm(ValidateNewOrderAdminNotification $request)
  // {
  //   $validated = $request->validated();
  //   if ($validated) {
  //     $fcmServerKey = env('FCM_SERVER_KEY');

  //     $users = User::where('is_admin', 1)->get();
  //     $userIds = $users->pluck('id');

  //     $token = DeviceToken::whereIn('user_id', $userIds)->get('token');
  //     $allTokens = $token->pluck('token');

  //     $title = $validated['title'];
  //     $message = $validated['message'];
  //     // $type = $request->type;

  //     // $notification_ids = $request->notification_ids;
  //     // $registrationIds = array($notification_ids);

  //     $registrationIds = $allTokens;

  //     $msg = [
  //       'title' => $title,
  //       'message' => $message,
  //       'click_action' => 'FCM_PLUGIN_ACTIVITY',
  //       'vibrate' => 1,
  //       'sound' => 1,
  //       // 'type'          => $type
  //     ];

  //     $fields = [
  //       'registration_ids' => $registrationIds,
  //       'data' => $msg,
  //       'priority' => 'high',
  //       'notification' => [
  //         'title' => $title,
  //         'body' => $message,
  //       ],
  //       // 'data'=> array(
  //       //     'name'=>'rishi'
  //       // )
  //     ];

  //     $headers = [
  //       'Authorization: key=' . $fcmServerKey,
  //       'Content-Type: application/json',
  //     ];

  //     $ch = curl_init();
  //     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
  //     curl_setopt($ch, CURLOPT_POST, true);
  //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
  //     $result = curl_exec($ch);
  //     curl_close($ch);
  //     // return response()->json($result);
  //     return $result;
  //   }
  // }

  public function fcm(Request $request)
  {
    // define('API_ACCESS_KEY', env('FCM_SERVER_KEY'));
    $fcmServerKey = env('FCM_SERVER_KEY');

    $users = User::where('is_admin', 1)->get();
    $userIds = $users->pluck('id');

    $token = DeviceToken::whereIn('user_id', $userIds)->get('token');
    $allTokens = $token->pluck('token');

    $title = $request->title;
    $message = $request->message;
    // $type = $request->type;

    // $notification_ids = $request->notification_ids;
    // $registrationIds = array($notification_ids);

    $registrationIds = $allTokens;

    $msg = [
      'title' => $title,
      'message' => $message,
      'click_action' => 'FCM_PLUGIN_ACTIVITY',
      'vibrate' => 1,
      'sound' => 1,
      // 'type'          => $type
    ];

    $fields = [
      'registration_ids' => $registrationIds,
      'data' => $msg,
      'priority' => 'high',
      'notification' => [
        'title' => $title,
        'body' => $message,
      ],
      // 'data'=> array(
      //     'name'=>'rishi'
      // )
    ];

    $headers = [
      'Authorization: key=' . $fcmServerKey,
      'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    // return response()->json($result);
    return $result;
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