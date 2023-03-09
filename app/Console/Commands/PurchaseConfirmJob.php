<?php

namespace App\Console\Commands;

use App\Enums\PurchaseStatus;
use App\Models\AutoPurchaseRecord;
use App\Services\AxieService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class PurchaseConfirmJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchase:confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '查询Axie购买结果';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AxieService $axieService)
    {
        $records = AutoPurchaseRecord::query()
            ->where('status', PurchaseStatus::WAITING)
            ->get();
        /** @var AutoPurchaseRecord $record */
        foreach ($records as $record) {
            $latestAxieInfo = $axieService->getAxie($record->axie_id);
            $currentOwner = Arr::get($latestAxieInfo, 'owner');
            $myWallet = config('services.wallet.public_key');
            if ($currentOwner != $record->owner) {
                if ($currentOwner == $myWallet) {
                    $record->status = PurchaseStatus::DONE;
                } else {
                    $record->remark = 'purchased by user[' . $currentOwner . ']';
                    $record->status = PurchaseStatus::FAIL;
                }
                $record->save();
            }
        }
        return 0;
    }
}
