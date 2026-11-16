<?php

namespace App\Console\Commands;

use App\Models\Queue;
use Illuminate\Console\Command;

class ResetDailyQueues extends Command
{
    protected $signature = 'queue:reset-daily';
    protected $description = 'Mereset antrean harian aktif sehingga nomor antrean berikutnya dimulai kembali dari 1';

    public function handle(): int
    {
        $updated = Queue::whereDate('queue_date', now()->toDateString())
            ->whereIn('status', ['WAITING', 'CALLED'])
            ->update(['status' => 'RESET']);

        $this->info('[' . now()->toDateTimeString() . "] Job reset antrean harian selesai. {$updated} antrean diarsipkan, nomor berikutnya kembali ke 1.");
        return self::SUCCESS;
    }
}
