<?php

namespace app\admin\controller\auth;

use app\admin\model\AuthGroup;
use app\common\controller\Backend;
use fast\Tree;
use think\Db;
use think\Exception;

/**
 * 角色管理
 *
 * @icon   fa fa-group
 * @remark 角色组可以有多个,角色有上下级层级关系,如果子角色有角色组和管理员的权限则可以派生属于自己组别下级的角色组或管理员
 */
class Group extends Backend
{

    /**
     * @var \app\admin\model\AuthGroup
     */
    protected $model = null;
    //当前登录管理员所有子组别
    protected $childrenGroupIds = [];
    //当前组别列表数据
    protected $grouplist = [];
    protected $groupdata = [];
    //无需要权限判断的方法
    protected $noNeedRight = ['roletree'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AuthGroup');

        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

        $groupList = collection(AuthGroup::where('id', 'in', $this->childrenGroupIds)->select())->toArray();

        Tree::instance()->init($groupList);
        $groupList = [];
        if ($this->auth->isSuperAdmin()) {
            $groupList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
        } else {
            $groups = $this->auth->getGroups();
            $groupIds = [];
            foreach ($groups as $m => $n) {
                if (in_array($n['id'], $groupIds) || in_array($n['pid'], $groupIds)) {
                    continue;
                }
                $groupList = array_merge($groupList, Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['pid'])));
                foreach ($groupList as $index => $item) {
                    $groupIds[] = $item['id'];
                }
            }
        }
        $groupName = [];
        foreach ($groupList as $k => $v) {
            $groupName[$v['id']] = $v['name'];
        }

        $this->grouplist = $groupList;
        $this->groupdata = $groupName;
        $this->assignconfig("admin", ['id' => $this->auth->id, 'group_ids' => $this->auth->getGroupIds()]);

        $this->view->assign('groupdata', $this->groupdata);
    }

    /**
     * 角色管理
     *
     * @ApiTitle    (角色列表)
     * @ApiSummary  (角色列表)
     * @ApiSector   (角色管理)
     * @ApiMethod   (GET)
     * @ApiRoute    (/api.php/auth/group/index)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
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
    public function index()
    {
        if ($this->request->isAjax()) {
            $list = $this->grouplist;
            foreach ($list as $key=>$value){

                $list[$key]['name'] = str_replace('&nbsp;└','',str_replace(' ','',$value['name']));

            }
            $total = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 角色管理
     *
     * @ApiTitle    (角色添加)
     * @ApiSummary  (角色添加)
     * @ApiSector   (角色管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/auth/group/add)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="name", type="String", required=true, description="角色名称")
     * @ApiParams   (name="rules", type="String", required=true, description="权限列表 用,方式隔开")
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
            $params = $this->request->post("", [], 'strip_tags');
            $params['rules'] = explode(',', $params['rules']);
            if(!$params['pid']){

                $params['pid'] = 2; //默认父级ID位2

            }
            if (!in_array($params['pid'], $this->childrenGroupIds)) {
                $this->error(__('The parent group exceeds permission limit'));
            }
            $parentmodel = model("AuthGroup")->get($params['pid']);
            if (!$parentmodel) {
                $this->error(__('The parent group can not found'));
            }
            // 父级别的规则节点
            $parentrules = explode(',', $parentmodel->rules);
            // 当前组别的规则节点
            $currentrules = $this->auth->getRuleIds();
            $rules = $params['rules'];
            // 如果父组不是超级管理员则需要过滤规则节点,不能超过父组别的权限
            $rules = in_array('*', $parentrules) ? $rules : array_intersect($parentrules, $rules);
            // 如果当前组别不是超级管理员则需要过滤规则节点,不能超当前组别的权限
            $rules = in_array('*', $currentrules) ? $rules : array_intersect($currentrules, $rules);
            $params['rules'] = implode(',', $rules);
            if ($params) {
                $this->model->create($params);
                $this->success(__('Success'));
                exit();
            }
            $this->error();
            exit();
        }
        return $this->view->fetch();
    }

    /**
     * 角色管理
     *
     * @ApiTitle    (角色编辑)
     * @ApiSummary  (角色编辑 [GET时候获取角色详情])
     * @ApiSector   (角色管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/auth/group/edit)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="ids", type="int", required=true, description="角色ID")
     * @ApiParams   (name="name", type="String", required=true, description="角色名称")
     * @ApiParams   (name="rules", type="String", required=true, description="权限列表 用,方式隔开")
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
    public function edit($ids = null)
    {
        $ids = $this->request->param('ids');
        if (!in_array($ids, $this->childrenGroupIds)) {
            $this->error(__('You have no permission'));
        }
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
//            $this->token();
            $params = $this->request->post("", [], 'strip_tags');
            if($params['row']){

                $params = $params['row'];
            }
            unset($params['ids']);
            //父节点不能是非权限内节点
            if(!$params['pid']){

                $params['pid'] = 2;
            }
            if (!in_array($params['pid'], $this->childrenGroupIds)) {
                $this->error(__('The parent group exceeds permission limit'));
            }
            // 父节点不能是它自身的子节点或自己本身
            if (in_array($params['pid'], Tree::instance()->getChildrenIds($row->id, true))) {
                $this->error(__('The parent group can not be its own child or itself'));
            }
            $params['rules'] = explode(',', $params['rules']);

            $parentmodel = model("AuthGroup")->get($params['pid']);
            if (!$parentmodel) {
                $this->error(__('The parent group can not found'));
            }
            // 父级别的规则节点
            $parentrules = explode(',', $parentmodel->rules);
            // 当前组别的规则节点
            $currentrules = $this->auth->getRuleIds();
            $rules = $params['rules'];
            // 如果父组不是超级管理员则需要过滤规则节点,不能超过父组别的权限
            $rules = in_array('*', $parentrules) ? $rules : array_intersect($parentrules, $rules);
            // 如果当前组别不是超级管理员则需要过滤规则节点,不能超当前组别的权限
            $rules = in_array('*', $currentrules) ? $rules : array_intersect($currentrules, $rules);
            $params['rules'] = implode(',', $rules);
            if ($params) {
                Db::startTrans();
                try {
                    $row->save($params);
                    $children_auth_groups = model("AuthGroup")->all(['id' => ['in', implode(',', (Tree::instance()->getChildrenIds($row->id)))]]);
                    $childparams = [];
                    foreach ($children_auth_groups as $key => $children_auth_group) {
                        $childparams[$key]['id'] = $children_auth_group->id;
                        $childparams[$key]['rules'] = implode(',', array_intersect(explode(',', $children_auth_group->rules), $rules));
                    }
                    model("AuthGroup")->saveAll($childparams);
                    Db::commit();
                    $this->success(__('Success'));
                    exit();
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                    exit();
                }
            }
            $this->error();
            exit();
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
        $this->success(__('Success'),'',$row);
    }

    /**
     * 角色管理
     *
     * @ApiTitle    (角色删除)
     * @ApiSummary  (角色删除)
     * @ApiSector   (角色管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/auth/group/del)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="ids", type="int", required=true, description="角色ID")
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
    public function del($ids = "")
    {
        $ids = $this->request->param('ids');
        if($ids == 1 || $ids == 2){

            $this->error('无法删除');
            exit();
        }
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $ids = explode(',', $ids);
            $grouplist = $this->auth->getGroups();
            $group_ids = array_map(function ($group) {
                return $group['id'];
            }, $grouplist);
            // 移除掉当前管理员所在组别
            $ids = array_diff($ids, $group_ids);

            // 循环判断每一个组别是否可删除
            $grouplist = $this->model->where('id', 'in', $ids)->select();
            $groupaccessmodel = model('AuthGroupAccess');
            foreach ($grouplist as $k => $v) {
                // 当前组别下有管理员
                $groupone = $groupaccessmodel->get(['group_id' => $v['id']]);
                if ($groupone) {
                    $ids = array_diff($ids, [$v['id']]);
                    continue;
                }
                // 当前组别下有子组别
                $groupone = $this->model->get(['pid' => $v['id']]);
                if ($groupone) {
                    $ids = array_diff($ids, [$v['id']]);
                    continue;
                }
            }
            if (!$ids) {
                $this->error(__('You can not delete group that contain child group and administrators'));
            }
            $count = $this->model->where('id', 'in', $ids)->delete();
            if ($count) {
                $this->success(__('Operation completed'));
            }
        }
        $this->error(__('Operation failed'));
    }

    /**
     * @ApiInternal
     * 批量更新
     */
    public function multi($ids = "")
    {
        // 组别禁止批量操作
        $this->error();
    }


    /**
     * 角色管理
     *
     * @ApiTitle    (读取角色权限树)
     * @ApiSummary  (读取角色权限树)
     * @ApiSector   (角色管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/auth/group/roletree)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="pid", type="String", required=true, description="角色组父级ID 默认2")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "",
    "data": [
    {
    "id": 5,
    "parent": "#",
    "text": "权限管理",
    "type": "menu",
    "state": {
    "selected": false
    }
    }
    ],
    "url": "http://www.river.test/api.html",
    "wait": 3
    })
     */
    public function roletree()
    {
        $this->loadlang('auth/group');

        $model = model('AuthGroup');
        $id = $this->request->post("id");
        $pid = $this->request->post("pid");
        $parentGroupModel = $model->get($pid);
        $currentGroupModel = null;
        if ($id) {
            $currentGroupModel = $model->get($id);
        }
        if (($pid || $parentGroupModel) && (!$id || $currentGroupModel)) {
            $id = $id ? $id : null;
            $ruleList = collection(model('AuthRule')->order('weigh', 'desc')->order('id', 'asc')->select())->toArray();
            //读取父类角色所有节点列表
            $parentRuleList = [];
            if (in_array('*', explode(',', $parentGroupModel->rules))) {
                $parentRuleList = $ruleList;
            } else {
                $parentRuleIds = explode(',', $parentGroupModel->rules);
                foreach ($ruleList as $k => $v) {
                    if (in_array($v['id'], $parentRuleIds)) {
                        $parentRuleList[] = $v;
                    }
                }
            }

            $ruleTree = new Tree();
            $groupTree = new Tree();
            //当前所有正常规则列表
            $ruleTree->init($parentRuleList);
            //角色组列表
            $groupTree->init(collection(model('AuthGroup')->where('id', 'in', $this->childrenGroupIds)->select())->toArray());

            //读取当前角色下规则ID集合
            $adminRuleIds = $this->auth->getRuleIds();
            //是否是超级管理员
            $superadmin = $this->auth->isSuperAdmin();
            //当前拥有的规则ID集合
            $currentRuleIds = $id ? explode(',', $currentGroupModel->rules) : [];

            if (!$id || !in_array($pid, $this->childrenGroupIds) || !in_array($pid, $groupTree->getChildrenIds($id, true))) {
                $parentRuleList = $ruleTree->getTreeList($ruleTree->getTreeArray(0), 'name');
                $hasChildrens = [];
                foreach ($parentRuleList as $k => $v) {
                    if ($v['haschild']) {
                        $hasChildrens[] = $v['id'];
                    }
                }
                $parentRuleIds = array_map(function ($item) {
                    return $item['id'];
                }, $parentRuleList);
                $nodeList = [];
                foreach ($parentRuleList as $k => $v) {
                    if (!$superadmin && !in_array($v['id'], $adminRuleIds)) {
                        continue;
                    }
                    if ($v['pid'] && !in_array($v['pid'], $parentRuleIds)) {
                        continue;
                    }
                    $state = array('selected' => in_array($v['id'], $currentRuleIds) && !in_array($v['id'], $hasChildrens));
                    $nodeList[] = array('id' => $v['id'], 'parent' => $v['pid'] ? $v['pid'] : '#', 'text' => __($v['title']), 'type' => 'menu', 'state' => $state);
                }
                $this->success('', null, $nodeList);
            } else {
                $this->error(__('Can not change the parent to child'));
            }
        } else {
            $this->error(__('Group not found'));
        }
    }
}
