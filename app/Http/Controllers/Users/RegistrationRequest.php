<?php

namespace App\Http\Controllers\Users;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'name' => "required|string|max:255",
            'email' => "required|string|email|max:255|unique:users",
            'password' => "required|string|min:6|confirmed"
        ];
    }

    /**
     * Get error messages for specific validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => "Обязательно укажите своё имя",
            'email.required' => "Обязательно укажите адрес электронной почты",
            'password.required' => "Обязательно придумайте пароль для входа",
            'password.confirmed' => "Пароли не совпадают",
        ];
    }
}
