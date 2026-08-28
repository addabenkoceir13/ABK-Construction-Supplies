<?php

namespace App\Console\Commands;

use App\Models\Debt;
use App\Support\ArabicNormalizer;
use Illuminate\Console\Command;

class BackfillNormalizedSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:backfill-normalized';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill fullname_normalized/phone_normalized on debts. Idempotent, safe to re-run.';

    public function handle(): int
    {
        $processed = 0;
        $malformed = [];

        // withTrashed(): soft-deleted debts get backfilled too (in case of a
        // future restore) — they never leak into search results regardless,
        // since DebtSearchQuery queries through Debt::query(), which applies
        // the default SoftDeletingScope automatically.
        Debt::withTrashed()->chunkById(200, function ($debts) use (&$processed, &$malformed) {
            foreach ($debts as $debt) {
                $debt->fullname_normalized = ArabicNormalizer::name($debt->fullname ?? '');
                $numbers = ArabicNormalizer::phones($debt->phone ?? '');
                $debt->phone_normalized = implode('/', $numbers);
                $debt->saveQuietly();

                $processed++;

                foreach ($numbers as $number) {
                    if (strlen($number) !== 10) {
                        $malformed[] = [
                            'debt_id' => $debt->id,
                            'raw' => $debt->phone,
                            'normalized' => $number,
                        ];
                    }
                }
            }
        });

        $this->info("Processed: {$processed} debts.");
        $this->info('Malformed phone numbers (not 10 digits, kept as-is): ' . count($malformed));

        foreach ($malformed as $entry) {
            $this->line("  debt #{$entry['debt_id']}: '{$entry['raw']}' -> '{$entry['normalized']}'");
        }

        return self::SUCCESS;
    }
}
