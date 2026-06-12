<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:120'],
            'hero_title' => ['nullable', 'string', 'max:180'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:120'],
            'whatsapp_group_invite_url' => ['nullable', 'url', 'max:2048'],
            'whatsapp_group_webhook_url' => ['nullable', 'url', 'max:2048'],
            'registration_enabled' => ['nullable', 'boolean'],
            'registration_email_restriction_enabled' => ['nullable', 'boolean'],
            'allowed_registration_domains' => ['nullable', 'string', 'max:2000'],
            'allowed_registration_emails' => ['nullable', 'string', 'max:10000'],
            'prize_first_place' => ['nullable', 'string', 'max:255'],
            'prize_second_place' => ['nullable', 'string', 'max:255'],
            'prize_third_place' => ['nullable', 'string', 'max:255'],
            'prize_reveal_at' => ['nullable', 'date'],
            'prize_first_place_image' => ['nullable', 'image', 'max:4096', 'mimes:png,jpg,jpeg,svg,webp'],
            'prize_second_place_image' => ['nullable', 'image', 'max:4096', 'mimes:png,jpg,jpeg,svg,webp'],
            'prize_third_place_image' => ['nullable', 'image', 'max:4096', 'mimes:png,jpg,jpeg,svg,webp'],
            'logo' => ['nullable', 'image', 'max:4096', 'mimes:png,jpg,jpeg,svg,webp'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $emails = str((string) $this->input('allowed_registration_emails'))
                ->replace([",", ";", "\r"], "\n")
                ->explode("\n")
                ->map(fn (string $email) => trim($email))
                ->filter();

            foreach ($emails as $email) {
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add('allowed_registration_emails', "El correo {$email} no es valido.");
                }
            }

            $domains = str((string) $this->input('allowed_registration_domains'))
                ->replace([",", ";", "\r"], "\n")
                ->explode("\n")
                ->map(fn (string $domain) => str($domain)->trim()->ltrim('@')->toString())
                ->filter();

            foreach ($domains as $domain) {
                if (! preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)+$/i', $domain)) {
                    $validator->errors()->add('allowed_registration_domains', "El dominio {$domain} no es valido.");
                }
            }
        });
    }
}
