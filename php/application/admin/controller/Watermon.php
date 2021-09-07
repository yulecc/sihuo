<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 水质监测
 *
 * @icon fa fa-circle-o
 */
class Watermon extends Backend
{
    
    /**
     * WaterMon模型对象
     * @var \app\admin\model\WaterMon
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\WaterMon;

    }


    /**
     * 水质监测
     *
     * @ApiTitle    (专题图片信息添加)
     * @ApiSummary  (其中mon_category去请求"api.php/whole/get_group_list?type=mon_type")
     * @ApiSector   (水质监测)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/watermon/add)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="mon_source", type="String", required=true, description="监测来源")
     * @ApiParams   (name="mon_category", type="int", required=true, description="监测类别")
     * @ApiParams   (name="mon_time", type="datetime", required=true, description="监测时间")
     * @ApiParams   (name="mon_images", type="json", required=false, description="图片 以json形式传递")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "操作成功!",
    "data": "",
    "url": "http://www.river.test/api.html",
    "wait": 3
    })
     */
    public function add(){

        $param = $this->request->param();
        if(!$param['mon_category']){

            $this->error('监测来源不能为空');
            exit();
        }
        $ids = array_column($this->cache_group['mon_type'],'id');
        if(!in_array($param['mon_category'],$ids)){

            $this->error('监测来源数据有误');
            exit();
        }

        parent::add();

    }

    /**
     * 水质监测
     *
     * @ApiTitle    (专题图片信息查看)
     * @ApiSummary  (专题图片信息查看)
     * @ApiSector   (水质监测)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/watermon/index)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="mon_source", type="String", required=false, description="监测来源")
     * @ApiParams   (name="mon_category", type="int", required=false, description="监测类别")
     * @ApiParams   (name="mon_time", type="datetime", required=false, description="监测时间 2个时间之前以~分割 2021-10-01~2021-10-02")
     * @ApiParams   (name="limit", type="int", required=true, description="每页数量")
     * @ApiParams   (name="page", type="int", required=true, description="页码")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "total": 2,
    "rows": [
    {
    "id": 2,
    "mon_source": "遥感",
    "mon_category": 51,
    "mon_time": "2021-09-07 11:11:00",
    "mon_images": "",
    "updatetime": 1630997782,
    "createtime": 1630997782,
    "mon_category_text": "高锰酸盐指数"
    },
    {
    "id": 3,
    "mon_source": "遥感",
    "mon_category": 50,
    "mon_time": "2021-09-07 11:11:00",
    "mon_images": "[\"https:\\/\\/fanyi-cdn.cdn.bcebos.com\\/static\\/translation\\/img\\/header\\/logo_e835568.png\",\"https:\\/\\/fanyi-cdn.cdn.bcebos.com\\/static\\/translation\\/img\\/header\\/logo_e835568.png\"]",
    "updatetime": 1630998063,
    "createtime": 1630998063,
    "mon_category_text": "溶解氧"
    }
    ]
    })
     */
    public function index()
    {

        $param = $this->request->param();
        $where = [];
        if($param['mon_source']){

            $where['mon_source'] = $param['mon_source'];
        }
        if($param['mon_category']){

            $where['mon_category'] = $param['mon_category'];
        }
        if($param['mon_time']){

            $mon_time = explode('~',$param['mon_time']);
            $where['mon_time'] = ['between',[$mon_time[0],$mon_time[1]]];
        }
        $limit = $param['limit'];
        $page = $param['page'];
        $rows = $this->model->where($where)->page($page,$limit)->select();
        $total =  $this->model->where($where)->count();
        $source = $this->model->field('id,mon_source')->group('mon_source')->select();
        $mon_category = $this->cache_group['mon_type'];
        $result = array("total" => $total, "rows" => $rows,'source'=>$source,'category'=>$mon_category);
        return json($result);
    }


}
