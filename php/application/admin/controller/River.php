<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 河湖管理
 *
 * @icon fa fa-circle-o
 */
class River extends Backend
{

    /**
     * River模型对象
     * @var \app\admin\model\River
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\River;

    }

    /**
     * @ApiInternal
     */
    public function import()
    {
        parent::import();
    }


    /**
     * 河湖管理
     *
     * @ApiTitle    (河湖信息查看)
     * @ApiSummary  (河湖信息查看)
     * @ApiSector   (河湖管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/river/index)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="river_type", type="int", required=false, description="类型 多个以,隔开")
     * @ApiParams   (name="river_name", type="String", required=false, description="水域名称")
     * @ApiParams   (name="river_attr", type="int", required=false, description="属性 多个以,隔开")
     * @ApiParams   (name="radmin_name", type="string", required=false, description="河湖长姓名")
     * @ApiParams   (name="radmin_plevel", type="int", required=false, description="河湖长等级")
     * @ApiParams   (name="river_level", type="int", required=false, description="水域等级")
     * @ApiParams   (name="circle_code", type="string", required=false, description="圈码")
     * @ApiParams   (name="tributary", type="string", required=false, description="所在干流")
     * @ApiParams   (name="limit", type="int", required=false, description="每页数量")
     * @ApiParams   (name="page", type="int", required=false, description="页码")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="radmin.name", type="string", required=true, sample="河湖长姓名")
     * @ApiReturnParams   (name="radmin.plevel", type="string", required=true, sample="河湖长等级")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "total": 1,
    "rows": [
    {
    "id": 1,
    "code": "hd00-0501d",
    "river_circle": "123",
    "river_type": 37,
    "river_name": "探清水河",
    "river_level": 42,
    "river_attr": 38,
    "radmin_id": 4,
    "circle_code": "31231",
    "circle_name": "圈码",
    "tributary": "支流",
    "createtime": 1630563000,
    "updatetime": 1630563000,
    "radmin": {
    "id": 4,
    "name": "哈好",
    "plevel": 35,
    "type": "擦",
    "autharea": 29,
    "company_id": 1,
    "job": "县长",
    "createtime": 1630387122,
    "updatetime": 1630387122,
    "plevel_text": "一等",
    "autharea_text": "前江街道"
    },
    "river_type_text": "河道",
    "river_level_text": "乡镇级",
    "river_attr_text": "分段"
    }
    ]
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
            if ($param['river_type']) {

                $where['river_type'] = ['in', $param['river_type']];
            }
            if ($param['river_name']) {

                $where['river_name'] = ['like', '%' . $param['river_name'] . '%'];
            }
            if ($param['river_attr']) {

                $where['river_attr'] = ['in', $param['river_attr']];
            }
            if ($param['radmin_name']) {

                $where['radmin.name'] = ['like', '%' . $param['radmin_name'] . '%'];
            }
            if ($param['radmin_plevel']) {

                $where['radmin.plevel'] = $param['radmin_plevel'];
            }

            if ($param['river_level']) {

                $where['river_level'] = $param['river_level'];
            }

            if ($param['circle_code']) {

                $where['circle_code'] = $param['circle_code'];
            }

            if ($param['tributary']) {

                $where['tributary'] = ['like', '%' . $param['tributary'] . '%'];

            }

            $limit = $param['limit'];
            $page = $param['page'];
            $list = $this->model
                ->with(['radmin'])
                ->where($where)
//                    ->order($sort, $order)
                ->paginate($limit, false, ['page' => $page]);

            foreach ($list as $key => $row) {

//                $list[$key]['river_type'] = $this->cache_normal[$row['river_type']]['value'];
//                $list[$key]['river_level'] = $this->cache_normal[$row['river_level']]['value'];
//                $list[$key]['river_attr'] = $this->cache_normal[$row['river_attr']]['value'];
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            $this->success('操作成功', $result);
        }
//        return $this->view->fetch();
    }


    /**
     * 河湖管理
     *
     * @ApiTitle    (河湖信息添加)
     * @ApiSummary  (河湖信息添加)
     * @ApiSector   (河湖管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/river/add)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="code", type="string", required=true, description="编码")
     * @ApiParams   (name="river_circle", type="String", required=true, description="河湖圈")
     * @ApiParams   (name="river_type", type="int", required=true, description="类型")
     * @ApiParams   (name="river_name", type="String", required=true, description="水域名称")
     * @ApiParams   (name="river_level", type="int", required=true, description="水域等级")
     * @ApiParams   (name="river_attr", type="int", required=true, description="属性")
     * @ApiParams   (name="radmin_id", type="int", required=true, description="河湖长ID")
     * @ApiParams   (name="circle_code", type="string", required=true, description="圈码")
     * @ApiParams   (name="circle_name", type="string", required=true, description="所在河湖圈")
     * @ApiParams   (name="tributary", type="string", required=true, description="所在干流")
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
