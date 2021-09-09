<?php

namespace app\admin\controller;

use app\admin\library\Auth;
use app\common\controller\Backend;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use think\exception\PDOException;

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
    /**
     * 水质监测
     *
     * @ApiTitle    (断面信息导入)
     * @ApiSummary  (断面信息导入)
     * @ApiSector   (水质监测)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/water/import)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="file", type="file", required=true, description="导入Excel文件")
     * @ApiParams   (name="explode_date", type="date", required=true, description="格式2020-01-11")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "导入成功",
    "data": "",
    "url": "http://www.river.test/api.html",
    "wait": 3
    })
     */
    public function import()
    {
        $file = request()->file('file');
        $explode_date = request()->param('explode_date');
        if(!$explode_date){

            $this->error('导入日期不能为空');
        }
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel';
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $info = $file->move($filePath);
        $filePath = './uploads/excel'  . '/' . str_replace('\\','/',$info->getSaveName());
        //实例化reader
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $this->error(__('Unknown data format'));
        }
        if ($ext === 'csv') {
            $file = fopen($filePath, 'r');
            $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
            $fp = fopen($filePath, "w");
            $n = 0;
            while ($line = fgets($file)) {
                $line = rtrim($line, "\n\r\0");
                $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                if ($encoding != 'utf-8') {
                    $line = mb_convert_encoding($line, 'utf-8', $encoding);
                }
                if ($n == 0 || preg_match('/^".*"$/', $line)) {
                    fwrite($fp, $line . "\n");
                } else {
                    fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                }
                $n++;
            }
            fclose($file) || fclose($fp);

            $reader = new Csv();
        } elseif ($ext === 'xls') {
            $reader = new Xls();
        } else {
            $reader = new Xlsx();
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            } else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }
        //加载文件
        $insert = [];
        try {
            if (!$PHPExcel = $reader->load($filePath)) {
                $this->error(__('Unknown data format'));
            }
            $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
            $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
            $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
            $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
            $fields = [];
            for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $fields[] = $val;
                }
            }
            for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                $values = [];
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $values[] = is_null($val) ? '' : $val;
                }
                $row = [];
                $temp = array_combine($fields, $values);
                foreach ($temp as $k => $v) {
                    if (isset($fieldArr[$k]) && $k !== '') {

                        if(in_array($fieldArr[$k],['river_type','river_attribute','people','section_type','control_level'])){

                            $cache_group_value = array_column($this->cache_group[$fieldArr[$k]],'value');
                            if(in_array($v,$cache_group_value)){

                                foreach ($this->cache_group[$fieldArr[$k]] as $value){

                                    if($value['value'] == $v){

                                        $v = $value['id'];

                                    }

                                }

                            }else{

                                $this->error('属性'.$v.'不存在');
                            }


                        }
                        $row[$fieldArr[$k]] = $v;
                    }
                }
                if ($row) {

                    $row['explode_date'] = $explode_date;
                    $insert[] = $row;
                }
            }

        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }

        try {
            //是否包含admin_id字段
            $has_admin_id = false;
            foreach ($fieldArr as $name => $key) {
                if ($key == 'admin_id') {
                    $has_admin_id = true;
                    break;
                }
            }
            if ($has_admin_id) {
                $auth = Auth::instance();
                foreach ($insert as &$val) {
                    if (!isset($val['admin_id']) || empty($val['admin_id'])) {
                        $val['admin_id'] = $auth->isLogin() ? $auth->id : 0;
                    }
                }
            }
            $this->model->saveAll($insert);
        } catch (PDOException $exception) {
            $msg = $exception->getMessage();
            if (preg_match("/.+Integrity constraint violation: 1062 Duplicate entry '(.+)' for key '(.+)'/is", $msg, $matches)) {
                $msg = "导入失败，包含【{$matches[1]}】的记录已存在";
            };
            $this->error($msg);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success('导入成功');
    }


    /**
     * 水质监测
     *
     * @ApiTitle    (断面信息查看)
     * @ApiSummary  (断面信息查看)
     * @ApiSector   (水质监测)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/water/section_index)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="river_name", type="String", required=false, description="河道名称")
     * @ApiParams   (name="point_number", type="String", required=false, description="点位编号")
     * @ApiParams   (name="point_name", type="String", required=false, description="点位名称")
     * @ApiParams   (name="water_category", type="String", required=false, description="水质类别")
     * @ApiParams   (name="river_type", type="int", required=false, description="类型 多个以,隔开")
     * @ApiParams   (name="section_type", type="int", required=false, description="断面类型")
     * @ApiParams   (name="people", type="int", required=false, description="河湖长等级")
     * @ApiParams   (name="control_level", type="int", required=false, description="控制方式")
     * @ApiParams   (name="limit", type="int", required=true, description="每页数量")
     * @ApiParams   (name="page", type="int", required=true, description="页码")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ()
     */
    public function section_index()
    {

        $param = $this->request->param();
        $where = [];
        if($param['river_name']){

            $where['river_name'] = ['like','%'.$param['river_name'].'%'];
        }
        if($param['point_number']){

            $where['point_number'] = ['like','%'.$param['point_number'].'%'];
        }
        if($param['point_name']){

            $where['point_name'] = ['like','%'.$param['point_name'].'%'];
        }
        if($param['water_category']){

            $where['water_category'] = $param['water_category'];
        }
        if($param['river_type']){

            $where['river_type'] = ['in',$param['river_type']];
        }
        if($param['people']){

            $where['people'] = $param['people'];
        }

        if($param['section_type']){

            $where['section_type'] = $param['section_type'];
        }

        if($param['control_level']){

            $where['control_level'] = $param['control_level'];
        }

        if($param['river_attribute']){

            $where['river_attribute'] = $param['river_attribute'];
        }

        $limit = $param['limit'];
        $page = $param['page'];
        $rows = $this->model->where($where)->page($page,$limit)->select();
        $total =  $this->model->where($where)->count();
        $list['river_type'] = $this->cache_group['river_type'];
        $list['river_attribute'] = $this->cache_group['river_attribute'];
        $list['section_type'] = $this->cache_group['section_type'];
        $list['people'] = $this->cache_group['people'];
        $list['control_level'] = $this->cache_group['control_level'];
        $result = array("total" => $total, "rows" => $rows,'search_list'=>$list);
        $this->success('获取成功',$result);
    }

    /**
     * 水质监测
     *
     * @ApiTitle    (断面信息导出)
     * @ApiSummary  (断面信息导出)
     * @ApiSector   (水质监测)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/water/download)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="river_name", type="String", required=false, description="河道名称")
     * @ApiParams   (name="point_number", type="String", required=false, description="点位编号")
     * @ApiParams   (name="point_name", type="String", required=false, description="点位名称")
     * @ApiParams   (name="water_category", type="String", required=false, description="水质类别")
     * @ApiParams   (name="river_type", type="int", required=false, description="类型 多个以,隔开")
     * @ApiParams   (name="section_type", type="int", required=false, description="断面类型")
     * @ApiParams   (name="people", type="int", required=false, description="河湖长等级")
     * @ApiParams   (name="control_level", type="int", required=false, description="控制方式")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ()
     */
    public function download(){

        $param = $this->request->param();
        $where = [];
        if($param['river_name']){

            $where['river_name'] = ['like','%'.$param['river_name'].'%'];
        }
        if($param['point_number']){

            $where['point_number'] = ['like','%'.$param['point_number'].'%'];
        }
        if($param['point_name']){

            $where['point_name'] = ['like','%'.$param['point_name'].'%'];
        }
        if($param['water_category']){

            $where['water_category'] = $param['water_category'];
        }
        if($param['river_type']){

            $where['river_type'] = ['in',$param['river_type']];
        }
        if($param['people']){

            $where['people'] = $param['people'];
        }

        if($param['section_type']){

            $where['section_type'] = $param['section_type'];
        }

        if($param['control_level']){

            $where['control_level'] = $param['control_level'];
        }

        if($param['river_attribute']){

            $where['river_attribute'] = $param['river_attribute'];
        }
        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        foreach ($list as $k => $v) {

            if($v['COLUMN_COMMENT']){

                if ($importHeadType == 'comment') {
                    $file_tile[$v['COLUMN_NAME']] = $v['COLUMN_COMMENT'];
                } else {
                    $file_tile[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
                }
            }

        }

        $arr = ['river_type','river_attribute','section_type','people','control_level'];
        foreach ($file_tile as $k=>$value){

            if(in_array($k,$arr)){

                $file_tile[$k.'_text'] = $value;
                unset($file_tile[$k]);

            }

        }

        $rows = $this->model->where($where)->select();
        download_excel('断面信息', '断面信息', $file_tile, $rows);


    }



}
