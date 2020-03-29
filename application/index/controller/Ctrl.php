<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class Ctrl extends Base
{
      //钱包页面
    public function wallet()
    {
        $balance = db('xy_users')->where('id',session('user_id'))->value('balance');
        $this->assign('balance',$balance);
        $balanceT = db('xy_convey')->where('uid',session('user_id'))->where('status',2)->sum('commission');
        $this->assign('balance_shouru',$balanceT);

        //收益
        $startDay = strtotime( date('Y-m-d 00:00:00', time()) );
        $shouyi = db('xy_convey')->where('uid',session('user_id'))->where('addtime','>',$startDay)->where('status',1)->select();

        //充值
        $chongzhi = db('xy_recharge')->where('uid',session('user_id'))->where('addtime','>',$startDay)->where('status',2)->select();

        //提现
        $tixian = db('xy_deposit')->where('uid',session('user_id'))->where('addtime','>',$startDay)->where('status',1)->select();

        $this->assign('shouyi',$shouyi);
        $this->assign('chongzhi',$chongzhi);
        $this->assign('tixian',$tixian);
        return $this->fetch();
    }


    public function recharge_before()
    {
        $pay = db('xy_pay')->where('status',1)->select();

        $this->assign('pay',$pay);
        return $this->fetch();
    }



    public function recharge2()
    {

        $type = isset($_REQUEST['type']) ? $_REQUEST['type']: 'wx';
        $pay = db('xy_pay')->where('status',1)->select();

        $title = $type=='wx' ? '微信支付': '支付宝支付';
        if ($type== 'bipay') {
            $title = '比特币支付';
        }
        $this->assign('title',$title);
        $this->assign('pay',$pay);
        $this->assign('type',$type);
        return $this->fetch();
    }

    //三方支付
    public function recharge3()
    {

        $type = isset($_REQUEST['type']) ? $_REQUEST['type']: 'wx';
        $pay = db('xy_pay')->where('status',1)->select();
        $this->assign('title',$type=='wx' ? '微信支付': '支付宝支付');
        $this->assign('pay',$pay);
        $this->assign('type',$type);
        return $this->fetch();
    }


    //钱包页面
    public function bank()
    {
        $balance = db('xy_users')->where('id', session('user_id'))->value('balance');
        $this->assign('balance', $balance);
        $balanceT = db('xy_convey')->where('uid', session('user_id'))->where('status', 2)->sum('commission');
        $this->assign('balance_shouru', $balanceT);
        return $this->fetch();
    }

    //获取提现订单接口
    public function get_deposit()
    {
        $info = db('xy_deposit')->where('uid',session('user_id'))->select();
        if($info) return json(['code'=>0,'info'=>'请求成功','data'=>$info]);
        return json(['code'=>1,'info'=>'暂无数据']);
    }



    public function recharge_do()
    {
        if(request()->isPost()){
            $num = input('post.price/d',0);
            $type = input('post.type/s','');

            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username')->find($uid);
            if(!$num ) return json(['code'=>1,'info'=>'参数错误']);

            //
            $pay = db('xy_pay')->where('name2',$type)->find();
            if ($num < $pay['min']) return json(['code'=>1,'info'=>'充值不能小于'.$pay['min']]);
            if ($num > $pay['max']) return json(['code'=>1,'info'=>'充值不能大于'.$pay['max']]);

            $id = getSn('SY');
            $res = db('xy_recharge')
                ->insert([
                    'id'        => $id,
                    'uid'       => $uid,
                    'tel'       => $uinfo['tel'],
                    'real_name' => $uinfo['username'],
                    'pic'       => '',
                    'num'       => $num,
                    'addtime'   => time(),
                    'pay_name'  => $type
                ]);
            if($res){
                $pay['id'] = $id;
                $pay['num'] =$num;
                if ($pay['name2'] == 'bipay' ) {
                    $pay['redirect'] = url('/index/Api/bipay').'?oid='.$id;
                }
                return json(['code'=>0,'info'=>$pay]);
            }

            else
                return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
        }
        return json(['code'=>0,'info'=>'请求成功!','data'=>[]]);
    }

    //提现接口
    public function do_deposit()
    {
        $res = check_time(9,17);
        //if($res) return json(['code'=>1,'info'=>'禁止在9:00~17:00以外的时间段执行当前操作!']);
        $bankinfo = Db::name('xy_bankinfo')->where('uid',session('user_id'))->where('status',1)->find();
        //var_dump($bankinfo);die;

        if(!$bankinfo) return json(['code'=>1,'info'=>'还没添加银行卡信息!']);
        if(request()->isPost()){
            $uid = session('user_id');

            //$filename = date('Ymd').$uid.'_TX.lock';


            $num = input('post.num/d',0);
            $bkid = input('post.bk_id/d',0);
            $token = input('post.token','');
            $data = ['__token__' => $token];
            $validate   = \Validate::make($this->rule,$this->msg);
            if(!$validate->check($data)) return json(['code'=>1,'info'=>$validate->getError()]);
            if($num<config('min_deposit')) return json(['code'=>1,'info'=>'最低提现额度为'.config('min_deposit')]);
            $uinfo = Db::name('xy_users')->field('recharge_num,deal_time,balance')->find($uid);//获取用户今日已充值金额
            if ($num > $uinfo['balance']) return json(['code'=>1,'info'=>'余额不足']);


            if($uinfo['deal_time']==strtotime(date('Y-m-d'))){
                if($num > 20000-$uinfo['recharge_num']) return ['code'=>1,'info'=>'今日剩余提现额度为'.( 20000 - $uinfo['recharge_num'])];
            }else{
                //重置最后交易时间
                Db::name('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>0,'recharge_num'=>0,'deposit_num'=>0]);
            }
            $id = getSn('CO');
            try {
                Db::startTrans();
                $res = Db::name('xy_deposit')->insert([
                    'id'          => $id,
                    'uid'         => $uid,
                    'bk_id'       => $bkid,
                    'num'         => $num,
                    'addtime'     => time()
                ]);
                $res1 = Db::name('xy_users')->where('id',session('user_id'))->setDec('balance',$num);
                if($res && $res1){
                    Db::commit();
                    return json(['code'=>0,'info'=>'操作成功!']);
                }else{
                    Db::rollback();
                    return json(['code'=>1,'info'=>'操作失败!']);
                }
            } catch (\Exception $e){
                Db::rollback();
                return json(['code'=>1,'info'=>'操作失败!请检查账号余额']);
            }
        }
        return json(['code'=>0,'info'=>'请求成功!','data'=>$bankinfo]);
    }

    //////get请求获取参数，post请求写入数据，post请求传人bkid则更新数据//////////
    public function do_bankinfo()
    {
        if(request()->isPost()){
            $token = input('post.token','');
            $data = ['__token__' => $token];
            $validate   = \Validate::make($this->rule,$this->msg);
            if(!$validate->check($data)) return json(['code'=>1,'info'=>$validate->getError()]);

            $username = input('post.username/s','');
            $bankname = input('post.bankname/s','');
            $cardnum = input('post.cardnum/s','');
            $site = input('post.site/s','');
            $tel = input('post.tel/s','');
            $status = input('post.default/d',0);
            $bkid = input('post.bkid/d',0); //是否为更新数据

            if(!$username)return json(['code'=>1,'info'=>'开户人名称为必填项']);
            if(mb_strlen($username) > 30)return json(['code'=>1,'info'=>'开户人名称长度最大为30个字符']);
            if(!$bankname)return json(['code'=>1,'info'=>'银行名称为必填项']);
            if(!$cardnum)return json(['code'=>1,'info'=>'银行卡号为必填项']);
            if(!$tel)return json(['code'=>1,'info'=>'手机号为必填项']);

            if($bkid)
                $cardn = Db::table('xy_bankinfo')->where('id','<>',$bkid)->where('cardnum',$cardnum)->count();
            else
                $cardn = Db::table('xy_bankinfo')->where('cardnum',$cardnum)->count();
            
            if($cardn)return json(['code'=>1,'info'=>'银行卡号已存在']);

            $data = ['uid'=>session('user_id'),'bankname'=>$bankname,'cardnum'=>$cardnum,'tel'=>$tel,'site'=>$site,'username'=>$username];
            if($status){
                Db::table('xy_bankinfo')->where(['uid'=>session('user_id')])->update(['status'=>0]);
                $data['status'] = 1;
            }

            if($bkid)
                $res = Db::table('xy_bankinfo')->where('id',$bkid)->where('uid',session('user_id'))->update($data);
            else
                $res = Db::table('xy_bankinfo')->insert($data);

            if($res!==false)
                return json(['code'=>0,'info'=>'操作成功']);
            else
                return json(['code'=>1,'info'=>'操作失败']);
        }
        $bkid = input('id/d',0); //是否为更新数据
        $where = ['uid'=>session('user_id')];
        if($bkid!==0) $where['id'] = $bkid;
        $info = db('xy_bankinfo')->where($where)->select();
        if(!$info) return json(['code'=>1,'info'=>'暂无数据']);
        return json(['code'=>0,'info'=>'请求成功','data'=>$info]);
    }

    //切换银行卡状态
    public function edit_bankinfo_status()
    {
        $id = input('post.id/d',0);

        Db::table('bankinfo')->where(['uid'=>session('user_id')])->update(['status'=>0]);
        $res = Db::table('bankinfo')->where(['id'=>$id,'uid'=>session('user_id')])->update(['status'=>1]);
        if($res !== false)
            return json(['code'=>0,'info'=>'操作成功!']); 
        else
            return json(['code'=>1,'info'=>'操作失败！']); 
    }

    //获取下级会员
    public function bot_user()
    {
        if(request()->isPost()){
            $uid = input('post.id/d',0);
            $token = ['__token__' => input('post.token','')];
            $validate   = \Validate::make($this->rule,$this->msg);
            if(!$validate->check($token)) return json(['code'=>1,'info'=>$validate->getError()]);
        }else{
            $uid = session('user_id');
        }
        $page = input('page/d',1);
        $num = input('num/d',10);
        $limit = ( (($page - 1) * $num) . ',' . $num );
        $data = db('xy_users')->where('parent_id',$uid)->field('id,username,headpic,addtime,childs,tel')->limit($limit)->order('addtime desc')->select();
        if(!$data) return json(['code'=>1,'info'=>'暂无数据']);
        return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
    }
    
    //修改密码
    public function set_pwd()
    {
        if(!request()->isPost()) return json(['code'=>1,'info'=>'错误请求']);
        $o_pwd = input('old_pwd/s','');
        $pwd = input('new_pwd/s','');
        $type = input('type/d',1);
        $uinfo = db('xy_users')->field('pwd,salt,tel')->find(session('user_id'));
        if($uinfo['pwd']!=sha1($o_pwd.$uinfo['salt'].config('pwd_str'))) return json(['code'=>1,'info'=>'密码错误!']);
        $res = model('admin/Users')->reset_pwd($uinfo['tel'],$pwd,$type);
        return json($res);
    }



    //我的下级
    public function get_user()
    {

        $uid = session('user_id');

        $type = input('post.type/d',1);

        $page = input('page/d',1);
        $num = input('num/d',10);
        $limit = ( (($page - 1) * $num) . ',' . $num );
        $uinfo = db('xy_users')->field('*')->find(session('user_id'));
        $other = [];
        if ($type == 1) {
            $uid = session('user_id');
            $data = db('xy_users')->where('parent_id', $uid)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $cond=implode(',',$ids1);
            $cond = !empty($cond) ? $cond = " uid in ($cond)":' parent_id=-1';
            $other = [];
            $other['chongzhi'] = db('xy_recharge')->where($cond)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($cond)->where('status', 2)->sum('num');
            $other['xiaji'] = count($ids1);

            //var_dump($uinfo);die;

            $iskou =0;
            foreach ($data as &$datum) {
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png':'';
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['kouchu_balance_uid'] == $datum['id']) {
                    $datum['chongzhi'] -= $uinfo['kouchu_balance'];
                    $iskou = 1;
                }

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }

            $other['chongzhi'] -= $uinfo['kouchu_balance'];
            return json(['code'=>0,'info'=>'请求成功','data'=>$data,'other'=>$other]);

        }else if($type == 2) {
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $cond=implode(',',$ids1);
            $cond = !empty($cond) ? $cond = " parent_id in ($cond)":' parent_id=-1';

            //获取二代ids
            $ids2 = db('xy_users')->where($cond)->field('id')->column('id');
            $cond2=implode(',',$ids2);
            $cond2 = !empty($cond2) ? $cond2 = " uid in ($cond2)":' parent_id=-1';
            $other = [];
            $other['chongzhi'] = db('xy_recharge')->where($cond2)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($cond2)->where('status', 2)->sum('num');
            $other['xiaji'] = count($ids2);



            $data = db('xy_users')->where($cond)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值

            foreach ($data as &$datum) {
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png':'';
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }

            return json(['code'=>0,'info'=>'请求成功','data'=>$data,'other'=>$other]);


        }else if($type == 3) {
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $cond=implode(',',$ids1);
            $cond = !empty($cond) ? $cond = " parent_id in ($cond)":' parent_id=-1';
            $ids2 = db('xy_users')->where($cond)->field('id')->column('id');

            $cond2=implode(',',$ids2);
            $cond2 = !empty($cond2) ? $cond2 = " parent_id in ($cond2)":' parent_id=-1';

            //获取三代的ids
            $ids22 = db('xy_users')->where($cond2)->field('id')->column('id');
            $cond22=implode(',',$ids22);
            $cond22 = !empty($cond22) ? $cond22 = " uid in ($cond22)":' uid=-1';
            $other = [];
            $other['chongzhi'] = db('xy_recharge')->where($cond22)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($cond22)->where('status', 2)->sum('num');
            $other['xiaji'] = count($ids22);

            //获取四代ids
            $cond4 =implode(',',$ids22);
            $cond4 = !empty($cond4) ? $cond4 = " parent_id in ($cond4)":' parent_id=-1';
            $ids4  = db('xy_users')->where($cond4)->field('id')->column('id'); //四代ids

            //充值
            $cond44 =implode(',',$ids4);
            $cond44 = !empty($cond44) ? $cond44 = " uid in ($cond44)":' uid=-1';
            $other['chongzhi4'] = db('xy_recharge')->where($cond44)->where('status', 2)->sum('num');
            $other['tixian4'] = db('xy_deposit')->where($cond44)->where('status', 2)->sum('num');
            $other['xiaji4'] = count($ids4);



            //获取五代
            $cond5 = implode(',',$ids4);
            $cond5 = !empty($cond5) ? $cond5 = " parent_id in ($cond5)":' parent_id=-1';
            $ids5  = db('xy_users')->where($cond5)->field('id')->column('id'); //五代ids

            //充值
            $cond55 =implode(',',$ids5);
            $cond55 = !empty($cond55) ? $cond55 = " uid in ($cond55)":' uid=-1';
            $other['chongzhi5'] = db('xy_recharge')->where($cond55)->where('status', 2)->sum('num');
            $other['tixian5'] = db('xy_deposit')->where($cond55)->where('status', 2)->sum('num');
            $other['xiaji5'] = count($ids5);

            $other['chongzhi_all'] = $other['chongzhi'] + $other['chongzhi4']+ $other['chongzhi5'];
            $other['tixian_all']   = $other['tixian'] + $other['tixian4']+ $other['tixian5'];

            $data = db('xy_users')->where($cond2)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值

            foreach ($data as &$datum) {
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png':'';
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }
            return json(['code'=>0,'info'=>'请求成功','data'=>$data,'other'=>$other]);
        }



        return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
    }

}