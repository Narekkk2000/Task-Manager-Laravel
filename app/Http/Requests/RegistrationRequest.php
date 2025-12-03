<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistrationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'surname' => 'required|string|max:120',
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(16)->toDateString(),
                'after_or_equal:' . now()->subYears(120)->toDateString(),  
            ],
            'address' => 'required|string|min:3|max:200',
            'email' => 'required|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                Password::min(4)->numbers()->symbols(),
            ]
        ];
    }


    /**
     * Handle a failed validation attempt.
     *
     * Overrides the default behavior to return a JSON response instead of a redirect.
     *
     * @param Validator $validator  The validator instance containing validation errors.
     * @throws HttpResponseException
     *
     * @return void
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }


    /**
     * Get custom error messages for validator errors.
     *
     * This method allows you to define custom validation messages for specific fields and rules.
     *
     * @return array<string, string>  An associative array of validation rule messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'surname.required' => 'The surname field is required.',
            'birth_date.required' => 'The birth date field is required.',
            'address.required' => 'The address field is required.',
            'email.required' => 'The email field is required.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.max' => 'The password must be at least 8 characters.',
        ];
    }
}
