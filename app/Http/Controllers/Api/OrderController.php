<?php
/**
 * Created by PhpStorm.
 * User: PINYER Co ltd
 * Date: 2018/9/21
 * Time: 11:03
 */
namespace App\Http\Controllers\Api;

use App\Http\Models\Coupon;
use App\Http\Models\Member;
use App\Http\Models\MemberCoupon;
use App\Http\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Models\Goods;
use App\Http\Models\Order;
use App\Http\Models\Formula;
use App\Http\Models\OrderDetail;
use DB;

class OrderController extends CommonController
{
    // 创建订单
    public function create(Request $request)
    {
        $data = $request->get('data');
        $couponId = $request->get('couponId');

        if (empty($data)) {
            return $this->_error('UNKNOWN_ERROR', '请选择配料');
        }

        $date =  date('Y-m-d H:i:s');
        $cartArr = json_decode($data, true);
        $index = 1;
        $insertData = [];
        $temperatures = [];
        $orderPrice = 0; // 订单总价
        $memberId = $this->getUserId();

        foreach ($cartArr as $data) {
            $temperature = $data['temperature'];
            $sugar = $data['sugar'];
            $weight = $data['weight'];
            $doubleId = (int)$data['double'];
            $goodsIds = $data['list'];
            $sugar && array_push($goodsIds, $sugar); // 有选择糖类
            $temperatures[$index] = $temperature ? : 'ice'; // 设置温度

            $goodsInfo = Goods::whereIn('id', $goodsIds)->select(['id', 'name', 'price', 'image'])->get();

            foreach ($goodsInfo as $goods) {
                $insertData[] = [
                    'goods_id'    => $goods['id'],
                    'goods_name'  => $goods['name'],
                    'goods_image' => $goods['image'],
                    'goods_num'   => $goods['id'] == $doubleId ? 2 : 1,
                    'goods_price' => $goods['price'],
                    'package_num' => $index,
                    'deploy'      => $sugar == $goods['id'] ? $weight : '',
                    'created_at'  => $date
                ];
                $goodsPrice = bcmul($goods['price'], $goods['id'] == $doubleId ? 2 : 1, 2);
                $orderPrice = bcadd($orderPrice, $goodsPrice, 2);
            }
            $index++;
        }

        DB::beginTransaction();
        try{
            $reducedPrice = 0;

            if ($couponId) {
                $reducedPrice = Coupon::where('id', $couponId)
                    ->where('start_time', '<', $date)
                    ->where('stop_time', '>=', $date)
                    ->where(function ($query) use($orderPrice) {
                        $query->where('match_price', '<=', $orderPrice)
                            ->orWhere('match_price', '=', 0);
                    })
                    ->pluck('reduced_price');

                if (empty($reducedPrice)) {
                    throw new \Exception('优惠券不存在');
                }

                MemberCoupon::where(['member_id' => $memberId, 'coupon_id' => $couponId])->update(['used' => 1]);
            }

            Order::insert([
                'shop_id'        => $this->_shopId,
                'member_id'      => $memberId,
                'order_sn'       => $this->getOrderSn(),
                'price'          => bcsub($orderPrice, $reducedPrice, 2),
                'original_price' => $orderPrice,
                'reduced_price'  => $reducedPrice,
                'coupon_id'      => $couponId,
                'pay_type'       => 1,
                'status'         => 0,     // 待支付
                'created_at'     => $date,
                'temperature'    => $temperatures ? serialize($temperatures) : ''
            ]);

            $orderId = DB::getPdo()->lastInsertId();

            if (! $orderId) {
                throw new \Exception('订单新增失败');
            }

            foreach ($insertData as $item) {
                $item['order_id'] = $orderId;

                OrderDetail::insert($item);
            }

            DB::commit();

            return $this->_successful(['orderId' => $orderId]);
        } catch (\Exception $e){
            DB::rollback();//事务回滚

            return $this->error($e->getMessage());
        }
    }

