<?php

namespace app\admin\controller;

use app\common\controller\Backend;
/**
 * 其他
 */
class Whole extends Backend
{
    protected $noNeedRight = ['*'];

    /**
     * 其他
     *
     * @ApiTitle    (按组别获取配置文件信息)
     * @ApiSummary  (按组别获取配置文件信息)
     * @ApiSector   (其他)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/whole/get_group_list)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="type", type="String", required=true, description="类型")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "47": {
    "id": 47,
    "name": "comprehensive",
    "group": "mon_type",
    "title": "name",
    "tip": "",
    "type": "string",
    "value": "综合水质评价",
    "content": "",
    "rule": "",
    "extend": "",
    "setting": "{\"table\":\"\",\"conditions\":\"\",\"key\":\"\",\"value\":\"\"}"
    }
    })
     */
    public function get_group_list()
    {

        $type = $this->request->param('type');
        if (!$type) {

            $this->error('数据有误');
            exit();
        }

        $this->success('获取成功',$this->cache_group[$type]);
    }

    /**
     * 其他
     *
     * @ApiTitle    (图片上传)
     * @ApiSummary  (图片上传)
     * @ApiSector   (其他)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/whole/imgUpload)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="file", type="file", required=true, description="图片路径")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "47": {
    "id": 47,
    "name": "comprehensive",
    "group": "mon_type",
    "title": "name",
    "tip": "",
    "type": "string",
    "value": "综合水质评价",
    "content": "",
    "rule": "",
    "extend": "",
    "setting": "{\"table\":\"\",\"conditions\":\"\",\"key\":\"\",\"value\":\"\"}"
    }
    })
     */
    public function imgUpload()
    {
        $file = request()->file('file');
        $filePath = 'images';
        if ($file) {
            $filePaths = ROOT_PATH . 'public' . DS . 'uploads' . DS . $filePath;
            if (!file_exists($filePaths)) {
                mkdir($filePaths, 0777, true);
            }
            $info = $file->move($filePaths);
            if ($info) {

                $imgpath = '/uploads/' . $filePath . '/' . str_replace('\\','/',$info->getSaveName());
                $data = [
                    'domain' => $_SERVER['HTTP_HOST'],
                    'filepath' => $imgpath,
                ];

                $this->success('上传成功',$data);

            } else {
                // 上传失败获取错误信息
                $this->error('上传失败');
            }
        }
    }


}