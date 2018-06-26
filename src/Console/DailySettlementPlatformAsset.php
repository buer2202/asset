<?php

namespace Buer\Asset\Console;

use Illuminate\Console\Command;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Buer\Asset\Repositories\PlatformAssetDailyRepository;

class DailySettlementPlatformAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-settlement:platform-asset {date=yesterday}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Platform asset daily settlement.';

    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PlatformAssetDailyRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = $this->argument('date');
        if ($date == 'yesterday') {
            $dailyDate = Carbon::yesterday()->toDateString();
        } else {
            $dailyDate = $date;
        }

        try {
            $this->repository->generateDaily($dailyDate);
        }
        catch (Exception $e) {
            Log::warning('平台资产日结：' . $e->getMessage());
        }
    }
}
