<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 水质监测
 *
 * @icon fa fa-circle-o
 */
class Water extends Backend
{
    
    /**
     * Water模型对象
     * @var \app\admin\model\Water
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Water;
        $this->view->assign("waterCategoryList", $this->model->getWaterCategoryList());
    }

//    public function import()
//    {
//        parent::import();
//    }


    /**
     * 水质监测
     *
     * @ApiTitle    (断面信息查看)
     * @ApiSummary  (断面信息查看)
     * @ApiSector   (水质监测)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/water/index)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="mon_source", type="String", required=false, description="监测来源")
     * @ApiParams   (name="mon_category", type="int", required=false, description="监测类别")
     * @ApiParams   (name="mon_time", type="datetime", required=false, description="监测时间 2个时间之前以~分割 2021-10-01~2021-10-02")
     * @ApiParams   (name="limit", type="int", required=true, description="每页数量")
     * @ApiParams   (name="page", type="int", required=true, description="页码")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ()
     */

    public function index()
    {



    }

}
