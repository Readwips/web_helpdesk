<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('user')?->id;

        return ['department_id' => 'nullable|exists:departments,id', 'name' => 'required|string|max:255', 'email' => ['required', 'email', Rule::unique('users')->ignore($id)], 'phone' => 'nullable|string|max:30', 'role' => 'required|in:admin,technician,user', 'status' => 'required|in:active,inactive', 'password' => [$id ? 'nullable' : 'required', 'confirmed', 'min:8']];
    }
}
