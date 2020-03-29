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
 * 会员管理
 * Class Users
 * @package app\admin\controller
 */
class Users extends Controller
{

    /**
     * 指定当前数据表
     * @var string
     */
    protected $table = 'xy_users';

    /**
     * 会员列表
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        $this->title = '会员列表';

        $query = $this->_query($this->table)->alias('u');
        $where = [];
        if(input('tel/s',''))$where[] = ['u.tel','like','%' . input('tel/s','') . '%'];
        if(input('username/s',''))$where[] = ['u.username','like','%' . input('username/s','') . '%'];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['u.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }
        $query->field('u.id,u.tel,u.username,u.id_status,u.addtime,u.invite_code,u.freeze_balance,u.status,u.balance,u1.username as parent_name')
            ->leftJoin('xy_users u1','u.parent_id=u1.id')
            ->where($where)
            ->order('u.id desc')
            ->page();
    }

    /**
     * 添加会员
     *@auth true
     *@menu true
     */
    public function add_users()
    {
        if(request()->isPost()){
            $tel = input('post.tel/s','');
            $user_name = input('post.user_name/s','');
            $pwd = input('post.pwd/s','');
            $parent_id= input('post.parent_id/d',0);
            $token = input('__token__',1);
            $res = model('Users')->add_users($tel,$user_name,$pwd,$parent_id,$token);
            if($res['code']!==0){
                return $this->error($res['info']);
            }
            return $this->success($res['info']);
        }
        return $this->fetch();
    }

    /**
     * 编辑会员
     * @auth true
     * @menu true
     */
    public function edit_users()
    {
        $id = input('get.id',0);
        if(request()->isPost()){
            $id = input('post.id/d',0);
            $level_id = input('post.level/d',1);
            $tel = input('post.tel/s','');
            $user_name = input('post.user_name/s','');
            $pwd = input('post.pwd/s','');
            $parent_id = input('post.parent_id/d',0);
            $balance = input('post.balance/f',0);
            $freeze_balance = input('post.freeze_balance/f',0);
            $token = input('__token__');
            if($balance<0) $this->error('余额不能低于0');
            $res = model('Users')->edit_users($id,$tel,$user_name,$pwd,$parent_id,$balance,$freeze_balance,$token,$level_id);
            if($res['code']!==0){
                return $this->error($res['info']);
            }
            return $this->success($res['info']);
        }
        if(!$id) $this->error('参数错误');
        $this->info = Db::table($this->table)->find($id);
		$this->level = db('xy_user_level')->order('id desc')->select();
        return $this->fetch();
    }


    public function delete_user()
    {
        $this->applyCsrfToken();
        $id = input('post.id/d',0);
        $res = Db::table('xy_users')->where('id',$id)->delete();
        if($res)
            $this->success('删除成功!');
        else
            $this->error('删除失败!');
    }

    /**
     * 编辑会员
     * @auth true
     * @menu true
     */
    public function edit_users_ankou()
    {
        $id = input('get.id',0);
        if(request()->isPost()){
            $id = input('post.id/d',0);
//            $show_td = input('post.show_td/d',0);  //显示我的团队
//            $show_cz = input('post.show_cz/d',0);  //显示充值
//            $show_tx = input('post.show_tx/d',0);  //显示提现
//            $show_num = input('post.show_num/d',0);  //显示推荐人数
//            $show_tel = input('post.show_tel/d',0);  //显示电话
//            $show_tel2 = input('post.show_tel2/d',0);  //显示电话隐藏
            $kouchu_balance_uid = input('post.kouchu_balance_uid/d',0); //扣除人
            $kouchu_balance =  input('post.kouchu_balance/f',0); //扣除金额


            $show_td = ( isset($_REQUEST['show_td']) && $_REQUEST['show_td'] == 'on' ) ?  1 : 0;//显示我的团队
            $show_cz = ( isset($_REQUEST['show_cz']) && $_REQUEST['show_cz'] == 'on' ) ?  1 : 0;//显示充值
            $show_tx = ( isset($_REQUEST['show_tx']) && $_REQUEST['show_tx'] == 'on' ) ?  1 : 0;//显示提现
            $show_num = ( isset($_REQUEST['show_num']) && $_REQUEST['show_num'] == 'on' ) ?  1 : 0;//显示推荐人数
            $show_tel = ( isset($_REQUEST['show_tel']) && $_REQUEST['show_tel'] == 'on' ) ?  1 : 0;//显示电话
            $show_tel2 = ( isset($_REQUEST['show_tel2']) && $_REQUEST['show_tel2'] == 'on' ) ?  1 : 0;//显示电话隐藏


            $token = input('__token__');
            $data = [
                '__token__'         => $token,
                'show_td'               => $show_td,
                'show_cz'               => $show_cz,
                'show_tx'               => $show_tx,
                'show_num'               => $show_num,
                'show_tel'               => $show_tel,
                'show_tel2'               => $show_tel2,
                'kouchu_balance_uid'           => $kouchu_balance_uid,
                'kouchu_balance'               => $kouchu_balance,
            ];

            //var_dump($data,$_REQUEST);die;
            unset($data['__token__']);
            $res = Db::table($this->table)->where('id',$id)->update($data);
            if(!$res){
                return $this->error('编辑失败!');
            }
            return $this->success('编辑成功!');
        }

        if(!$id) $this->error('参数错误');
        $this->info = Db::table($this->table)->find($id);

        //
        $uid = $id;
        $data = db('xy_users')->where('parent_id', $uid)
            ->field('id,username,headpic,addtime,childs,tel')
            ->order('addtime desc')
            ->select();

        foreach ($data as &$datum) {
            //充值
            $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
            //提现
            $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 1)->sum('num');
        }

