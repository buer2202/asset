<?php
namespace Buer\Asset\Methods;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Buer\Asset\Exceptions\AssetException;
use Buer\Asset\Models\UserAmountFlow;
use Buer\Asset\Models\PlatformAmountFlow;
use Buer\Asset\Models\PlatformAsset;
use Buer\Asset\Models\UserAsset;
use Buer\Asset\Models\User;

// 交易基类
abstract class TradeBase
{
    // 1.加款 2.提现 3.冻结 4.解冻 5.消费 6.退款 7.支出 8.收入
    const TRADE_TYPE_RECHARGE = 1;
    const TRADE_TYPE_WITHDRAW = 2;
    const TRADE_TYPE_FREEZE   = 3;
    const TRADE_TYPE_UNFREEZE = 4;
    const TRADE_TYPE_CONSUME  = 5;
    const TRADE_TYPE_REFUND   = 6;
    const TRADE_TYPE_EXPEND   = 7;
    const TRADE_TYPE_INCOME   = 8;

    protected $userAsset          = null;
    protected $platformAsset      = null;
    protected $userAmountFlow     = null;
    protected $platformAmountFlow = null;

    protected $userId;  // 用户ID
    protected $fee;     // 交易金额
    protected $type;    // 交易类型
    protected $subtype; // 交易子类型
    protected $tradeNo; // 交易单号
    protected $remark;  // 备注
    protected $adminUserId;  // 管理员id
    protected $tradeModel;  // 交易模型，用于更新流水的多态关联。
    protected $expendFrom;  // 扣款来源。调用expend和consume方法时有效。可选项: balance (余额), frozen (冻结)。默认从余额扣款

    /**
     *  注：tradeModel必须传入定义了 userAmountFlows() 和 platformAmountFlows() 两个多态方法的模型。
     *  请在模型中use AssetAmountMorphMany 这个trait
     */

    /**
     * subtype 见配置文件 tradetype.user_sub tradetype.platform_sub
     */
    public function __construct($fee, $subtype, $tradeNo, $remark, $userId, $adminUserId = 0, $tradeModel = null, $expendFrom = null)
    {
        if ($fee > -0.0001 && $fee < 0.0001) {
            throw new AssetException('金额范围不正确');
        }

        // 判断是否存在科学计数法
        if (strpos(strtolower($fee), 'e')) {
            throw new AssetException('金额精度不正确');
        }

        if (!empty($tradeModel) && !($tradeModel instanceof Model)) {
            throw new AssetException('交易模型必须是一个模型对象');
        }

        $this->fee         = (float)$fee;
        $this->subtype     = (int)$subtype;
        $this->tradeNo     = trim((string)$tradeNo);
        $this->remark      = trim((string)$remark);
        $this->userId      = (int)$userId;
        $this->adminUserId = (int)$adminUserId;
        $this->tradeModel  = $tradeModel;
        $this->expendFrom  = $expendFrom;

        DB::beginTransaction();

        // 获取用户信息
        $user = User::find($userId);
        if (empty($user)) {
            throw new AssetException('操作资产时检查到用户不存在');
        }

        // 获取用户资产
        $this->userAsset = UserAsset::where('user_id', $this->userId)->lockForUpdate()->first();
        if (empty($this->userAsset)) {
            if (!UserAsset::create(['user_id' => $this->userId])) {
                throw new AssetException('用户资产创建失败');
            }

            $this->userAsset = UserAsset::where('user_id', $this->userId)->lockForUpdate()->first();
        }

        // 获取平台资产
        $this->platformAsset = PlatformAsset::where('id', 1)->lockForUpdate()->first();
        if (empty($this->platformAsset)) {
            if (!PlatformAsset::create(['id' => 1])) {
                throw new AssetException('平台资产创建失败');
            }

            $this->platformAsset = PlatformAsset::where('id', 1)->lockForUpdate()->first();
        }

        $this->beforeUser();
        $this->updateUserAsset();
        $this->createUserAmountFlow();
        $this->beforePlatform();
        $this->updatePlatformAsset();
        $this->createPlatformAmountFlow();

        DB::commit();
        return true;
    }

    // 用户前置操作
    public function beforeUser() {}

    // 更新用户余额
    abstract public function updateUserAsset();

