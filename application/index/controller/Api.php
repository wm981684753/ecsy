<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019 
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 

// +----------------------------------------------------------------------

namespace app\index\controller;

use library\Controller;
use think\Db;

/**
 * 支付控制器
 */
class Api extends Controller
{

    public $BASE_URL = "https://bapi.app";
    protected $appKey = '';
    protected $appSecret = '';

    public function __construct()
    {
        $this->appKey = config('bipay.appKey');
        $this->appSecret = config('bipay.appSecret');
    }

    public function bipay()
    {
        $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid']: '';
        if ($oid) {
            $r = db('xy_recharge')->where('id',$oid)->find();
            if ($r) {
                $notifyUrl = url('/index/api/bipay_notify');
                $returnUrl = url('/index/api/bipay_return');
                $price = $r['num'] * 100;
                $res = $this->create_order($oid,$price,'用户充值',$notifyUrl, $returnUrl);

                if ($res && $res['code']==200) {
                    $url = $res['data']['pay_url'];
                    $this->redirect($url);
                }
            }
        }
    }

    public function bipay_return()
    {
        return $this->fetch();
    }


    public function bipay_notify()
    {

        $content = file_get_contents('php://input');
        $post    = (array)json_decode($content, true);
        file_put_contents("bipay_notify.log",$content."\r\n",FILE_APPEND);

        if (!$post['order_id']) {
            die('fail');
        }
        $oid = $post['order_id'];
        $r = db('xy_recharge')->where('id',$oid)->find();
        if (!$r) {
            die('fail');
        }
        if ($post['order_state']!=1) {
            die('fail');
        }

        if ($r['status'] == 2){
            die('SUCCESS');
        }

        if ($post['order_state']) {
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);
            $oinfo = $r;
            $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
            $res2 = Db::name('xy_balance_log')
                ->insert([
                    'uid'=>$oinfo['uid'],
                    'oid'=>$oid,
                    'num'=>$oinfo['num'],
                    'type'=>1,
                    'status'=>1,
                    'addtime'=>time(),
                ]);
            /************* 发放推广奖励 *********/
            $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
            if($uinfo['active']===0){
                Db::name('xy_users')->where('id',$uinfo['id'])->update(['active'=>1]);
                //将账号状态改为已发放推广奖励
                $userList = model('Users')->parent_user($uinfo['id'],3);
                if($userList){
                    foreach($userList as $v){
                        if($v['status']===1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                            Db::name('xy_reward_log')
                                ->insert([
                                    'uid'=>$v['id'],
                                    'sid'=>$uinfo['id'],
                                    'oid'=>$oid,
                                    'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                    'lv'=>$v['lv'],
                                    'type'=>1,
                                    'status'=>1,
                                    'addtime'=>time(),
                                ]);
                        }
                    }
                }
            }
            /************* 发放推广奖励 *********/
            die('SUCCESS');
        }
    }


    public function create_order(
        $orderId, $amount, $body, $notifyUrl, $returnUrl, $extra = '', $orderIp = '', $amountType = 'CNY', $lang = 'zh_CN')
    {
        $reqParam = [
            'order_id' => $orderId,
            'amount' => $amount,
            'body' => $body,
            'notify_url' => $notifyUrl,
            'return_url' => $returnUrl,
            'extra' => $extra,
            'order_ip' => $orderIp,
            'amount_type' => $amountType,
            'time' => time() * 1000,
            'app_key' => $this->appKey,
            'lang' => $lang
        ];
        $reqParam['sign'] = $this->create_sign($reqParam, $this->appSecret);
        $url = $this->BASE_URL . '/api/v2/pay';
        return $this->http_request($url, 'POST', $reqParam);
    }

    /**
     * @return {
     * bapp_id: "2019081308272299266f",
     * order_id: "1565684838",
     * order_state: 0,
     * body: "php-sdk sample",
     * notify_url: "https://sdk.b.app/api/test/notify/test",
     * order_ip: "",
     * amount: 1,
     * amount_type: "CNY",
     * amount_btc: 0,
     * pay_time: 0,
     * create_time: 1565684842076,
     * order_type: 2,
     * app_key: "your_app_key",
     * extra: ""
     * }
     */
    public function get_order($orderId)
    {
        $reqParam = [
            'order_id' => $orderId,
            'time' => time() * 1000,
            'app_key' => $this->appKey
        ];
        $reqParam['sign'] = $this->create_sign($reqParam, $this->appSecret);
        $url = $this->BASE_URL . '/api/v2/order';
        return $this->http_request($url, 'GET', $reqParam);
    }

    public function is_sign_ok($params)
    {
        $sign = $this->create_sign($params, $this->appSecret);
        return $params['sign'] == $sign;
    }

    public function create_sign($params, $appSecret)
    {
        $signOriginStr = '';
        ksort($params);
        foreach ($params as $key => $value) {
            if (empty($key) || $key == 'sign') {
                continue;
            }
            $signOriginStr = "$signOriginStr$key=$value&";
        }
        return strtolower(md5($signOriginStr . "app_secret=$appSecret"));
    }

    private function http_request($url, $method = 'GET', $params = [])
    {
        $curl = curl_init();

        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            $jsonStr = json_encode($params);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonStr);
        } else if ($method == 'GET') {
            $url = $url . "?" . http_build_query($params, '', '&');
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        $output = curl_exec($curl);
        if (curl_errno($curl) > 0) {
            return [];
        }
        curl_close($curl);
        $json = json_decode($output, true);

        return $json;
    }


    //----------------------------------------------------------------
    //  paysapi
    //----------------------------------------------------------------

    public function pay(){

        $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid']: '';
        if ($oid) {
            $r = db('xy_recharge')->where('id',$oid)->find();
            if ($r) {
                $notifyUrl = url('/index/api/bipay_notify');
                $returnUrl = url('/index/api/bipay_return');
                $price = $r['num'] * 100;
                $res = $this->create_order($oid,$price,'用户充值',$notifyUrl, $returnUrl);

                if ($res && $res['code']==200) {
                    $url = $res['data']['pay_url'];
                    $this->redirect($url);
                }
            }
        }



            $goodsname = '用户充值';
            if (substr($recharge['order_no'], 0, 4) == 'VIPC') {
                $goodsname = '用户升级';
            }

            $order_no = $recharge['order_no'];


            $istype = 1;//1支付宝  2微信 3 比特币支付


            $return_url = $payType['store_frontend_redirect_url'];
            $notify_url = $payType['store_notify_url'];;

            $uid   = $payType['store_name'];    //"此处填写Yipay的uid";
            $token = $payType['store_key'];     //"此处填写Yipay的Token";

            $orderid  = $order_no;
            $orderuid = $recharge['member_id'];
            $price    = $recharge['price'];

            $key = md5($goodsname. $istype . $notify_url . $orderid . $orderuid . $price . $return_url . $token. $uid);

            $data = array(
                'goodsname'=>$goodsname,
                'istype'=>$istype,
                'key'=>$key,
                'notify_url'=>$notify_url,
                'orderid'=>$orderid,
                'orderuid'=>$orderuid,
                'price'=>$price,
                'return_url'=>$return_url,
                'uid'=>$uid
            );
            $this->assign('data',$data);
            $this->assign('post_url',self::POST_URL);
            $this->display();
        



    }



}