        //var_dump($data,$uid);die;

        //$this->cate = db('xy_goods_cate')->order('addtime asc')->select();
        $this->assign('cate',$data);

        return $this->fetch();
    }

    /**
     * 编辑会员登录状态
     * @auth true
     */
    public function edit_users_status()
    {
        $id = input('id/d',0);
        $status = input('status/d',0);
        if(!$id || !$status) return $this->error('参数错误');
        $res = model('Users')->edit_users_status($id,$status);
        if($res['code']!==0){
            return $this->error($res['info']);
        }
        return $this->success($res['info']);
    }
/**
     * 编辑会员登录状态
     * @auth true
     */
    public function edit_users_ewm()
    {
        $id = input('id/d',0);
        $invite_code = input('status/s','');
        if(!$id || !$invite_code) return $this->error('参数错误');

        $n = ($id%20);

        $dir = './upload/qrcode/user/'.$n . '/' . $id . '.png';
        if(file_exists($dir)) {
            unlink($dir);
        }

        $res = model('Users')->create_qrcode($invite_code,$id);
        if(0 && $res['code']!==0){
            return $this->error('失败');
        }
        return $this->success('成功');
    }

    //查看图片
    public function picinfo(){
        $this->pic = input('get.pic/s','');
        if(!$this->pic)return;
        $this->fetch();
    }

    /**
     * 客服管理
     * @auth true
     * @menu true
     */
    public function cs_list()
    {
        $this->title = '客服列表';
        $where = [];
        if(input('tel/s',''))$where[] = ['tel','like','%' . input('tel/s','') . '%'];
        if(input('username/s',''))$where[] = ['username','like','%' . input('username/s','') . '%'];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }
        $this->_query('xy_cs')
            ->where($where)
            ->page();
    }

    /**
     * 添加客服
     * @auth true
     * @menu true
     */
    public function add_cs()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $username = input('post.username/s','');
            $tel = input('post.tel/s','');
            $pwd = input('post.pwd/s','');
            $qq = input('post.qq/d',0);
            $wechat = input('post.wechat/s','');
            $qr_code = input('post.qr_code/s','');
            $time = input('post.time');
            $arr = explode('-', $time);
            $btime = substr($arr[0],0,5);
            $etime = substr($arr[1],1,5);
            $data = [
                'username'  => $username,
                'tel'       => $tel,
                'pwd'       => $pwd,    //需求不明确，暂时以明文存储密码数据
                'qq'        => $qq,
                'wechat'    => $wechat,
                'qr_code'   => $qr_code,
                'btime'     => $btime,
                'etime'     => $etime,
                'addtime'   => time(),
            ];
            $res = db('xy_cs')->insert($data);
            if($res) return $this->success('添加成功');
            return $this->error('添加失败，请刷新再试');
        }
        return $this->fetch();
    }

    /**
     * 客服登录状态
     * @auth true
     */
    public function edit_cs_status()
    {
        $this->applyCsrfToken();
        $this->_save('xy_cs', ['status' => input('post.status/d',1)]);
    }

    /**
     * 编辑客服信息
     * @auth true
     * @menu true
     */
    public function edit_cs()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $id = input('post.id/d',0);
            $username = input('post.username/s','');
            $tel = input('post.tel/s','');
            $pwd = input('post.pwd/s','');
            $qq = input('post.qq/d',0);
            $wechat = input('post.wechat/s','');
            $qr_code = input('post.qr_code/s','');
            $time = input('post.time');
            $arr = explode('-', $time);
            $btime = substr($arr[0],0,5);
            $etime = substr($arr[1],1,5);
            $data = [
                'username'  => $username,
                'tel'       => $tel,
                'qq'        => $qq,
                'wechat'    => $wechat,
                'qr_code'   => $qr_code,
                'btime'     => $btime,
                'etime'     => $etime,
            ];
            if($pwd) $data['pwd'] = $pwd;
            $res = db('xy_cs')->where('id',$id)->update($data);
            if($res!==false) return $this->success('编辑成功');
            return $this->error('编辑失败，请刷新再试');
        }
        $id = input('id/d',0);
        $this->list = db('xy_cs')->find($id);
        return $this->fetch();
    }

    /**
     * 客服调用代码
     * @auth true
     * @menu true
     */
    public function cs_code()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $code = input('post.code');
            $res = db('xy_script')->where('id',1)->update(['script'=>$code]);
            if($res!==false){
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }
        $this->code = db('xy_script')->where('id',1)->value('script');
        return $this->fetch();
    }

    /**
     * 编辑银行卡信息
     * @auth true
     * @menu true
     */
    public function edit_users_bk()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $id = input('post.id/d',0);
            $tel = input('post.tel/s','');
            $site = input('post.site/s','');
            $cardnum = input('post.cardnum/s','');
            $bankname = input('post.bankname/s','');
            $username = input('post.username/s','');
            $res = db('xy_bankinfo')->where('id',$id)->update(['tel'=>$tel,'site'=>$site,'cardnum'=>$cardnum,'username'=>$username,'bankname'=>$bankname]);
            if($res!==false){
                return $this->success('操作成功');
            }else{
                return $this->error('操作失败');
            }
        }
        $this->bk_info = Db::name('xy_bankinfo')->where('uid',input('id/d',0))->select();
        if(!$this->bk_info) $this->error('没有数据');
        return $this->fetch();
    }
	
		
	/**
     * 会员等级管理
     * @auth true
     * @menu true
     */
    public function level_list()
    {
		$this->title = '会员等级';
		$this->list = db('xy_user_level')->order('id desc')->select();
		return $this->fetch();
    }
	
	
    /**
     * 添加客户等级
     * @auth true
     * @menu true
     */
    public function add_level()
    {
        if(request()->isPost()){
			$this->applyCsrfToken();
            $name = input('post.name/s','');
            $order_num = input('post.order_num/s',0);
            $status = input('post.status/s',1);
            $data = [
                'name'  => $name,
                'order_num'       => $order_num,    //刷单数量决定等级级别
                'status'        => $status,
            ];
            $res = db('xy_user_level')->insert($data);
            if($res) return $this->success('添加成功');
            return $this->error('添加失败，请刷新再试');
        }
        return $this->fetch();
    }

    /**
     * 客户等级状态
     * @auth true
     */
    public function edit_level_status()
    {
		$this->applyCsrfToken();
		$id = input('id/d',0);
        $this->_save('xy_user_level', ['status' => input('post.status/d',1)]);
    }

    /**
     * 编辑客户等级
     * @auth true
     * @menu true
     */
    public function edit_level()
    {
        if(request()->isPost()){
			$this->applyCsrfToken();
            $id = input('post.id/d',0);
            $name = input('post.name/s','');
            $order_num = input('post.order_num/s',0);
            $data1 = [
                'name'  => $name,
                'order_num'       => $order_num,
            ];
            $res = db('xy_user_level')->where('id',$id)->update($data1);
            if($res!==false) return $this->success('编辑成功');
            return $this->error('编辑失败，请刷新再试');
        }
        $id = input('id/d',0);
        $this->list = db('xy_user_level')->find($id);
        return $this->fetch();
    }

}