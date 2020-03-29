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
 * 定时器
 */
class Crontab extends Controller
{
    //冻结订单
    public function freeze_order()
    {
        $timeout = time()-config('deal_timeout');//超时订单
        $oinfo = Db::name('xy_convey')->where('status',0)->where('addtime','<=',$timeout)->field('id')->select();
		var_dump($oinfo);exit;
        if($oinfo){
            foreach ($oinfo as $v) {
                Db::name('xy_convey')->where('id',$v['id'])->update(['status'=>5,'endtime'=>time()]);
            }
        }
        $this->cancel_order();
        $this->reset_deal();
		echo 'success';
    }

    //强制取消订单并冻结账户 
    public function cancel_order()
    {
        $timeout = time()-config('deal_timeout');//超时订单
        //$oinfo = Db::name('xy_convey')->field('id oid,uid')->where('status',5)->where('endtime','<=',$timeout)->select();
        $oinfo = Db::name('xy_convey')->field('id oid,uid')->where('status',0)->where('endtime','<=',$timeout)->select();
        if($oinfo){
            foreach ($oinfo as $v) {
                Db::name('xy_convey')->where('id',$v['oid'])->update(['status'=>4,'endtime'=>time()]);
                $tmp =Db::name('xy_users')->field('deal_error,deal_status')->find($v['uid']);
                //记录违规信息
                if($tmp['deal_status']!=0){
                    if($tmp['deal_error'] < (int)config('deal_error')){
                        Db::name('xy_users')->where('id',$v['uid'])->update(['deal_status'=>1,'deal_error'=>Db::raw('deal_error+1')]);
                        Db::name('xy_user_error')->insert(['uid'=>$v['uid'],'oid'=>$v['oid'],'addtime'=>time(),'type'=>2]);
                    }elseif ($tmp['deal_error'] >= (int)config('deal_error')) {
                        Db::name('xy_users')->where('id',$v['uid'])->update(['deal_status'=>1,'deal_error'=>0]);
                        Db::name('xy_user_error')->insert(['uid'=>$v['uid'],'oid'=>$v['oid'],'addtime'=>time(),'type'=>3]);
                        //记录交易冻结信息
                    }
                }
            }
        }
    }
	
	public function start()
    {
        $oinfo = Db::name('xy_convey')->where('status',5)->where('endtime','<=',time())->select();
        if ($oinfo) {
            //
            foreach ($oinfo as $v) {
                //
                Db::name('xy_convey')->where('id',$v['id'])->update(['status'=>1]);

                //
                $res1 = Db::name('xy_users')
                    ->where('id', $v['uid'])
                    //->dec('balance',$info['num'])//
                    ->inc('balance',$v['num'])
                    //->inc('freeze_balance',$info['num']+$info['commission']) //冻结商品金额 + 佣金//
                    ->dec('freeze_balance',$v['num']) //冻结商品金额 + 佣金
                    ->update(['deal_status'=>1]);
               // $this->deal_reward($v['uid'],$v['id'],$v['num'],$v['commission']);

                //
            }

        }
        $this->cancel_order();
        $this->reset_deal();
        //var_dump($oinfo,time(),date('Y-m-d H:i:s', 1577812622));die;
        return json(['code'=>1,'info'=>'执行成功！']);
    }
	
    //解冻账号
    public function reset_deal()
    {
        $uinfo = Db::name('xy_users')->where('deal_status',0)->field('id')->select();
        if($uinfo){
            foreach ($uinfo as $v) {
                $time = Db::name('xy_user_error')->where('uid',$v['id'])->where('type',3)->order('addtime desc')->limit(1)->value('addtime');
                if($time || $time <= time()-config('deal_feedze')){ 
                    //解封账号
                    Db::name('xy_users')->where('id',$v['id'])->update(['deal_status'=>1]);
                    Db::name('xy_user_error')->insert(['uid'=>$v['id'],'oid'=>'-','addtime'=>time(),'type'=>1]);
                }
            }
        }
    }

    //发放佣金
    public function do_reward()
    {
        try {
            $time = strtotime(date('Y-m-d', time()));//获取当天凌晨0点的时间戳
            $data = Db::name('xy_reward_log')->where('addtime','between', time()-3600*24 . ',' . time() )->where('status',1)->select();//获取当天佣金
            if($data){
                foreach ($data as $k => $v) {
                    Db::name('xy_users')->where('id',$v['uid'])->setInc('balance',$v['num']);
                    Db::name('xy_reward_log')->where('id',$v['id'])->update(['status'=>2,'endtime'=>time()]);
                }
            }
            echo 1;
        } catch (\Throwable $th) {
            //throw $th;
        }
        echo 'success';
    }


    //定时器 解除冻结 反还佣金和本金
    





    public function delOrder()
    {
		

    }
}