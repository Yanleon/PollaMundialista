<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getByKeys(array $keys): Collection
    {
        return static::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key');
    }

    public static function allowedRegistrationEmails(): Collection
    {
        return str(static::getValue('allowed_registration_emails', ''))
            ->replace([",", ";", "\r"], "\n")
            ->explode("\n")
            ->map(fn (string $email) => str($email)->lower()->trim()->toString())
            ->filter()
            ->unique()
            ->values();
    }

    public static function allowedRegistrationDomains(): Collection
    {
        return str(static::getValue('allowed_registration_domains', ''))
            ->replace([",", ";", "\r"], "\n")
            ->explode("\n")
            ->map(fn (string $domain) => str($domain)->lower()->trim()->ltrim('@')->toString())
            ->filter()
            ->unique()
            ->values();
    }

    public static function registrationEmailIsAllowed(string $email): bool
    {
        $allowedEmails = static::allowedRegistrationEmails();
        $allowedDomains = static::allowedRegistrationDomains();
        $normalizedEmail = str($email)->lower()->trim()->toString();

        if ($allowedEmails->isEmpty() && $allowedDomains->isEmpty()) {
            return true;
        }

        if ($allowedEmails->contains($normalizedEmail)) {
            return true;
        }

        $emailDomain = str($normalizedEmail)->after('@')->toString();

        return $allowedDomains->contains($emailDomain);
    }
}
