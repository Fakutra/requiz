<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Offering;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AutoRejectExpiredOffering extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offering:auto-reject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $offerings = Offering::whereNull('responded_at')
            ->whereNotNull('response_deadline')
            ->where('response_deadline', '<', $now)
            ->with('applicant')
            ->get();

        if ($offerings->isEmpty()) {
            $this->info('Tidak ada offering expired.');
            return Command::SUCCESS;
        }

        foreach ($offerings as $offering) {
            DB::transaction(function () use ($offering, $now) {
                $offering->update([
                    'responded_at' => $now,
                ]);

                if ($offering->applicant) {
                    $offering->applicant->update([
                        'status' => 'Menolak Offering',
                    ]);
                }
            });

            $this->info("Auto reject: {$offering->applicant?->name}");
        }

        return Command::SUCCESS;
    }
}
