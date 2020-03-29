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
class Pay extends Controller
{

    /**
     * 指定当前数据表
     * @var string
     */
    protected $table = 'xy_pay';

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
        $query->field('*')
            ->where($where)
            ->order('u.id asc')
            ->page();
    }



    /**
     * 编辑会员
     * @auth true
     * @menu true
     */
    public function edit()
    {
        $id = input('get.id',0);

        if(request()->isPost()){
            $id = input('post.id/d',0);
            $name = input('post.name/s','');
            $min = input('post.min/f',0);
            $max = input('post.max/f','');
            $ewm = input('post.ewm/s','');
            $token = input('__token__');

            $data =array(
                'name'=>$name,
                'min'=>$min,
                'max'=>$max,
                'ewm'=>$ewm,
            );
            $res = Db::table($this->table)->where('id',$id)->update($data);
            if(!$res){
                return $this->error($res['info']);
            }
            $this->success('编辑成功','/admin.html#/admin/pay/index.html');
        }
        if(!$id) $this->error('参数错误');
        $this->info = Db::table($this->table)->find($id);

        //var_dump($this->info);die;
        return $this->fetch();
    }


    /**
     * 禁用系统权限
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        //$this->applyCsrfToken();
        $this->_save($this->table, ['status' => '0']);
    }

    /**
     * 启用系统权限
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function resume()
    {
        //$this->applyCsrfToken();
        $this->_save($this->table, ['status' => '1']);
    }


}