<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],

            'deadline' => ['nullable', 'date', 'after:now'],

            'brief' => ['required', 'array'],
            'brief.goal' => ['required', 'string', 'min:10'],
            'brief.topic' => ['required', 'string', 'min:10'],

            'brief.quantities' => ['required', 'array', 'min:1'],
            'brief.quantities.*' => ['required', 'integer', 'min:1', 'max:50'],

            'brief.word_counts' => ['sometimes', 'array'],
            'brief.word_counts.*' => ['integer', 'min:50', 'max:2000'],
        ];
    }
}
