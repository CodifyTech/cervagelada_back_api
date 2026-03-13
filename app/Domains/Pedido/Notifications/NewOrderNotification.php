<?php

namespace App\Domains\Pedido\Notifications;

use App\Domains\Pedido\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Pedido $pedido,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $total = number_format($this->pedido->total, 2, ',', '.');

        return (new MailMessage)
            ->subject('Novo pedido recebido!')
            ->greeting('Olá, '.($notifiable->name ?? 'Lojista').'!')
            ->line('Você recebeu um novo pedido com pagamento confirmado.')
            ->line("Valor total: R$ {$total}")
            ->line('Itens: '.$this->pedido->itemPedidos->count().' produto(s)')
            ->action('Ver Pedido', url('/pedidos'))
            ->line('Acesse o painel para aceitar e preparar o pedido.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'pedido_id' => $this->pedido->id,
            'total' => $this->pedido->total,
            'itens_count' => $this->pedido->itemPedidos->count(),
            'message' => 'Novo pedido recebido - R$ '.number_format($this->pedido->total, 2, ',', '.'),
        ];
    }
}
