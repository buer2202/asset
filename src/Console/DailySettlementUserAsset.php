<?php

namespace Buer\Asset\Console;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Buer\Asset\Repositories\UserAssetDailyRepository;

// 用户资产日结
class DailySettlementUserAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-settlement:user-asset {date=yesterday}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User asset daily settlement.';

    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserAssetDailyRepository $repository)
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
            $date = Carbon::yesterday()->toDateString();
        }

        $result = $this->repository->generateAllUserDaily($date);

        if (!$result) {
            Log::warning('用户资产日结，重复做日结的用户', $this->repository->getSettlementedUserIds());
            Log::warning('用户资产日结，做日结失败的用户', $this->repository->getUnsettlementedUserIds());
        }
    }
}
