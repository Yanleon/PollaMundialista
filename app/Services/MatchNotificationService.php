<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\MatchGame;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MatchNotificationService
{
    public function notifyTodayMatches(Collection $matches): array
    {
        $sentEmails = 0;
        $failedEmails = 0;

        $participants = User::query()
            ->where('role', 'participant')
            ->where('status', 'active')
            ->whereNotNull('email')
            ->get(['id', 'name', 'email']);

        $message = $this->buildMessage($matches);

        foreach ($participants as $participant) {
            try {
                Mail::raw($message, function ($mail) use ($participant): void {
                    $mail->to($participant->email, $participant->name)
                        ->subject('Partidos de hoy - Polla Mundialista 2026');
                });
                $sentEmails++;
            } catch (\Throwable $exception) {
                $failedEmails++;
                Log::warning('No se pudo enviar notificacion por correo', [
                    'user_id' => $participant->id,
                    'email' => $participant->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $whatsappSent = false;
        $whatsappError = null;
        $webhookUrl = AppSetting::getValue('whatsapp_group_webhook_url') ?: config('services.whatsapp.group_webhook_url');

        if ($webhookUrl) {
            try {
                $response = Http::timeout(20)
                    ->withOptions(['verify' => false])
                    ->post($webhookUrl, ['message' => $message]);

                $whatsappSent = $response->successful();

                if (! $whatsappSent) {
                    $whatsappError = 'Webhook devolvio estado '.$response->status();
                }
            } catch (\Throwable $exception) {
                $whatsappError = $exception->getMessage();
                Log::warning('No se pudo enviar notificacion a WhatsApp', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return [
            'sent_emails' => $sentEmails,
            'failed_emails' => $failedEmails,
            'whatsapp_sent' => $whatsappSent,
            'whatsapp_error' => $whatsappError,
            'participants_count' => $participants->count(),
        ];
    }

    private function buildMessage(Collection $matches): string
    {
        $lines = [
            'Partidos programados para hoy:',
            '',
        ];

        foreach ($matches as $match) {
            if (! $match instanceof MatchGame) {
                continue;
            }

            $lines[] = sprintf(
                '- %s | %s vs %s | %s',
                $match->match_date?->format('d/m H:i') ?? 'Sin hora',
                $match->homeTeam?->name ?? 'Local',
                $match->awayTeam?->name ?? 'Visitante',
                $match->phase
            );
        }

        $lines[] = '';
        $lines[] = 'Ingresa a la plataforma y registra tus pronosticos antes del cierre.';

        return implode(PHP_EOL, $lines);
    }
}
