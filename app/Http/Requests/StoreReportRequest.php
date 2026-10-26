<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public const MESSAGE_MIN = 10;

    public const MESSAGE_MAX = 1000;

    public const CONTACT_MAX = 100;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:'.self::MESSAGE_MIN, 'max:'.self::MESSAGE_MAX],
            'contact' => ['nullable', 'string', 'max:'.self::CONTACT_MAX],
            // Honeypot: hidden from people, often filled by bots. Handled in the controller.
            'hp_check' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => __('reports.errors.message_required'),
            'message.min' => __('reports.errors.message_min', ['min' => self::MESSAGE_MIN]),
            'message.max' => __('reports.errors.message_max', ['max' => self::MESSAGE_MAX]),
            'contact.max' => __('reports.errors.contact_max', ['max' => self::CONTACT_MAX]),
        ];
    }
}
