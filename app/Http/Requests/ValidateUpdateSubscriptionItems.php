<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateUpdateSubscriptionItems extends FormRequest
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
      'product_delivered' => 'string | required',
      'today' => 'date | required',
      'orderId' => 'numeric  | required | exists:orders,id',
      'order_status' => 'string | required',
      'delivered_by' => 'string | required',
    ];
  }

  protected function prepareForValidation()
  {
    $this->merge([
      // 'product_delivered' => ucwords($this->product_delivered),
      'today' => date('Y-m-d', strtotime($this->today)),
    ]);
  }
}