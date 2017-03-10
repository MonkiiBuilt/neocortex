<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFormRequest extends FormRequest
{
    public static $messages = [
        'url.required' => "Are you serious?! You can't even type in a URL properly?! Try again, doofus.",
        'url.url' => "Are you serious?! You can't even type in a URL properly?! Try again, doofus.",
        'url.invalid' => "I can't get anything useful from that url. I haven't got time for this shit. Try something less stupid."
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
            'url' => 'required|url'
        ];

        return $rules;
    }

    public function messages()
    {
        return self::$messages;
    }
}
