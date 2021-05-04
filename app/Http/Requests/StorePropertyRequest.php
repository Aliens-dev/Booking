<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:3|max:100',
            'state' => ['required', Rule::in(wilayas())],
            'city' => ['required', Rule::in(communes())],
            'street' => 'required|min:3|max:255',
            'price' => 'required|integer|min:200',
            'type' => 'required',
            'rooms' => 'required|min:1|integer',
            'images' => 'required|max:10',
            'images.*' => 'image|mimes:jpg,bmp,png',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['success' => false, 'errors' => $validator->errors()], 403);
        throw  new ValidationException($validator, $response);
        //parent::failedValidation($validator);
    }
}
