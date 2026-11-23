<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInnovationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $innovation = $this->route('innovation');
        return $innovation && $this->user()->can('update', $innovation);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp,gif,svg,zip,mp4,mov,avi,wmv,flv,mkv,webm,mp3,wav,ogg,m4a,aac',
                'max:51200', // 50MB per file (increased for video/audio)
            ],
            'collaborators' => ['nullable', 'array'],
            'collaborators.*.user_id' => ['required_with:collaborators', 'exists:users,id'],
            'collaborators.*.role' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a clear title for your innovation.',
            'title.min' => 'The title must be at least 5 characters.',
            'description.required' => 'Please describe your innovation, its objectives, and impact.',
            'description.min' => 'The description must be at least 20 characters.',
            'attachments.*.mimes' => 'Attachments must be documents (PDF, Word, Excel, PowerPoint), images (JPG, PNG, WebP, GIF, SVG), videos (MP4, MOV, AVI, WebM), audio (MP3, WAV, OGG), or ZIP files.',
            'attachments.*.max' => 'Each attachment must not exceed 50 megabytes.',
        ];
    }
}
