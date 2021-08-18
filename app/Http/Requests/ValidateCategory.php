<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class ValidateCategory extends FormRequest
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
      'category' => 'required | string',
      'meal_type' => 'required | array',
      'meal_type.name' => 'required | string',
      'meal_type.image' => 'nullable | image',
      'dish_type' => 'nullable | array',
      'subscriptions' => 'required | array',
    ];
  }

  protected function prepareForValidation()
  {
    $val = $this->all();
    $this->merge([
      'category' => ucwords($this->category),
      //   'meal_type.name' => ucwords($this->meal_type['name']),
      //   $val['meal_type']['name'] => ucwords($val['meal_type']['name']),
    ]);
  }
}