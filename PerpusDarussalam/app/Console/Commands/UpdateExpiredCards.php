<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateExpiredCards extends Command
{
    protected $signature = 'cards:update-expired';
    protected $description = 'Update status_kartu jadi expired untuk user yang masa berlakunya sudah lewat';

    public function handle()
    {
        $count = User::where('masa_berlaku_sampai', '<', now())
            ->where('status_kartu', 'aktif')
            ->update(['status_kartu' => 'expired']);

        $this->info("Berhasil update {$count} kartu menjadi expired.");
    }
}