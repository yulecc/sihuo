<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 河道管理
 *
 * @icon fa fa-circle-o
 */
class Radmin extends Backend
{
    
    /**
     * Radmin模型对象
     * @var \app\admin\model\Radmin
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Radmin;

    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 河道管理
     *
     * @ApiTitle    (河长信息查看)
     * @ApiSummary  (河长信息查看)
     * @ApiSector   (河道管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/radmin/index)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="name", type="string", required=false, description="人员姓名")
     * @ApiParams   (name="autharea", type="int", required=false, description="行政区划")
     * @ApiParams   (name="plevel", type="int", required=false, description="人员等级")
     * @ApiParams   (name="company_name", type="string", required=false, description="公司名称")
     * @ApiParams   (name="limit", type="int", required=false, description="每页数量")
     * @ApiParams   (name="page", type="int", required=false, description="页码")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "操作成功",
    "data": "",
    "url": "http://www.river.test/api.html",
    "wait": 3
    })
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
//            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $param = $this->request->param();
            $where = [];
            if($param['name']){

                $where['radmin.name'] = $param['name'];
            }
            if($param['autharea']){

                $where['autharea'] = $param['autharea'];
            }
            if($param['company_name']){

                $where['company.name'] = ['like','%'.$param['company_name'].'%'];
            }
            if($param['plevel']){

                $where['plevel'] = $param['plevel'];
            }
            $limit = $param['limit'];
            $page = $param['page'];
            $list = $this->model
                    ->with(['company'])
                    ->where($where)
//                    ->order($sort, $order)
                    ->paginate($limit,false,['page'=>$page]);

            foreach ($list as $key=>$row) {
                
                $row->getRelation('company')->visible(['name']);
                $list[$key]['plevel'] = $this->cache_normal[$row['plevel']]['value'];
                $list[$key]['autharea'] = $this->cache_normal[$row['autharea']]['value'];

            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 河道管理
     *
     * @ApiTitle    (河长信息添加)
     * @ApiSummary  (河长信息添加)
     * @ApiSector   (河道管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/radmin/add)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="name", type="string", required=true, description="姓名")
     * @ApiParams   (name="plevel", type="String", required=true, description="等级ID")
     * @ApiParams   (name="type", type="string", required=true, description="类型")
     * @ApiParams   (name="autharea", type="int", required=true, description="行政区域ID")
     * @ApiParams   (name="company_id", type="int", required=true, description="公司ID")
     * @ApiParams   (name="job", type="string", required=true, description="职务")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "操作成功",
    "data": "",
    "url": "http://www.river.test/api.html",
    "wait": 3
    })
     */
    public function add()
    {

      parent::add();

    }


}
