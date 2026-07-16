<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Administrative fields — only managers can change these.
            'name'            => ['prohibited'],
            'email'           => ['prohibited'],
            'room_number'     => ['prohibited'],
            'resident_number' => ['prohibited'],
            'role'            => ['prohibited'],
            // Phone number is self-managed (M-16).
            'phone_number'    => ['nullable', 'string', 'max:30'],
            // Profile photo fields.
            'profile_photo'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
            'remove_profile_photo'=> ['nullable', 'boolean'],
        ];
    }
}
