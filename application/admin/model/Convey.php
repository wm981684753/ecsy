<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Convey extends Model
{

    protected $table = 'xy_convey';

    /**
     * 创建订单
     *
     * @param int $uid
     * @return array
     */
    public function create_order($uid,$cid)
    {
        $add_id = Db::name('xy_member_address')->where('uid',$uid)->where('is_default',1)->value('id');//获取收款地址信息s
        if(!$add_id) return ['code'=>1,'info'=>'还没有设置收货地址'];
        $uinfo = Db::name('xy_users')->field('deal_status,balance')->find($uid);
        if($uinfo['deal_status']!=2) return ['code'=>1,'info'=>'抢单已终止'];
        $min = $uinfo['balance']*config('deal_min_num')/100;
        $max = $uinfo['balance']*config('deal_max_num')/100;

        $goods = $this->rand_order($min,$max,$cid);

        $id = getSn('UB');
        Db::startTrans();
        $res = Db::name('xy_users')->where('id',$uid)->update(['deal_status'=>3,'deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>Db::raw('deal_count+1')]);//将账户状态改为交易中
        //通过商品id查找 佣金比例
        $cate = Db::name('xy_goods_cate')->find($goods['cid']);;
       if($goods['num'] > $uinfo['balance']) return ['code'=>1,'info'=>'可用余额不足!'];

        //var_dump($cate,123,$goods);die;

        $res1 = Db::name($this->table)
                ->insert([
                    'id'            => $id,
                    'uid'           => $uid,
                    'num'           => $goods['num'],
                    'addtime'       => time(),
                    'endtime'       => time()+config('deal_timeout'),
                    'add_id'        => $add_id,
                    'goods_id'      => $goods['id'],
                    'goods_count'   => $goods['count'],
                    //'commission'    => $goods['num']*config('vip_1_commission'),
                    'commission'    => $goods['num']*$cate['bili'],
                ]);
        if($res && $res1){
            Db::commit();
            return ['code'=>0,'info'=>'抢单成功!','oid'=>$id];
        }else{
            Db::rollback();
            return ['code'=>1,'info'=>'抢单失败!请稍后再试'];
        }
    }

    /**
     * 随机生成订单
     */
    private function rand_order($min,$max,$cid)
    {	
        $num = mt_rand($min,$max);//随机交易额
		$a=$num/10;
        $goods = Db::name('xy_goods_list')
                ->orderRaw('rand()')
                //->where('goods_price','between',[$a,$num])
                ->where('cid','=',$cid)
                ->find();
		
        if (!$goods) {
            echo json_encode(['code'=>1,'info'=>'抢单失败, 该分类库存不足!']);die;
        }

        $count = round($num/$goods['goods_price']);//
        if($count*$goods['goods_price']<$min||$count*$goods['goods_price']>$max){
            self::rand_order($min,$max,$cid);
        }
        return ['count'=>$count,'id'=>$goods['id'],'num'=>$count*$goods['goods_price'],'cid'=>$goods['cid']];
    }

    /**
     * 处理订单
     *
     * @param string $oid      订单号
     * @param int    $status   操作      1会员确认付款 2会员取消订单 3后台强制付款 4后台强制取消
     * @param int    $uid      用户ID    传参则进行用户判断
     * @param int    $uid      收货地址
     * @return array
     */
    public function do_order($oid,$status,$uid='',$add_id='')
    {
		//file_put_contents('01.txt',11111);
        $info = Db::name('xy_convey')->find($oid);
        if(!$info) return ['code'=>1,'info'=>'订单号不存在'];
        if($uid && $info['uid']!=$uid) return ['code'=>1,'info'=>'参数错误，请确认订单号!'];
        if(!in_array($info['status'],[0,5])) return ['code'=>1,'info'=>'该订单已处理！请刷新页面'];

        //TODO 判断余额是否足够
        $userPrice = Db::name('xy_users')->where('id',$info['uid'])->value('balance');
        if ($userPrice < $info['num']) return ['code'=>1,'info'=>'账户可用余额不足!'];

        //$tmp = ['endtime'=>time(),'status'=>$status];
		//var_dump(config('deal_feedze'));die;
        $tmp = ['endtime'=>time()+config('order_freeze'),'status'=>5];
        $add_id?$tmp['add_id']=$add_id:'';
        Db::startTrans();
        $res = Db::name('xy_convey')->where('id',$oid)->update($tmp);
        if(in_array($status,[1,3])){
            //确认付款
            $res1 = Db::name('xy_users')
                        ->where('id', $info['uid'])
                        ->dec('balance',$info['num'])
                        ->inc('freeze_balance',$info['num']+$info['commission']) //冻结商品金额 + 佣金
                        ->update(['deal_status' => 1,'status'=>1]);
           
            $res2 = Db::name('xy_balance_log')->insert([
                'uid'           => $info['uid'],
                'oid'           => $oid,
                'num'           => $info['num'],
                'type'          => 2,
                'status'        => 2,
                'addtime'       => time()
            ]);
			Db::name('xy_convey')->where('id',$oid)->update(['status'=>1]);
            if($status==3) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>'系统通知','content'=>'交易订单'.$oid.'已被系统强制付款，如有疑问请联系客服','addtime'=>time()]);
            //系统通知
            if($res && $res1 && $res2){
                Db::commit();
				//var_dump(22222);
                $c_status = Db::name('xy_convey')->where('id',$oid)->value('c_status');
				//var_dump(config('1_d_reward'));die;
                //判断是否已返还佣金
				//var_dump($info['commission']);die;
                if($c_status===0) $this->deal_reward($info['uid'],$oid,$info['num'],$info['commission']);
				
                return ['code'=>0,'info'=>'操作成功!'];
            }else {
                Db::rollback();
                return ['code'=>1,'info'=>'操作失败'];
            }
        }elseif (in_array($status,[2,4])) {
            $res1 = Db::name('xy_users')->where('id',$info['uid'])->update(['deal_status'=>1]);
            if($status==4) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>'系统通知','content'=>'交易订单'.$oid.'已被系统强制取消，如有疑问请联系客服','addtime'=>time()]);
            //系统通知
            if($res && $res1!==false){
                Db::commit();
                return ['code'=>0,'info'=>'操作成功!'];
            }else {
                Db::rollback();
                return ['code'=>1,'info'=>'操作失败','data'=>$res1];
            }
        }
    }

    /**
     * 交易返佣
     *
     * @return void
     */
    public function deal_reward($uid,$oid,$num,$cnum)
    {
        $res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
		$res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setDec('freeze_balance',$num+$cnum);
		//var_dump($res);
        if($res){
                $list = Db::name('xy_balance_log')->insertGetId([
                    //记录返佣信息
                    'uid'       => $uid,
                    'oid'       => $oid,
                    'num'       => $num+$cnum,
                    'type'      => 3,
                    'addtime'   => time()
                ]);
				//var_dump($list);die;
                //将订单状态改为已返回佣金
                Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>1]);
                Db::name('xy_reward_log')->insert(['oid'=>$oid,'uid'=>$uid,'num'=>$num,'addtime'=>time(),'type'=>2]);//记录充值返佣订单
				//var_dump($id);die;
                 /************* 发放交易奖励 *********/
                    $userList = model('admin/Users')->parent_user($uid,5);
                    if($userList){
                        foreach($userList as $v){
                            if($v['status']===1){
                                Db::name('xy_reward_log')
                                ->insert([
                                    'uid'       => $v['id'],
                                    'sid'       => $uid,
                                    'oid'       => $oid,
                                    'num'       => $cnum*config($v['lv'].'_d_reward'),
                                    'lv'        => $v['lv'],
                                    'type'      => 2,
                                    'status'    => 2,
                                    'addtime'   => time(),
                                ]);
								Db::name('xy_users')->where(['id'=>$v['id'],'status'=>1])->setInc('balance',($cnum*config($v['lv'].'_d_reward')));
                            }
                        }
                    }
                 /************* 发放交易奖励 *********/
        }else{
            $res1 = Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>2]);//记录账号异常
        }
    }
}