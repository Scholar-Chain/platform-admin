<?php

namespace App\Listeners;

use App\Services\FonnteService;
use App\Events\PublisherRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWhatsappNotificationToPublisherRegistered implements ShouldQueue
{
    use InteractsWithQueue;

    private $fonnteService;

    /**
     * Create the event listener.
     */
    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Handle the event.
     */
    public function handle(PublisherRegistered $event): void
    {
        $message = "Halo {$event->user->name}! 👋\n\nTerima kasih telah mendaftar di *Scholar Chain* 🎓\nPendaftaran Anda telah kami terima dan saat ini sedang menunggu proses verifikasi oleh admin.\n\nKami akan segera menghubungi Anda setelah akun Anda berhasil diverifikasi.\n\nJika ada pertanyaan, silakan hubungi tim kami kapan saja.\n\nTerima kasih telah bergabung bersama Scholar Chain! 🙌";
        $this->fonnteService->sendMessage(
            target: $event->user->phone_number,
            message: $message,
        );
    }
}
