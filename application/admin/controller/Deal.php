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

namespace app\admin\controller;

use app\admin\service\NodeService;
use library\Controller;
use library\tools\Data;
use think\Db;

/**
 * 交易中心
 * Class Users
 * @package app\admin\controller
 */
class Deal extends Controller
{

    /**
     * 订单列表
     *@auth true
     *@menu true
     */
    public function order_list()
    {
        $this->title = '订单列表';
        $where = [];
        if(input('oid/s','')) $where[] = ['xc.id','like','%'.input('oid','').'%'];
        if(input('username/s','')) $where[] = ['u.username','like','%' . input('username/s','') . '%'];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['fc.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }
        $this->_query('xy_convey')
            ->alias('xc')
            ->leftJoin('xy_users u','u.id=xc.uid')
            ->leftJoin('xy_goods_list g','g.id=xc.goods_id')
            ->field('xc.*,u.username,g.goods_name,g.goods_price')
            ->where($where)
            ->order('addtime desc')
            ->page();
    }

    /**
     * 处理用户交易订单
     */
    public function do_user_order()
    {
        $this->applyCsrfToken();
        $oid = input('post.id/s','');
        $status = input('post.status/d',1);
        if(!\in_array($status,[3,4])) return $this->error('参数错误');
        $res = model('Convey')->do_order($oid,$status);
        if($res['code']===0)
            return $this->success('操作成功');
        else
            return $this->error($res['info']);
    }

    /**
     * 交易控制
     * @auth true
     * @menu true
     */
    public function deal_console()
    {
        $this->title = '交易控制';
        if(request()->isPost()){
            $deal_min_balance = input('post.deal_min_balance/d',0);
			$deal_freeze		=input('post.deal_freeze/d',0);
            $deal_timeout     = input('post.deal_timeout/d',0);
            $deal_min_num     = input('post.deal_min_num/d',0);   
            $deal_max_num     = input('post.deal_max_num/d',0);
            $deal_count       = input('post.deal_count/d',0);
            $deal_reward_count= input('post.deal_reward_count/d',0);
            $deal_feedze      = input('post.deal_feedze/d',0);
            $deal_error       = input('post.deal_error/d',0);
            $deal_commission  = input('post.deal_commission/f',0);
            $_1reward  = input('post.1_reward/f',0);
            $_2reward  = input('post.2_reward/f',0);
            $_3reward  = input('post.3_reward/f',0);
            $_1_d_reward  = input('post.1_d_reward/f',0);
            $_2_d_reward  = input('post.2_d_reward/f',0);
            $_3_d_reward  = input('post.3_d_reward/f',0);
            $_4_d_reward  = input('post.4_d_reward/f',0);
            $_5_d_reward  = input('post.5_d_reward/f',0);
			$order_freeze= input('post.order_freeze/f',0);

            //可以加上限制条件
            if($deal_commission>1||$deal_commission<0) return $this->error('参数错误'); 
            setconfig(['deal_min_balance'],[$deal_min_balance]);
            setconfig(['deal_timeout'],[$deal_timeout]);
			setconfig(['order_freeze'],[$order_freeze]);
            setconfig(['deal_min_num'],[$deal_min_num]);
            setconfig(['deal_max_num'],[$deal_max_num]);
            setconfig(['deal_reward_count'],[$deal_reward_count]);
            setconfig(['deal_count'],[$deal_count]);
            setconfig(['deal_feedze'],[$deal_feedze]);
            setconfig(['deal_error'],[$deal_error]);
            setconfig(['deal_commission'],[$deal_commission]);
            setconfig(['1_reward'],[$_1reward]);
            setconfig(['2_reward'],[$_2reward]);
            setconfig(['3_reward'],[$_3reward]);
            setconfig(['1_d_reward'],[$_1_d_reward]);
            setconfig(['2_d_reward'],[$_2_d_reward]);
            setconfig(['3_d_reward'],[$_3_d_reward]);
            setconfig(['4_d_reward'],[$_4_d_reward]);
            setconfig(['5_d_reward'],[$_5_d_reward]);
            setconfig(['vip_1_commission'],[input('post.vip_1_commission/f')]);
            setconfig(['vip_2_commission'],[input('post.vip_2_commission/f')]);
            setconfig(['vip_2_num'],[input('post.vip_2_num/f')]);
            setconfig(['vip_3_commission'],[input('post.vip_3_commission/f')]);
            setconfig(['vip_3_num'],[input('post.vip_3_num/f')]);
            setconfig(['master_cardnum'],[input('post.master_cardnum')]);
            setconfig(['master_name'],[input('post.master_name')]);
            setconfig(['master_bank'],[input('post.master_bank')]);
            setconfig(['master_bk_address'],[input('post.master_bk_address')]);
            setconfig(['deal_zhuji_time'],[input('post.deal_zhuji_time')]);
            setconfig(['deal_shop_time'],[input('post.deal_shop_time')]);

            return $this->success('操作成功!');
        }
        return $this->fetch();
    }

    /**
     * 商品管理
     *@auth true
     *@menu true
     */
    public function goods_list()
    {
        $this->title = '商品管理';

        $this->cate = db('xy_goods_cate')->order('addtime asc')->select();
        $where = [];
        //var_dump($this->cate);die;
        $query = $this->_query('xy_goods_list');
        if(input('title/s',''))$where[] = ['goods_name','like','%' . input('title/s','') . '%'];
        if(input('cid/d',''))$where[] = ['cid','=',input('cid/d','')];

        //var_dump($where);die;
        $query->where($where)->page();;


    }


    /**
     * 商品管理
     *@auth true
     *@menu true
     */
    public function goods_cate()
    {
        $this->title = '分类管理';
        $this->_query('xy_goods_cate')->page();
    }

    /**
     * 添加商品
     *@auth true
     *@menu true
     */
    public function add_goods()
    {
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $shop_name      = input('post.shop_name/s','');
            $goods_name     = input('post.goods_name/s','');
            $goods_price    = input('post.goods_price/f',0);
            $goods_pic      = input('post.goods_pic/s','');
            $goods_info     = input('post.goods_info/s','');
            $cid     = input('post.cid/d',1);
            $res = model('GoodsList')->submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$goods_info,$cid);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/deal/goods_list.html');
            else 
                return $this->error($res['info']);
        }
        $this->cate = db('xy_goods_cate')->order('addtime asc')->select();
        return $this->fetch();
    }


    /**
     * 添加商品
     *@auth true
     *@menu true
     */
    public function add_cate()
    {
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $name      = input('post.name/s','');
            $bili     = input('post.bili/s','');
            $info    = input('post.cate_info/s','');

            $res = $this->submit_cate($name,$bili,$info,0);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/deal/goods_cate.html');
            else
                return $this->error($res['info']);
        }
        return $this->fetch();
    }


    /**
     * 添加商品
     *
     * @param string $shop_name
     * @param string $goods_name
     * @param string $goods_price
     * @param string $goods_pic
     * @param string $goods_info
     * @param string $id 传参则更新数据,不传则写入数据
     * @return array
     */
    public function submit_cate($name,$bili,$info,$id)
    {
        if(!$name) return ['code'=>1,'info'=>('请输入分类名称')];
        if(!$bili) return ['code'=>1,'info'=>('请输入比例')];

        $data = [
            'name'     => $name,
            'bili'    => $bili,
            'cate_info'   => $info,
            'addtime'       => time()
        ];
        if(!$id){
            $res = Db::table('xy_goods_cate')->insert($data);
        }else{
            $res = Db::table('xy_goods_cate')->where('id',$id)->update($data);
        }
        if($res)
            return ['code'=>0,'info'=>'操作成功!'];
        else
            return ['code'=>1,'info'=>'操作失败!'];
    }

    /**
     * 编辑商品信息
     * @auth true
     * @menu true
     */
    public function edit_goods($id)
    {
        $id = (int)$id;
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $shop_name      = input('post.shop_name/s','');
            $goods_name     = input('post.goods_name/s','');
            $goods_price    = input('post.goods_price/f',0);
            $goods_pic      = input('post.goods_pic/s','');
            $goods_info     = input('post.goods_info/s','');
            $id             = input('post.id/d',0);
            $cid             = input('post.cid/d',0);
            $res = model('GoodsList')->submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$goods_info,$cid,$id);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/deal/goods_list.html');
            else 
                return $this->error($res['info']);
        }
        $info = db('xy_goods_list')->find($id);
        $this->cate = db('xy_goods_cate')->order('addtime asc')->select();
        $this->assign('cate',$this->cate);
        $this->assign('info',$info);
        return $this->fetch();
    }
 /**
     * 编辑商品信息
     * @auth true
     * @menu true
     */
    public function edit_cate($id)
    {
        $id = (int)$id;
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $name      = input('post.name/s','');
            $bili     = input('post.bili/s','');
            $info    = input('post.cate_info/s','');

            $res = $this->submit_cate($name,$bili,$info,$id);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/deal/goods_cate.html');
            else
                return $this->error($res['info']);
        }
        $info = db('xy_goods_cate')->find($id);
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 更改商品状态
     * @auth true
     */
    public function edit_goods_status()
    {
        $this->applyCsrfToken();
        $this->_form('xy_goods_list', 'form');
    }

    /**
     * 删除商品
     * @auth true
     */
    public function del_goods()
    {
        $this->applyCsrfToken();
        $this->_delete('xy_goods_list');
    }
    /**
     * 删除商品
     * @auth true
     */
    public function del_cate()
    {
        $this->applyCsrfToken();
        $this->_delete('xy_goods_cate');
    }

    /**
     * 充值管理
     * @auth true
     * @menu true
     */
    public function user_recharge()
    {
        $this->title = '充值管理';
        $query = $this->_query('xy_recharge')->alias('xr');
        $where = [];
        if(input('oid/s','')) $where[] = ['xr.id','like','%'.input('oid','').'%'];
        if(input('tel/s','')) $where[] = ['xr.tel','=',input('tel/s','')];
        if(input('username/s','')) $where[] = ['u.username','like','%' . input('username/s','') . '%'];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['fc.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }
        $query->leftJoin('xy_users u','u.id=xr.uid')
            ->field('xr.*,u.username')
            ->where($where)
            ->order('addtime desc')
            ->page();
    }

    /**
     * 审核充值订单
     * @auth true
     */
    public function edit_recharge()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $oid = input('post.id/s','');
            $status = input('post.status/d',1);
            $oinfo = Db::name('xy_recharge')->find($oid);
            Db::startTrans();
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>$status]);
            if($status==2){
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
                //发放注册奖励
            }elseif($status==3){
                $res1 = Db::name('xy_message')
                        ->insert([
                            'uid'=>$oinfo['uid'],
                            'type'=>2,
                            'title'=>'系统通知',
                            'content'=>'充值订单'.$oid.'已被退回，如有疑问请联系客服',
                            'addtime'=>time()
                        ]);
            }
            if($res && $res1){
                Db::commit();
                
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

                $this->success('操作成功!');
            }else{
                Db::rollback();
                $this->error('操作失败!');
            }
        }
    }

    /**
     * 提现管理
     * @auth true
     * @menu true
     */
    public function deposit_list()
    {
        $this->title = '提现列表';
        $this->_query('xy_deposit')
            ->alias('xd')
            ->leftJoin('xy_users u','u.id=xd.uid')
            ->leftJoin('xy_bankinfo bk','bk.id=xd.bk_id')
            ->field('xd.*,u.username,bk.bankname,bk.username as khname,bk.tel,bk.cardnum,u.id uid')
            ->order('addtime desc,endtime desc')
            ->page();
    }

    /**
     * 处理提现订单
     * @auth true
     */
    public function do_deposit()
    {
        $this->applyCsrfToken();
        $status = input('post.status/d',1);
        if($status==3){
            //驳回订单的业务逻辑
            Db::name('xy_users')->where('id',input('post.uid',0))->setInc('balance',input('num/f',0));
        }
        $this->_save('xy_deposit', ['status' =>$status,'endtime'=>time()]);
    }

    /**
     * 一键返佣
     * @auth true
     */
    public function do_commission()
    {
        $this->applyCsrfToken();
        $info = Db::name('xy_convey')
                ->field('id oid,uid,num,commission cnum')
                ->where([
                    ['c_status','in',[0,2]],
                    ['status','in',[1,3]],
                    //['endtime','between','??']    //时间限制
                ])
                ->select();
        if(!$info) return $this->error('当前没有待返佣订单!');
        try {
            foreach ($info as $k => $v) {
                Db::startTrans();
                $res = Db::name('xy_users')->where('id',$v['uid'])->where('status',1)->setInc('balance',$v['num']+$v['cnum']);
                if($res){
                    $res1 = Db::name('xy_balance_log')->insert([
                        //记录返佣信息
                        'uid'       => $v['uid'],
                        'oid'       => $v['oid'],
                        'num'       => $v['num']+$v['cnum'],
                        'type'      => 3,
                        'addtime'   => time()
                    ]);
                    Db::name('xy_convey')->where('id',$v['oid'])->update(['c_status'=>1]);
                }else{
                    // Db::name('xy_system_log')->insert();
                    $res1 = Db::name('xy_convey')->where('id',$v['oid'])->update(['c_status'=>2]);//记录账号异常
                }
                if($res!==false && $res1)
                    Db::commit();
                else
                    Db::rollback();
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        return $this->success('操作成功!');
    }

    /**
     * 交易流水
     * @auth true
     * @menu true
     */
    public function order_log()
    {
        $this->title = '交易流水';
        $this->_query('xy_balance_log')->page();
    }

    /**
     * 团队返佣
     * @auth true
     * @menu true
     */
    public function team_reward()
    {
        
    }
}