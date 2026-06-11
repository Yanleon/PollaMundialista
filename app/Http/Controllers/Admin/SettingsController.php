<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $keys = [
            'company_name',
            'hero_title',
            'hero_subtitle',
            'support_email',
            'whatsapp_group_invite_url',
            'whatsapp_group_webhook_url',
            'company_logo_path',
            'allowed_registration_emails',
            'allowed_registration_domains',
            'prize_first_place',
            'prize_second_place',
            'prize_third_place',
            'prize_reveal_at',
            'prize_first_place_image_path',
            'prize_second_place_image_path',
            'prize_third_place_image_path',
        ];
        $settings = AppSetting::getByKeys($keys);

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        AppSetting::setValue('company_name', $data['company_name']);
        AppSetting::setValue('hero_title', $data['hero_title'] ?? null);
        AppSetting::setValue('hero_subtitle', $data['hero_subtitle'] ?? null);
        AppSetting::setValue('support_email', $data['support_email'] ?? null);
        AppSetting::setValue('whatsapp_group_invite_url', $data['whatsapp_group_invite_url'] ?? null);
        AppSetting::setValue('whatsapp_group_webhook_url', $data['whatsapp_group_webhook_url'] ?? null);
        AppSetting::setValue('allowed_registration_domains', $data['allowed_registration_domains'] ?? null);
        AppSetting::setValue('allowed_registration_emails', $data['allowed_registration_emails'] ?? null);
        AppSetting::setValue('prize_first_place', $data['prize_first_place'] ?? null);
        AppSetting::setValue('prize_second_place', $data['prize_second_place'] ?? null);
        AppSetting::setValue('prize_third_place', $data['prize_third_place'] ?? null);
        AppSetting::setValue('prize_reveal_at', $data['prize_reveal_at'] ?? null);

        foreach ([
            'prize_first_place_image' => 'prize_first_place_image_path',
            'prize_second_place_image' => 'prize_second_place_image_path',
            'prize_third_place_image' => 'prize_third_place_image_path',
        ] as $input => $settingKey) {
            if ($request->hasFile($input)) {
                $old = AppSetting::getValue($settingKey);

                if ($old) {
                    Storage::disk('public')->delete($old);
                }

                $path = $request->file($input)->store('prizes', 'public');
                AppSetting::setValue($settingKey, $path);
            }
        }

        if ($request->hasFile('logo')) {
            $old = AppSetting::getValue('company_logo_path');

            if ($old) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('logo')->store('company', 'public');
            AppSetting::setValue('company_logo_path', $path);
        }

        return back()->with('success', 'Configuracion actualizada correctamente.');
    }
}
