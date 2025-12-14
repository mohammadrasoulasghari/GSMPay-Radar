<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePrAnalysisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Webhook is open (can add API key validation later)
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
            'repository' => ['required', 'string', 'max:255'],
            'pr_number' => ['required'],
            'pr_link' => ['nullable', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:500'],
            'author' => ['required', 'array'],
            'author.username' => ['required', 'string', 'max:255'],
            'author.name' => ['nullable', 'string', 'max:255'],
            'ai_analysis' => ['required', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'repository.required' => 'The repository field is required.',
            'pr_number.required' => 'The pr_number field is required.',
            'author.required' => 'The author field is required.',
            'author.username.required' => 'The author.username field is required.',
            'ai_analysis.required' => 'The ai_analysis field is required.',
            'ai_analysis.array' => 'The ai_analysis must be a valid JSON object.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     * Returns JSON response for API consistency.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
