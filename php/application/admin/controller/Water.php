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

                        if(in_array($fieldArr[$k],['river_type','river_attribute','people','section_type'])){

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
    public function section_index()
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
