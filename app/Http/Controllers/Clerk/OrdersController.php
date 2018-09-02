<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Models\Category;
use App\Http\Models\Goods;
use App\Http\Models\Order;
use App\Http\Models\Shop;
use Illuminate\Http\Request;

class OrdersController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('clerk.orders.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::select(['id', 'volume', 'name'])->get();
        $newCategory = [];
        foreach ($categories as $category) {
            $newCategory[$category->id] = $category;
        }

        $products = Goods::withTrashed()->orderBy('category_id')->get();

        $data = [];

        foreach ($products as $product) {
            $data[$product->category_id]['volume'] = $newCategory[$product->category_id]['volume'];
            $data[$product->category_id]['items'][] = $product;
        }

        $categoryOne = [20, 21, 22, 23, 24, 25];
        $categoryTwo = [26, 27, 28, 29];
        $categoryThree = [30, 31, 32, 33];

        //$products = $this->getTree();
        return view('clerk.orders.create', compact('products', 'data', 'categoryOne', 'categoryTwo', 'categoryThree'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        $orderInfo = $order->details;

        $order['status'] =  $this->getStatus($order['status']);
        $order['pay_type'] = $this->getPayType($order['pay_type']);

        $packages = [];

        foreach ($orderInfo as $item) {
            $packages[$item['package_num']][] = $item;
        }

        return view('clerk.orders.show', compact('order', 'orderInfo', 'packages'));
    }

    /**
     * 列表API
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $perPage = $request->get('limit'); // 每页数量由首页控制
        $orderSn = $request->get('order_sn');
        $shopId = $request->get('shop_id');
        $status = $request->get('status');
        $payType = $request->get('pay_type');
        $startTime = $request->get('start_time');
        $stopTime = $request->get('stop_time');

        $orm = Order::query();

        $shopId && $orm->where('shop_id', '=', $shopId);
        $orderSn && $orm->where('order_sn', 'like', "{$orderSn}%");
        is_numeric($status) && $orm->where('status', '=', $status);
        is_numeric($payType) && $orm->where('pay_type', '=', $payType);
        $startTime && $orm->where('created_at', '>=', $startTime);
        $stopTime && $orm->where('created_at', '<=', $stopTime);

        $data = $orm->orderBy('id', 'desc')->paginate($perPage);

        $items = $data->items();

        foreach ($items as &$item) {
            $item['shop_name'] = $item->shop->name;
            $item['member_name'] = $item->member->username;
            $item['status_type'] = $item['status'];
            $item['status'] = $this->getStatus($item['status']);
            $item['pay_type'] = $this->getPayType($item['pay_type']);
        }

        $result = [
            'code'  =>  0,
            'msg'   =>  '',
            'count' => $data->total(),
            'data'  => $items
        ];
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * 订单状态
     * @param $status
     * @return mixed
     */
    protected function getStatus($status)
    {
        return config('web.order_status')[$status];
    }

    /**
     * 支付方式
     * @param $type
     * @return mixed
     */
    protected function getPayType($type)
    {
        return config('web.pay_type')[$type];
    }

    /**
     * @return array
     */
    protected function getTree()
    {
        $data = [
            'base' => [
                ['name' => '茶底', 'class_name' => ''],
                ['name' => '牛奶', 'class_name' => ''],
                ['name' => '其他', 'class_name' => ''],
            ],
            'level-one' => [
                'volume' => '250ml',
                'hidden' => 0,
                'base-0' => [
                    ['id' => 1, 'name' => '回甘普洱', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ['id' => 2,'name' => '茉莉绿茶', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ['id' => 3,'name' => '雨前龙井', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ['id' => 4,'name' => '正山小种', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ['id' => 5,'name' => '金凤茶王', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                ],
                'base-1' => [
                    ['id' => 6,'name' => '脱脂奶', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ['id' => 7,'name' => '全脂奶', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                ],
                'base-2' => [
                    ['id' => 8,'name' => '气泡水', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ['id' => 9,'name' => '乳酸菌', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ['id' => 10,'name' => '冰块', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ['id' => 11,'name' => '直饮水', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0]
                ],
            ],
            'level-one-choose' => [
                'volume' => '50ml',
                'hidden' => 0,
                'base-0' => [
                    ['id' => 12,'name' => '调和奶', 'image' => '', 'calorie' =>  20, 'choose' =>  0, 'hidden' =>  0],
                    ['id' => 13,'name' => '鲜奶', 'image' => '', 'calorie' =>  20, 'choose' =>  0, 'hidden' =>  0],
                    ['id' => 14,'name' => '无', 'image' => '', 'calorie' =>  20, 'choose' =>  0, 'hidden' =>  0],
                ],
                'base-1' => [
                    ['id' => 15,'name' => '可可/黑巧/白巧', 'image' => '', 'calorie' =>  20, 'choose' =>  0, 'hidden' =>  0],
                    ['id' => 16,'name' => '椰浆', 'image' => '', 'calorie' =>  20, 'choose' =>  0, 'hidden' =>  0],
                    ['id' => 17,'name' => '抹茶', 'image' => '', 'calorie' =>  20, 'choose' =>  0, 'hidden' =>  0],
                ],
                'base-2' => [
                    ['id' => 18,'name' => '蝶豆花', 'image' => '', 'calorie' =>  20, 'choose' =>  0, 'hidden' =>  0],
                    ['id' => 19,'name' => '白朗姆酒', 'image' => '', 'calorie' =>  20, 'choose' =>  0, 'hidden' =>  0]
                ],
            ],
            'level-two' => [
                'volume' => '150ml',
                'hidden'=> 0,
                'name'=> '',
                'base-0' => [
                    'name' => '果瓜类',
                    'items' => [
                        ['id' => 20, 'name' => '草莓', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 21, 'name' => '芒果', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 22, 'name' => '水蜜桃', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 23, 'name' => '猕猴桃', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 24, 'name' => '西瓜', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 25, 'name' => '葡萄', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0]
                    ]
                ],
                'base-1' => [
                    'name' => '柑橘类',
                    'items' => [
                        ['id' => 26, 'name' => '西柚', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 27, 'name' => '橙子', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 28, 'name' => '黄柠檬', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 29, 'name' => '青柠檬', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0]
                    ]
                ],
                'base-2' => [
                    'name' => '谷物类',
                    'items' => [
                        ['id' => 30, 'name' => '红豆', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 31, 'name' => '绿豆', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 32, 'name' => '玉米', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 33, 'name' => '燕麦', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0]
                    ]
                ],
                'base-3' => [
                    'name' => '其他',
                    'items' => [
                        ['id' => 34, 'name' => '一级品类双倍', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0, 'volume' => '250',]
                    ]
                ]
            ],
            'level-three' => [
                'volume' => '0',
                'hidden' => 0,
                'base-0' => [
                    'name' => '糖浆',
                    'items' => [
                        ['id' => 35, 'name' => '多糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 36, 'name' => '正常', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 37, 'name' => '少糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 38, 'name' => '半糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 39, 'name' => '无糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ]
                ],

                'base-1'  => [
                    'name' => '黑糖浆',
                    'items' => [
                        ['id' => 40, 'name' => '多糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 41, 'name' => '正常', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 42, 'name' => '少糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 43, 'name' => '半糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 44, 'name' => '无糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ]
                ],
                'base-2'  => [
                    'name' => '焦糖浆',
                    'items' => [
                        ['id' => 45, 'name' => '多糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 46, 'name' => '正常', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 47, 'name' => '少糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 48, 'name' => '半糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 49, 'name' => '无糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ]
                ],
            ],
            'level-four' => [
                'volume' => '25ml',
                'hidden' => 0,
                'base-0' => [
                    'name' => '配料类',
                    'items' => [
                        ['id' => 50, 'name' => '琥珀珍珠', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 51, 'name' => '燕麦', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 52, 'name' => '椰果', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 53, 'name' => '布丁', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 54, 'name' => '雪晶灵', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 55, 'name' => '奇亚籽', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 56, 'name' => '奥利奥', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0]
                    ]
                ]
            ],
            'level-five' => [
                'volume' => '50ml',
                'hidden' => 0,
                'base-0' => [
                    'name' => '奶盖',
                    'items' => [
                        ['id' => 57, 'name' => '海盐奶盖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 58, 'name' => '芝士奶盖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ]
                ]
            ],
            'level-six' => [
                'volume' => '0',
                'hidden' => 0,
                'base-0' => [
                    'name' => '',
                    'items' => [
                        ['id' => 59, 'name' => '烤焦糖', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 60, 'name' => '面包', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                        ['id' => 61, 'name' => '坚果碎类', 'image'=> '', 'calorie' => 20, 'choose' => 0, 'hidden' => 0],
                    ]
                ]
            ],
        ];
        return $data;
    }
}
