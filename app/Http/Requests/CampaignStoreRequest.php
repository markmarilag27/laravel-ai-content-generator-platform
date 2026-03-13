<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CampaignStoreRequest extends FormRequest
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
            'brief.quantities' => ['required', 'array', 'min:1', 'max:10'],
            'brief.quantities.*' => ['required', 'integer', 'min:1', 'max:7'],
            'brief.word_counts' => [
                'required',
                'array',
                'size:'.count($this->input('brief.quantities', [])),
            ],
            'brief.word_counts.*' => ['required', 'integer', 'min:50', 'max:2000'],
        ];
    }

    /**
     * Custom validation to ensure the keys (content types) match perfectly.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $quantities = $this->input('brief.quantities', []);
                $wordCounts = $this->input('brief.word_counts', []);

                $quantityKeys = array_keys($quantities);
                $wordCountKeys = array_keys($wordCounts);

                $missingInWordCounts = array_diff($quantityKeys, $wordCountKeys);
                $missingInQuantities = array_diff($wordCountKeys, $quantityKeys);

                if (! empty($missingInWordCounts) || ! empty($missingInQuantities)) {
                    $validator->errors()->add(
                        'brief.word_counts',
                        'The content types (keys) in quantities and word_counts must be identical.'
                    );
                }
            },
        ];
    }
}
