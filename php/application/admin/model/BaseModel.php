<?php
namespace app\admin\model;

use think\Model;

class BaseModel extends Model{

    public $cache_normal;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->cache_normal = get_config_list(false)['normal'];
    }

}