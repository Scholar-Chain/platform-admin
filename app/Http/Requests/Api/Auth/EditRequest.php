<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'author.*' => ['nullable'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('author') && !auth('api')->user()->hasRole(UserRole::AUTHOR->value)) {
                $validator->errors()->add('author', 'Anda tidak memiliki izin untuk menentukan author.');
            }
        });
    }
}
