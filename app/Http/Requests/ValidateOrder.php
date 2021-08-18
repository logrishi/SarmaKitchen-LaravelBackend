<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ValidateOrder extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'phone_no' => 'required | integer | digits:10',
      'address_id' => 'required | integer | exists:addresses,id',
      'address' => 'required | string',
      'total_price' => 'required | numeric',
      'payment' => 'required | array',
      // 'meal_type' => 'required | string',
      // 'subscription_duration' => 'required | integer',
      // 'num_persons' => 'nullable | integer',
      // 'start_date' => 'nullable | date',
      // 'end_date' => 'nullable | date',
      'order_status' => 'required | string',
      'order_status_code' => 'required | integer',
      'orderItems' => 'required | array',
      // 'orderItems.*.product_id' => 'required | integer',
      // 'orderItems.*.product_name' => 'required | string',
      // 'orderItems.*.quantity' => 'required | integer',
      // 'orderItems.*.price' => 'required | numeric',
      // 'orderItems.*.size' => 'nullable | string',
      // 'orderItems.*.note' => 'nullable | string',
      // 'orderItems.*.item_rating' => 'nullable | numeric',
      // 'orderItems.*.meal_type' => 'required | string',
      // 'orderItems.*.subscription_duration' => 'required | integer',
      // 'orderItems.*.num_persons' => 'nullable | integer',
      // 'orderItems.*.start_date' => 'nullable | date',
      // 'orderItems.*.end_date' => 'nullable | date',
    ];
  }

  protected function prepareForValidation()
  {
    // $this->merge([
    //   'veg_only' => ($this->veg_only == true
    //       ? 1
    //       : $this->veg_only == false)
    //     ? 0
    //     : null,
    // ]);
    $this->merge([
      // 'orderItems.*.start_date' => $this->orderItems['start_date']
      //   ? date("Y-m-d", strtotime($this->orderItems['start_date']))
      //   : null,
      // 'orderItems.*.end_date' => $this->orderItems['end_date']
      //   ? date("Y-m-d", strtotime($this->orderItems['end_date']))
      //   : null,
    ]);
  }
}