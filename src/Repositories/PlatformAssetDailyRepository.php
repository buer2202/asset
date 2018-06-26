<?php
namespace Buer\Asset\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Buer\Asset\Models\PlatformAmountFlow;
use Buer\Asset\Models\PlatformAssetDaily;

class PlatformAssetDailyRepository
{
    /**
     * 做日结
     * @param date $date 格式：'2017-10-12'
     */
    public function generateDaily($date)
    {
        $carbonObj = Carbon::parse($date);
        $timeStart = $carbonObj->toDateTimeString();
        $timeEnd   = $carbonObj->addSeconds(86399)->toDateTimeString(); // 当日23:59:59

        // 判断数据是否存在
        $data = PlatformAssetDaily::find($date);
        if(!empty($data)) {
            throw new Exception('已做过平台资产日结');
        }

        // 取平台资金当日最后一笔流水
        $thatDayLastFlow = PlatformAmountFlow::where('created_at', '<=', $timeEnd)->orderBy('created_at', 'desc')->first();

        // 取平台资金当日统计
        $thatDayAggregate = PlatformAmountFlow::whereBetween('created_at', [$timeStart, $timeEnd])
            ->groupBy('trade_type')
            ->select(DB::raw('trade_type, SUM(fee) AS amount, COUNT(fee) AS quantity'))
            ->get()
            ->keyBy('trade_type');

        $platformAssetDaily = new PlatformAssetDaily;
        $platformAssetDaily->date                 = $date;
        $platformAssetDaily->amount               = $thatDayLastFlow->amount ?? 0;
        $platformAssetDaily->managed              = $thatDayLastFlow->managed ?? 0;
        $platformAssetDaily->balance              = $thatDayLastFlow->balance ?? 0;
        $platformAssetDaily->frozen               = $thatDayLastFlow->frozen ?? 0;

        $platformAssetDaily->recharge             = $thatDayAggregate[1]['amount'] ?? 0;
        $platformAssetDaily->total_recharge       = $thatDayLastFlow->total_recharge ?? 0;

        $platformAssetDaily->withdraw             = abs($thatDayAggregate[2]['amount'] ?? 0);
        $platformAssetDaily->total_withdraw       = $thatDayLastFlow->total_withdraw ?? 0;

        $platformAssetDaily->consume              = $thatDayAggregate[5]['amount'] ?? 0;
        $platformAssetDaily->total_consume        = $thatDayLastFlow->total_consume ?? 0;

        $platformAssetDaily->refund               = abs($thatDayAggregate[6]['amount'] ?? 0);
        $platformAssetDaily->total_refund         = $thatDayLastFlow->total_refund ?? 0;

        $platformAssetDaily->trade_quantity       = $thatDayAggregate[8]['quantity'] ?? 0;
        $platformAssetDaily->total_trade_quantity = $thatDayLastFlow->total_trade_quantity ?? 0;

        $platformAssetDaily->trade_amount         = abs($thatDayAggregate[8]['amount'] ?? 0);
        $platformAssetDaily->total_trade_amount   = $thatDayLastFlow->total_trade_amount ?? 0;

        if (!$platformAssetDaily->save()) {
            throw new Exception('日结记录保存失败');
        }

        return true;
    }

    /**
     * 跑历史数据（一般用来手动）
     * @param date $dateStart 格式：'2017-10-12'
     * @param date $dateEnd 格式：'2017-10-12'
     */
    public function scriptrun($dateStart, $dateEnd)
    {
        $start = Carbon::parse($dateStart);
        $end = Carbon::parse($dateEnd);

        while($start->lte($end)) {
            $this->generateDaily($start->toDateString());
            $start->addDay();
        }

        return true;
    }
}
