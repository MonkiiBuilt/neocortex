<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFormRequest extends FormRequest
{
    public static $messages = [
        'url.required' => "Are you serious?! You can't even type in a URL properly?! Try again, doofus.",
        'url.url' => "Are you serious?! You can't even type in a URL properly?! Try again, doofus.",
        'url.invalid' => "I can't get anything useful from that url. I haven't got time for this shit. Try something less stupid.",
        'upload.required' => "Did you just try to upload without including a file?! If you do that again, I'm revoking your access for 24 hours.",
        'upload.error' => "Some developer obviously set me up before they had their coffee. Ask the lazy bastard to try again 'cause you can't upload a file.",
        'upload.notimage' => "Look up there. It says \"Upload an <b>IMAGE<b>\". Only '.jpg', '.jpeg', '.png' or '.gif' files, you nuff nuff.",
        'upload.toobig' => "File too big"
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
        if (isset($_POST['btnUrl'])) {
            $rules = [
                'url' => 'required|url'
            ];
        }
        else if (isset($_POST['btnUpload'])) {
            $rules = [
                'upload' => 'required'
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return self::$messages;
    }
}
