<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateDeliveryOtp extends FormRequest
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
      'order_id' => 'numeric | required | exists:orders,id',
      'today' => 'date | required',
      'subscription_duration' => 'numeric | required',
      'otp' => 'numeric | required',
    ];
  }
  protected function prepareForValidation()
  {
    $this->merge([
      'today' => date('Y-m-d', strtotime($this->today)),
    ]);
  }
}