    // 写用户流水
    public function createUserAmountFlow()
    {
        $this->userAmountFlow = new UserAmountFlow;
        $this->userAmountFlow->user_id        = $this->userId;
        $this->userAmountFlow->admin_user_id  = $this->adminUserId;
        $this->userAmountFlow->trade_type     = $this->type;
        $this->userAmountFlow->trade_subtype  = $this->subtype;
        $this->userAmountFlow->trade_no       = $this->tradeNo;
        $this->userAmountFlow->fee            = $this->fee;
        $this->userAmountFlow->remark         = $this->remark;
        $this->userAmountFlow->balance        = $this->userAsset->balance;
        $this->userAmountFlow->frozen         = $this->userAsset->frozen;
        $this->userAmountFlow->total_recharge = $this->userAsset->total_recharge;
        $this->userAmountFlow->total_withdraw = $this->userAsset->total_withdraw;
        $this->userAmountFlow->total_consume  = $this->userAsset->total_consume;
        $this->userAmountFlow->total_refund   = $this->userAsset->total_refund;
        $this->userAmountFlow->total_expend   = $this->userAsset->total_expend;
        $this->userAmountFlow->total_income   = $this->userAsset->total_income;
        $this->userAmountFlow->created_at     = date('Y-m-d H:i:s');

        if (!$this->userAmountFlow->save()) {
            throw new AssetException('流水记录失败');
        }

        // 写多态关联
        if (!empty($this->tradeModel)) {
            if(!method_exists($this->tradeModel, 'userAmountFlows')) {
                throw new AssetException('订单模型未定义多态[userAmountFlows]');
            }

            if (!$this->tradeModel->userAmountFlows()->save($this->userAmountFlow)) {
                throw new AssetException('更新多态信息失败');
            }
        }

        return true;
    }

    // 平台前置操作
    public function beforePlatform() {}

    // 更新平台资金
    abstract public function updatePlatformAsset();

    // 写平台流水
    public function createPlatformAmountFlow()
    {
        $this->platformAmountFlow = new PlatformAmountFlow;
        $this->platformAmountFlow->user_id              = $this->userId;
        $this->platformAmountFlow->admin_user_id        = $this->adminUserId;
        $this->platformAmountFlow->trade_type           = $this->type;
        $this->platformAmountFlow->trade_subtype        = $this->subtype;
        $this->platformAmountFlow->trade_no             = $this->tradeNo;
        $this->platformAmountFlow->fee                  = $this->fee;
        $this->platformAmountFlow->remark               = $this->remark;
        $this->platformAmountFlow->amount               = $this->platformAsset->amount;
        $this->platformAmountFlow->managed              = $this->platformAsset->managed;
        $this->platformAmountFlow->balance              = $this->platformAsset->balance;
        $this->platformAmountFlow->frozen               = $this->platformAsset->frozen;
        $this->platformAmountFlow->total_recharge       = $this->platformAsset->total_recharge;
        $this->platformAmountFlow->total_withdraw       = $this->platformAsset->total_withdraw;
        $this->platformAmountFlow->total_consume        = $this->platformAsset->total_consume;
        $this->platformAmountFlow->total_refund         = $this->platformAsset->total_refund;
        $this->platformAmountFlow->total_trade_quantity = $this->platformAsset->total_trade_quantity;
        $this->platformAmountFlow->total_trade_amount   = $this->platformAsset->total_trade_amount;
        $this->platformAmountFlow->created_at           = date('Y-m-d H:i:s');

        if (!$this->platformAmountFlow->save()) {
            throw new AssetException('流水记录失败');
        }

        // 写多态关联
        if (!empty($this->tradeModel)) {
            if(!method_exists($this->tradeModel, 'platformAmountFlows')) {
                throw new AssetException('订单模型未定义多态[platformAmountFlows]');
            }

            if (!$this->tradeModel->platformAmountFlows()->save($this->platformAmountFlow)) {
                throw new AssetException('更新多态信息失败');
            }
        }

        return true;
    }

    public function getUserAsset()
    {
        return $this->userAsset;
    }

    public function getUserAmountFlow()
    {
        return $this->userAmountFlow;
    }

    public function getPlatformAsset()
    {
        return $this->platformAsset;
    }

    public function getPlatformAmountFlow()
    {
        return $this->platformAmountFlow;
    }
}
