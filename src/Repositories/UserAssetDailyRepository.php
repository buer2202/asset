<?php
namespace Buer\Asset\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Buer\Asset\Models\UserAsset;
use Buer\Asset\Models\UserAmountFlow;
use Buer\Asset\Models\UserAssetDaily;

class UserAssetDailyRepository
{
    private $_settlementedUserIds = [];
    private $_unsettlementedUserIds = [];

    public function generateAllUserDaily($date)
    {
        $return = true;

        foreach (UserAsset::all() as $user) {
            $result = $this->generateUserDaily($date, $user->user_id);
            if (!$result) {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * 做日结
     * @param date $date 格式：'2017-10-12'
     * @param int $userId
     */
    public function generateUserDaily($date, $userId)
    {
        $carbonObj = Carbon::parse($date);
        $timeStart = $carbonObj->toDateTimeString();
        $timeEnd   = $carbonObj->addSeconds(86399)->toDateTimeString(); // 当日23:59:59

        // 判断数据是否存在
        $data = UserAssetDaily::where('date', $date)->where('user_id', $userId)->first();
        if(!empty($data)) {
            $this->_settlementedUserIds[] = $userId;
            return false;
        }

        // 取用户资金当日最后一笔流水
        $thatDayLastFlow = UserAmountFlow::where('user_id', $userId)->where('created_at', '<=', $timeEnd)->orderBy('created_at', 'desc')->first();

        // 取用户资金当日统计
        $thatDayAggregate = UserAmountFlow::where('user_id', $userId)->whereBetween('created_at', [$timeStart, $timeEnd])
            ->groupBy('trade_type')
            ->select(DB::raw('trade_type, SUM(fee) AS amount'))
            ->get()
            ->keyBy('trade_type');

        $userAssetDaily = new UserAssetDaily;
        $userAssetDaily->date           = $date;
        $userAssetDaily->user_id        = $userId;
        $userAssetDaily->balance        = $thatDayLastFlow->balance ?? 0;
        $userAssetDaily->frozen         = $thatDayLastFlow->frozen ?? 0;

        $userAssetDaily->recharge       = $thatDayAggregate[1]['amount'] ?? 0;
        $userAssetDaily->total_recharge = $thatDayLastFlow->total_recharge ?? 0;

        $userAssetDaily->withdraw       = abs($thatDayAggregate[2]['amount'] ?? 0);
        $userAssetDaily->total_withdraw = $thatDayLastFlow->total_withdraw ?? 0;

        $userAssetDaily->consume        = abs($thatDayAggregate[5]['amount'] ?? 0);
        $userAssetDaily->total_consume  = $thatDayLastFlow->total_consume ?? 0;

        $userAssetDaily->refund         = $thatDayAggregate[6]['amount'] ?? 0;
        $userAssetDaily->total_refund   = $thatDayLastFlow->total_refund ?? 0;

        $userAssetDaily->expend         = abs($thatDayAggregate[7]['amount'] ?? 0);
        $userAssetDaily->total_expend   = $thatDayLastFlow->total_expend ?? 0;

        $userAssetDaily->income         = $thatDayAggregate[8]['amount'] ?? 0;
        $userAssetDaily->total_income   = $thatDayLastFlow->total_income ?? 0;

        if (!$userAssetDaily->save()) {
            $this->_unsettlementedUserIds[] = $userId;
            return false;
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

    public function getSettlementedUserIds()
    {
        return $this->_settlementedUserIds;
    }

    public function getUnsettlementedUserIds()
    {
        return $this->_unsettlementedUserIds;
    }
}
