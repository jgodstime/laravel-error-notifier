<?php
namespace ErrorNotifier\Notify\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ErrorNotifierRequest extends FormRequest
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
            'access_url' => 'nullable',
            'is_authenticated' => 'nullable',
            'id' => 'nullable',
            'email' => 'nullable',
            'status_code' => 'nullable',
            'message' => 'required|string',
        ];
    }
}
