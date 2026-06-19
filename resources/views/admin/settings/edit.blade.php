@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="section-title">Configuracion general</h1>
            <p class="section-subtitle">Sube logo de empresa y personaliza textos clave de la plataforma.</p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        <x-card>
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Nombre de la empresa</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $settings->get('company_name', 'Polla Mundialista Empresarial 2026')) }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('company_name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Correo de soporte</label>
                        <input type="email" name="support_email" value="{{ old('support_email', $settings->get('support_email')) }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        @error('support_email') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">Titulo principal</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $settings->get('hero_title')) }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                    @error('hero_title') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">Subtitulo</label>
                    <textarea name="hero_subtitle" rows="3" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">{{ old('hero_subtitle', $settings->get('hero_subtitle')) }}</textarea>
                    @error('hero_subtitle') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-2xl border border-slate-700 bg-slate-900/60 p-4">
                    <div class="mb-4 flex flex-col gap-3 border-b border-slate-700 pb-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-100">Estado del registro</h2>
                            <p class="text-sm text-slate-400">Apagalo cuando no quieras que entren participantes nuevos. Los usuarios existentes podran seguir iniciando sesion.</p>
                        </div>
                        <label class="inline-flex items-center gap-2 rounded-full border border-slate-600 bg-slate-950 px-3 py-2 text-sm font-semibold text-slate-200">
                            <input type="hidden" name="registration_enabled" value="0">
                            <input type="checkbox" name="registration_enabled" value="1" @checked(old('registration_enabled', $settings->get('registration_enabled', '1')) == '1') class="rounded border-slate-500 bg-slate-800 text-sky-500 focus:ring-sky-500">
                            Permitir nuevos registros
                        </label>
                    </div>
                    @error('registration_enabled') <p class="mb-3 text-xs text-red-300">{{ $message }}</p> @enderror

                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-100">Restriccion de registro</h2>
                            <p class="text-sm text-slate-400">Cuando esta activa, solo se aceptan los dominios o correos definidos abajo. Si esta apagada, cualquier correo valido puede registrarse.</p>
                        </div>
                        <label class="inline-flex items-center gap-2 rounded-full border border-slate-600 bg-slate-950 px-3 py-2 text-sm font-semibold text-slate-200">
                            <input type="hidden" name="registration_email_restriction_enabled" value="0">
                            <input type="checkbox" name="registration_email_restriction_enabled" value="1" @checked(old('registration_email_restriction_enabled', $settings->get('registration_email_restriction_enabled')) == '1') class="rounded border-slate-500 bg-slate-800 text-sky-500 focus:ring-sky-500">
                            Activar restriccion
                        </label>
                    </div>
                    @error('registration_email_restriction_enabled') <p class="mb-3 text-xs text-red-300">{{ $message }}</p> @enderror

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200">Dominios autorizados para registrarse</label>
                            <textarea name="allowed_registration_domains" rows="3" placeholder="wexler.com.co" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">{{ old('allowed_registration_domains', $settings->get('allowed_registration_domains')) }}</textarea>
                            <p class="mt-1 text-xs text-slate-400">Escribe dominios sin espacios, por ejemplo <span class="font-semibold text-slate-300">empresa.com.co</span>. Tambien puedes escribir <span class="font-semibold text-slate-300">@empresa.com.co</span>.</p>
                            @error('allowed_registration_domains') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200">Correos autorizados para registrarse</label>
                            <textarea name="allowed_registration_emails" rows="7" placeholder="usuario1@empresa.com&#10;usuario2@empresa.com" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">{{ old('allowed_registration_emails', $settings->get('allowed_registration_emails')) }}</textarea>
                            <p class="mt-1 text-xs text-slate-400">Opcional: escribe correos puntuales adicionales. Estas listas solo se aplican cuando la restriccion esta activa.</p>
                            @error('allowed_registration_emails') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-sky-400/30 bg-sky-500/10 p-4">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-sky-100">Integraciones de notificaciones</h2>
                        <p class="text-sm text-sky-100/70">Guarda el enlace del grupo y, si tienes una integracion externa, configura el webhook para envios automaticos.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200">Link de invitacion al grupo WhatsApp</label>
                            <input type="url" name="whatsapp_group_invite_url" value="{{ old('whatsapp_group_invite_url', $settings->get('whatsapp_group_invite_url')) }}" placeholder="https://chat.whatsapp.com/..." class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                            <p class="mt-1 text-xs text-slate-400">Este link sirve para entrar al grupo, pero no envia mensajes automaticos.</p>
                            @error('whatsapp_group_invite_url') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200">Webhook para mensajes automaticos</label>
                            <input type="url" name="whatsapp_group_webhook_url" value="{{ old('whatsapp_group_webhook_url', $settings->get('whatsapp_group_webhook_url')) }}" placeholder="https://tu-webhook" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                            <p class="mt-1 text-xs text-slate-400">Debe ser una URL que acepte solicitudes POST. Un link de invitacion de WhatsApp no funciona como webhook. Si lo dejas vacio, se enviara solo por correo.</p>
                            @error('whatsapp_group_webhook_url') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-400/30 bg-amber-500/10 p-4">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-amber-100">Premios secretos</h2>
                        <p class="text-sm text-amber-100/70">Cada premio puede tener su propia fecha de destape: octavos, cuartos, final o la ronda que definas.</p>
                    </div>

                    @php
                        $prizeFields = [
                            'first_place' => ['label' => 'Primer lugar', 'placeholder' => 'Premio para el campeon'],
                            'second_place' => ['label' => 'Segundo lugar', 'placeholder' => 'Premio para el subcampeon'],
                            'third_place' => ['label' => 'Tercer lugar', 'placeholder' => 'Premio para el tercer puesto'],
                        ];
                    @endphp

                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach ($prizeFields as $key => $prizeField)
                            @php
                                $nameKey = 'prize_'.$key;
                                $imageInput = $nameKey.'_image';
                                $imagePathKey = $nameKey.'_image_path';
                                $revealKey = $nameKey.'_reveal_at';
                                $deleteKey = 'delete_'.$nameKey;
                            @endphp

                            <div class="rounded-2xl border border-amber-300/20 bg-slate-950/45 p-3">
                                <label class="mb-1 block text-sm font-semibold text-slate-200">{{ $prizeField['label'] }}</label>
                                <input type="text" name="{{ $nameKey }}" value="{{ old($nameKey, $settings->get($nameKey)) }}" placeholder="{{ $prizeField['placeholder'] }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                                @error($nameKey) <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror

                                <label class="mt-3 mb-1 block text-sm font-semibold text-slate-200">Fecha de destape</label>
                                <input type="date" name="{{ $revealKey }}" value="{{ old($revealKey, $settings->get($revealKey, $settings->get('prize_reveal_at'))) }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                                @error($revealKey) <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror

                                <label class="mt-3 mb-1 block text-sm font-semibold text-slate-200">Imagen</label>
                                <input type="file" name="{{ $imageInput }}" accept="image/*" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                                @error($imageInput) <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                                @if ($settings->get($imagePathKey))
                                    <img src="{{ asset('storage/'.$settings->get($imagePathKey)) }}" alt="Imagen premio {{ strtolower($prizeField['label']) }}" class="mt-3 h-24 w-full rounded-lg object-cover">
                                @endif

                                <label class="mt-3 flex items-center gap-2 rounded-xl border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm font-semibold text-rose-100">
                                    <input type="checkbox" name="{{ $deleteKey }}" value="1" class="rounded border-rose-400 bg-slate-900 text-rose-500 focus:ring-rose-500">
                                    Eliminar este premio
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">Logo de empresa</label>
                    <input type="file" name="logo" accept="image/*" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                    @error('logo') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror

                    @if ($settings->get('company_logo_path'))
                        <div class="mt-3 rounded-lg border border-slate-700 bg-slate-900 p-3">
                            <p class="mb-2 text-xs uppercase tracking-wider text-slate-400">Logo actual</p>
                            <img src="{{ asset('storage/'.$settings->get('company_logo_path')) }}" alt="Logo empresa" class="h-14 w-auto rounded-md bg-slate-800 p-2">
                        </div>
                    @endif
                </div>

                <x-button type="submit">Guardar configuracion</x-button>
            </form>
        </x-card>
    </div>
@endsection
