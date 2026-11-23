<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('review innovations') || $this->user()->hasRole(['reviewer', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'decision' => ['required', 'string', 'in:approved,rejected,revision_requested'],
            'feedback' => ['required', 'string', 'min:10', 'max:5000'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Please provide a score from 1 to 5.',
            'rating.min' => 'The minimum rating is 1.',
            'rating.max' => 'The maximum rating is 5.',
            'decision.required' => 'Please select an evaluation decision.',
            'decision.in' => 'Decision must be Approved, Rejected, or Revision Requested.',
            'feedback.required' => 'Please provide feedback for the innovator explaining your decision.',
            'feedback.min' => 'Feedback must be at least 10 characters long to provide actionable guidance.',
        ];
    }
}
