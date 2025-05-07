<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\JadwalPelayanan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifikasiJadwalBaru extends Notification
{
    use Queueable;
    protected $jadwal;
    /**
     * Create a new notification instance.
     */
    public function __construct(JadwalPelayanan $jadwal)
    {
        $this->jadwal = $jadwal;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Jadwal Pelayanan Baru')
            ->line('Jadwal pelayanan baru untuk Anda.')
            ->line('Tanggal: ' . $this->jadwal->date)
            ->line('Sesi: ' . $this->jadwal->jadwal)
            ->action('Lihat jadwal', route('user.jadwal-pelayanan'))
            ->line('Terima kasih');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