    /**
     * 检测订单  返回店铺名，用户手机号，排队情况，优惠券列表
     * @return array
     */
    public function check(Request $request)
    {
        $memberId =  $this->getUserId();
        $shopName = Shop::where('id', $this->_shopId)->pluck('name');
        $userInfo = Member::where('id', $memberId)->select('telephone', 'gender')->first();
        $now = time();
        $datetime = date('Y-m-d H:i:s');
        $orderPrice = $request->get('orderPrice');

        $position = Order::where(['shop_id' => $this->_shopId, 'status' => 1])->whereBetween('created_at', [strtotime('today'), $now])->count();

        $couponList = DB::table('member_coupons')
            ->join('coupons', 'member_coupons.coupon_id', '=', 'coupons.id')
            ->select(['coupons.id', 'coupons.title', 'coupons.match_price', 'coupons.reduced_price', 'coupons.stop_time'])
            ->where('member_coupons.member_id', $memberId)
            ->where('member_coupons.used', '=', 0)
            ->where(function ($query) use($orderPrice) {
                $query->where('coupons.match_price', '<=', $orderPrice)
                    ->orWhere('coupons.match_price', '=', 0);
            })
            ->where('coupons.stop_time', '>=', $datetime)
            ->where('coupons.start_time', '<', $datetime)
            ->orderBy('coupons.reduced_price', 'desc')
            ->get();

        if (! empty($couponList)) {
            foreach ($couponList as $key => &$item) {
                $item = (array)$item;
                $item['deadline'] = date('Y-m-d', strtotime($item['stop_time']));
            }
        }

        $gender = '';
        if ($userInfo) {
            if ($userInfo['gender'] == 1) {
                $gender = '先生';
            } else if ($userInfo['gender'] == 2) {
                $gender = '小姐';
            }
        }

        $data = [
            'datetime' => date('Y-m-d H:i'),
            'shopName' => $shopName,
            'telephone' => $userInfo ? $userInfo['telephone'] : '',
            'gender' => $gender,
            'position' => $position,
            'coupons' => $couponList
        ];

        return $this->_successful($data);
    }

    // 取消订单
    public function cancel(Request $request)
    {
        $couponId = $request->get('couponId');
        $orderId = $request->get('orderId');
        $memberId = $this->getUserId();

        if (empty($orderId)) {
            return $this->_error('UNKNOWN_ERROR', '订单ID不能为空');
        }

        DB::beginTransaction();
        try{
            if ($couponId) {
                MemberCoupon::where(['member_id' => $memberId, 'coupon_id' => $couponId])->update(['used' => 0]);
            }

            Order::where(['id' => $orderId, 'member_id' => $memberId, 'status' => 0])->update(['status' => 6 ]);

            DB::commit();

            return $this->_successful();
        } catch (\Exception $e){
            DB::rollback();//事务回滚

            return $this->error($e->getMessage());
        }
    }

    protected function getOrderSn()
    {
        do {
            $orderSn = date('YmdHis') . rand(1000, 9999);
        } while (Order::where('order_sn', $orderSn)->count());

        return $orderSn;
    }

    /**
     * 获取制作订单
     */
    public function index()
    {
        // 取最近一条,未完成的
        $orderInfo = Order::where(['member_id' => 1, 'shop_id' => 1, 'status' => 1])
            ->whereBetween('created_at', [date('Y-m-d'), date('Y-m-d H:i:s')])
            ->orderBy('id', 'desc')
            ->first();

        $carts = [];
        $recommends = [];
        //$orderInfo = []; // todo delete
        // 订单状态处理中的
        if (! empty ($orderInfo)) {

            $details = $orderInfo->details->toArray();

            foreach ($details as $detail) {
                $carts[$detail['package_num']][] = $detail;
            }

        } else {
            // 获取推荐
            $formulas = Formula::where('shop_id', 1)
                ->orderBy('likes', 'desc')
                ->limit(15)
                ->get();

            foreach ($formulas as $formula) {
                $orderInfo = Order::find($formula['order_id']);
                $temperature = $orderInfo['temperature'] ? unserialize($orderInfo['temperature']) : [];
                $temps = config('web.temperature');
                $orderDetail = $orderInfo->details->toArray();
                $cart = [];

                foreach ($orderDetail as $detail) {
                    if ($formula['package_num'] == $detail['package_num']) {
                        if (! empty($detail['deploy'])) {
                            $detail['goods_name'] =  $detail['goods_name'] . '(' . $detail['deploy'] . ')';
                        }
                        $cart[] = $detail;
                    }
                }

                // 杯子的温度
                if (isset($temperature[$formula['package_num']])) {
                    $cart[] = ['goods_name' => $temps[$temperature[$formula['package_num']]], 'type' => $temperature[$formula['package_num']], 'id' => -1];
                }

                array_push($recommends, $cart);
            }
        }

        return $this->_successful(['orders' => array_values($carts), 'recommends' => array_values($recommends)]);
    }

    /**
     * 推荐配方
     */
    public function recommend(Request $request)
    {

    }
}