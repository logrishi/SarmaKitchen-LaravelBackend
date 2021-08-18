<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateAddress extends FormRequest
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
      'phone_no' => 'required | numeric | digits:10',
      'address' => 'required | string',
      'landmark' => 'required | string',
      'address_coords.latitude' => 'required | numeric',
      'address_coords.longitude' => 'required | numeric',
    ];
  }

  protected function prepareForValidation()
  {
    // $this->merge([
    // 'phone_no' =>
    // ]);
  }

  public function messages()
  {
    return [
        // 'phone_no' => 'Invalid Phone no',
      ];
  }
}