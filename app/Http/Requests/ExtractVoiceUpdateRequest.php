<?php

namespace App\Http\Requests;

use App\Models\BrandVoiceProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExtractVoiceUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var BrandVoiceProfile $profile */
        $profile = $this->profile;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brand_voice_profiles', 'name')->ignore($profile->id),
            ],
            'samples' => [
                'required',
                'array',
                'min:3',
                'max:5',
            ],
            'samples.*' => [
                'required',
                'string',
                'min:50',
                'max:2000',
            ],
        ];
    }

    public function attributes(): array
    {
        $attributes = [
            'name' => 'Profile name',
        ];

        foreach ($this->input('samples', []) as $key => $value) {
            $attributes["samples.{$key}"] = 'Sample '.($key + 1);
        }

        return $attributes;
    }
}
