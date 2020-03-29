<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * 下单控制器
 */
class RotOrder extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        $where = [
            ['uid','=',session('user_id')],
            ['addtime','between',strtotime(date('Y-m-d')).','.time()],
        ];
        $this->day_deal = Db::name('xy_convey')->where($where)->where('status','in',[1,3,5])->sum('commission');
        $this->lock_deal = Db::name('xy_users')->where('id',session('user_id'))->sum('freeze_balance');
        $this->price = Db::name('xy_users')->where('id',session('user_id'))->sum('balance');
        $this->day_d_count = Db::name('xy_convey')->where($where)->where('status','in',[0,1,3,5])->count('id');
        $this->day_l_count = Db::name('xy_convey')->where($where)->where('status',5)->count('num');//交易冻结单数

        //var_dump($this->day_l_count);die;
        $this->team_num = Db::name('xy_reward_log')->where('uid',session('user_id'))->where('addtime','between',strtotime(date('Y-m-d')) . ',' . time())->where('status',1)->sum('num');//获取下级返佣数额
        $this->deal_count = Db::name('xy_users')->where('id',session('user_id'))->value('deal_count');

        //分类
        $type = input('get.type/d',1);
        //$this->cate = db('xy_goods_cate')->order('addtime asc')->select();
        $this->cate = Db::name('xy_goods_cate')->find($type);;

        //var_dump($this->cate);die;

        return $this->fetch();
    }
  /**
    *提交抢单
    */
    public function submit_order()
    {
        $tmp = $this->check_deal();
        if($tmp) return json($tmp);
        $res = check_time(9,22);
        //if($res) return json(['code'=>1,'info'=>'禁止在9:00~22:00以外的时间段执行当前操作!']);
        $uid = session('user_id');
        $add_id = db('xy_member_address')->where('uid',$uid)->where('is_default',1)->value('id');//获取收款地址信息
        if(!$add_id) return json(['code'=>2,'info'=>'还没有设置收货地址']);
        //检查交易状态
        // $sleep = mt_rand(config('min_time'),config('max_time'));
        $res = db('xy_users')->where('id',$uid)->update(['deal_status'=>2]);//将账户状态改为等待交易
        if($res === false) return json(['code'=>1,'info'=>'抢单失败，请稍后再试！']);
        // session_write_close();//解决sleep造成的进程阻塞问题
        // sleep($sleep);
        //
        $cid = input('post.cid/d',1);
        $count = db('xy_goods_list')->where('cid','=',$cid)->count();
        //dump($cid);exit();

        if($count < 1){
			return json(['code'=>1,'info'=>'抢单失败，商品库存不足1！']);
		}


        $res = model('admin/Convey')->create_order($uid,$cid);
        return json($res);
    }

    /**
     * 停止抢单
     */
    public function stop_submit_order()
    {
        $uid = session('user_id');
        $res = db('xy_users')->where('id',$uid)->where('deal_status',2)->update(['deal_status'=>1]);
        if($res){
            return json(['code'=>0,'info'=>'操作成功!']);
        }else{
            return json(['code'=>1,'info'=>'操作失败!']);
        }
    }

}