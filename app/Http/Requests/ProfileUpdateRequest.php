<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->input('name', $this->user()->name),
            'email' => $this->input('email', $this->user()->email),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $canUpdatePassword = $this->user()?->role === 'admin';

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'password' => $canUpdatePassword
                ? ['nullable', 'string', 'min:8', 'confirmed']
                : ['nullable', 'prohibited'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * Get the custom validation messages for the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.image' => 'Please upload a valid image file.',
            'image.max' => 'Profile images must be 2MB or smaller.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.prohibited' => 'Only admin can update password from profile settings.',
        ];
    }
}
