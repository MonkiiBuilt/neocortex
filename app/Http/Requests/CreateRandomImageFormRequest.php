<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRandomImageFormRequest extends FormRequest
{
    public static $messages = [
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!\Auth::user()) return false;

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // if (isset($_POST['btnUrl'])) {

        $rules = [

        ];

        return $rules;
    }

    public function messages()
    {
        return self::$messages;
    }
}
