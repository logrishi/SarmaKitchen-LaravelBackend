<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ValidateProduct extends FormRequest
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
  public function rules(Request $request)
  {
    return [
      'category_id' => 'required | integer | exists:categories,id',
      'name' => 'required | string | unique:products',
      'description' => 'required | string',
      'is_veg' => 'nullable | boolean',
      'is_available' => 'nullable | boolean',
      'image' => 'nullable | image',
      'meal_type' => 'required | array',
      'meal_type.name' => 'string',
      // 'details' => 'array',
      // 'details.customizations' => 'nullable | array',
      // 'details.customizations.*.name' => 'nullable | string',
      // 'details.customizations.*.option' => 'nullable | array',
      'details.subscriptions' => 'required | array',
      'details.dish_type' => 'nullable | array',
      'details.size' => 'nullable | array',
      'details.size.*' => 'nullable | string',
      'details.notes' => 'nullable | array',
      'details.notes.*' => 'nullable | string',
      'details.price' => 'nullable | array',
      'details.price.*' => 'nullable | string',
      // 'prices' => 'array',
    ];
  }

  public function messages()
  {
    return [
        // 'name.required' => 'Name is required',
        // 'details.required' => 'Details is required',
        // 'type.required' => 'Product Type is required',
        // 'category.required' => 'Product Category is required',
        // 'price.required' => 'Price is required',
        // 'image.required' => 'Image is required',
      ];
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'name' => ucwords($this->name),
      'is_veg' => $this->is_veg == null ? 0 : 1,
    ]);
  }
}