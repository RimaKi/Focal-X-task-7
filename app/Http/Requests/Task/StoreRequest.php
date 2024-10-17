<?php

namespace App\Http\Requests\Task;

use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Role::find(auth()->user()->role_id)->name == 'task_builder';
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:bug,feature,improvement'],
            'priority' => ['required', 'in:low,medium,high'],
            'assigned_to' => ['numeric', 'exists:users,id'],
            'dependencies' => ['array'],
            'dependencies.*' => ['numeric', 'exists:tasks,id']
        ];
    }
}
