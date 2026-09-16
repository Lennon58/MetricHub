<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Personaliza a notificação de e-mail de verificação
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifique seu endereço de e-mail')
                ->greeting('Olá, ' . $notifiable->name . '!')
                ->line('Clique no botão abaixo para verificar e ativar sua conta em nossa plataforma.')
                ->action('Verificar E-mail', $url)
                ->line('Se você não criou uma conta, nenhuma ação adicional é necessária.')
                ->salutation('Atenciosamente, ' . config('app.name'));
        });
    }
}