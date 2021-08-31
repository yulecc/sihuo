<?php

namespace app\admin\controller\auth;

use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\common\controller\Backend;
use fast\Random;
use fast\Tree;
use think\Db;
use think\Validate;

/**
 * 管理员管理
 *
 * @icon   fa fa-users
 * @remark 一个管理员可以有多个角色组,左侧的菜单根据管理员所拥有的权限进行生成
 */
class Admin extends Backend
{

    /**
     * @var \app\admin\model\Admin
     */
    protected $model = null;
    protected $selectpageFields = 'id,username,nickname,avatar';
    protected $searchFields = 'id,username,nickname';
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Admin');
        //$this->auth->isSuperAdmin()
        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

        $groupList = collection(AuthGroup::where('id', 'in', $this->childrenGroupIds)->select())->toArray();

        Tree::instance()->init($groupList);
        $groupdata = [];
        if ($this->auth->isSuperAdmin()) {
            $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
            foreach ($result as $k => $v) {
                $groupdata[$v['id']] = $v['name'];
            }
        } else {
            $result = [];
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['id']));
                $temp = [];
                foreach ($childlist as $k => $v) {
                    $temp[$v['id']] = $v['name'];
                }
                $result[__($n['name'])] = $temp;
            }
            $groupdata = $result;
        }

        $this->view->assign('groupdata', $groupdata);
        $this->assignconfig("admin", ['id' => $this->auth->id]);
    }

    /**
     * 管理员管理
     *
     * @ApiTitle    (管理员列表)
     * @ApiSummary  (管理员列表)
     * @ApiSector   (管理员管理)
     * @ApiMethod   (GET)
     * @ApiRoute    (/api.php/auth/admin/index)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="username", type="String", required=false, description="用户名")
     * @ApiParams   (name="autharea_id", type="int", required=false, description="区域权限ID")
     * @ApiParams   (name="group_id", type="int", required=false, description="权限ID")
     * @ApiParams   (name="company", type="String", required=false, description="单位名称")
     * @ApiParams   (name="limit", type="int", required=false, description="每页数量")
     * @ApiParams   (name="page", type="int", required=false, description="页码")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "total": 1,
    "rows": [
    {
    "id": 7,
    "username": "admin2",
    "nickname": "",
    "avatar": "/assets/img/avatar.png",
    "email": "",
    "loginfailure": 0,
    "logintime": 1630129435,
    "loginip": "127.0.0.1",
    "createtime": 1630128107,
    "updatetime": 1630129435,
    "status": "normal",
    "mobile": "18858585588",
    "company": "宁波大学",
    "autharea_id": 2,
    "groups": "2",
    "groups_text": "管理员组"
    }
    ]
    })
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $childrenGroupIds = $this->childrenGroupIds;
            $groupName = AuthGroup::where('id', 'in', $childrenGroupIds)
                ->column('id,name');
            $authGroupList = AuthGroupAccess::where('group_id', 'in', $childrenGroupIds)
                ->field('uid,group_id')
                ->select();

            $adminGroupName = [];
            foreach ($authGroupList as $k => $v) {
                if (isset($groupName[$v['group_id']])) {
                    $adminGroupName[$v['uid']][$v['group_id']] = $groupName[$v['group_id']];
                }
            }
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $adminGroupName[$this->auth->id][$n['id']] = $n['name'];
            }
//            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $param = $this->request->param();
            $limit = $param['limit'];
            $page = $param['page'];
            $group_id = $param['group_id'];
            unset($param['limit'],$param['group_id'],$param['page']);
            $where = [];
            if(array_filter($param)){

                $where = array_filter($param);
            }
            if($group_id){

                $where['b.group_id'] = $group_id;
            }
            $list = $this->model
                ->alias('a')
                ->join('auth_group_access b','b.uid=a.id','left')
                ->where($where)
                ->where('a.id', 'in', $this->childrenAdminIds)
                ->field(['password', 'salt', 'token'], true)
                ->group('a.id')
//                ->order($sort, $order)
                ->paginate($limit,false,['page'=>$page]);

            foreach ($list as $k => &$v) {
                $groups = isset($adminGroupName[$v['id']]) ? $adminGroupName[$v['id']] : [];
                $v['groups'] = implode(',', array_keys($groups));
                $v['groups_text'] = implode(',', array_values($groups));
            }
            unset($v);
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
//        return $this->view->fetch();
    }

    /**
     * 管理员管理
     *
     * @ApiTitle    (管理员添加)
     * @ApiSummary  (管理员添加)
     * @ApiSector   (管理员管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/auth/admin/add)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="username", type="String", required=true, description="用户名")
     * @ApiParams   (name="group_id", type="string", required=true, description="角色ID,多个ID用，隔开")
     * @ApiParams   (name="autharea_id", type="int", required=true, description="区域ID")
     * @ApiParams   (name="company", type="String", required=true, description="单位")
     * @ApiParams   (name="mobile", type="String", required=true, description="联系电话")
     * @ApiParams   (name="password", type="string", required=true, description="密码")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "操作成功",
    "data": []
    "url": "",
    "wait": 3
    })
     */
    public function add()
    {
        if ($this->request->isPost()) {
//            $this->token();
            $params = $this->request->post();
            if ($params) {
                Db::startTrans();
                try {
                    if (!Validate::is($params['password'], '\S{6,16}')) {
                        exception(__("Please input correct password"));
                    }
                    $group = $params['group_id'];
                    unset($params['group_id']);
                    $params['salt'] = Random::alnum();
                    $params['password'] = md5(md5($params['password']) . $params['salt']);
                    $params['avatar'] = '/assets/img/avatar.png'; //设置新管理员默认头像。
                    $result = $this->model->validate('Admin.add')->save($params);
                    if ($result === false) {
                        exception($this->model->getError());
                    }
//                    $group = $this->request->post();
                    $group = explode(',',$group);
                    //过滤不允许的组别,避免越权
                    $group = array_intersect($this->childrenGroupIds, $group);
                    if (!$group) {
                        exception(__('The parent group exceeds permission limit'));
                    }

                    $dataset = [];
                    foreach ($group as $value) {
                        $dataset[] = ['uid' => $this->model->id, 'group_id' => $value];
                    }
                    model('AuthGroupAccess')->saveAll($dataset);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                $this->success(lang('Success'));
                exit();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
//        return $this->view->fetch();
    }

    /**
     * 管理员管理
     *
     * @ApiTitle    (管理员编辑)
     * @ApiSummary  (管理员编辑  [GET时候获取用户详情])
     * @ApiSector   (管理员管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/auth/admin/edit)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="ids", type="int", required=true, description="需要编辑的ID")
     * @ApiParams   (name="username", type="String", required=true, description="用户名")
     * @ApiParams   (name="group_id", type="string", required=true, description="角色ID,多个ID用，隔开")
     * @ApiParams   (name="autharea_id", type="int", required=true, description="区域ID")
     * @ApiParams   (name="company", type="String", required=true, description="单位")
     * @ApiParams   (name="mobile", type="String", required=true, description="联系电话")
     * @ApiParams   (name="password", type="string", required=true, description="密码 没有则不修改")
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
    public function edit($ids = null)
    {
        $ids = $this->request->param('ids');
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if (!in_array($row->id, $this->childrenAdminIds)) {
            $this->error(__('You have no permission'));
        }
        if ($this->request->isPost()) {
//            $this->token();
            $params = $this->request->post();
            if ($params) {
                Db::startTrans();
                try {
                    $group = $params['group_id'];
                    unset($params['group_id']);
                    if ($params['password']) {
                        if (!Validate::is($params['password'], '\S{6,16}')) {
                            exception(__("Please input correct password"));
                        }
                        $params['salt'] = Random::alnum();
                        $params['password'] = md5(md5($params['password']) . $params['salt']);
                    } else {
                        unset($params['password'], $params['salt']);
                    }
                    //这里需要针对username和email做唯一验证
                    $adminValidate = \think\Loader::validate('Admin');
                    $adminValidate->rule([
                        'username' => 'require|regex:\w{3,12}|unique:admin,username,' . $row->id,
                        'mobile'    => 'require|regex:/^1[3456789]\d{9}$/|unique:admin,mobile,' . $row->id,
                        'password' => 'regex:\S{32}',
                        'company' => 'require',
                    ]);
                    $result = $row->validate('Admin.edit')->save($params);
                    if ($result === false) {
                        exception($row->getError());
                    }

                    // 先移除所有权限
                    model('AuthGroupAccess')->where('uid', $row->id)->delete();

//                    $group = $this->request->post("group/a");
                    $group = explode(',',$group);
                    // 过滤不允许的组别,避免越权
                    $group = array_intersect($this->childrenGroupIds, $group);
                    if (!$group) {
                        exception(__('The parent group exceeds permission limit'));
                    }

                    $dataset = [];
                    foreach ($group as $value) {
                        $dataset[] = ['uid' => $row->id, 'group_id' => $value];
                    }
                    model('AuthGroupAccess')->saveAll($dataset);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                $this->success(__('Success'));
                exit();
            }
            $this->error(__('Parameter %s can not be empty', ''));
            exit();
        }
        $grouplist = $this->auth->getGroups($row['id']);
        $groupids = [];
        foreach ($grouplist as $k => $v) {
            $groupids[] = $v['id'];
        }

        $return_data['row'] = $row;
        $return_data['groupids'] = $groupids;
        $this->success(lang('Success'),'',$return_data);
    }

    /**
     * 管理员管理
     *
     * @ApiTitle    (管理员删除)
     * @ApiSummary  (管理员删除)
     * @ApiSector   (管理员管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/auth/admin/del)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="ids", type="int", required=true, description="需要删除的ID")
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
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids && $ids != 1) {
            $ids = array_intersect($this->childrenAdminIds, array_filter(explode(',', $ids)));
            // 避免越权删除管理员
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model->where('id', 'in', $ids)->where('id', 'in', function ($query) use ($childrenGroupIds) {
                $query->name('auth_group_access')->where('group_id', 'in', $childrenGroupIds)->field('uid');
            })->select();
            if ($adminList) {
                $deleteIds = [];
                foreach ($adminList as $k => $v) {
                    $deleteIds[] = $v->id;
                }
                $deleteIds = array_values(array_diff($deleteIds, [$this->auth->id]));
                if ($deleteIds) {
                    Db::startTrans();
                    try {
                        $this->model->destroy($deleteIds);
                        model('AuthGroupAccess')->where('uid', 'in', $deleteIds)->delete();
                        Db::commit();
                    } catch (\Exception $e) {
                        Db::rollback();
                        $this->error($e->getMessage());
                    }
                    $this->success(__('Success'));
                    exit();
                }
                $this->error(__('No rows were deleted'));
                exit();
            }
        }
        $this->error(__('You have no permission'));
    }

    /**
     * 批量更新
     *@ApiInternal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    /**
     * 下拉搜索
     * @ApiInternal
     */
    public function selectpage()
    {
        $this->dataLimit = 'auth';
        $this->dataLimitField = 'id';
        return parent::selectpage();
    }



}
