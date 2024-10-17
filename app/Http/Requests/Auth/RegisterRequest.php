<?php

namespace App\Http\Requests\Auth;

use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Role::find(auth()->user()->role_id)->name == 'admin';
    }

    public function failedAuthorization()
    {
        throw new AuthorizationException("You don't have the right role.");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'national_id' => 'required|string|max:255',
            'role_id' => 'required|integer|exists:roles,id'
        ];
    }
}
