<?php

namespace app\admin\controller;

use Aes\Aes;
use app\admin\model\AdminLog;
use app\common\controller\Backend;
use think\Config;
use think\exception\HttpResponseException;
use think\Hook;
use think\Request;
use think\Response;
use think\Validate;

/**
 * 登录
 * @internal
 */
class Index extends Backend
{

    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');

    }

    /**
     * 管理员登录
     *
     * @ApiTitle    (管理员登录)
     * @ApiSummary  (管理员登录)
     * @ApiSector   (登录)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/index/login)
     * @ApiParams   (name="username", type="String", required=true, description="用户名")
     * @ApiParams   (name="password", type="string", required=true, description="密码")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "登录成功!",
    "data": {
    "token": "+jBevpl5orfhjaUzo3KoYq0saVrNRoamb+x2+E1BdNQyFD4w1brBzm8/RLvoGseu3Yv9a5LIU1DEIz82q2wBdw==",
    "username": "admin",
    "avatar": "/assets/img/avatar.png"
    },
    "url": "",
    "wait": 3
    })
     */
    public function login()
    {
//        $url = $this->request->get('url', 'index/index');
//        if ($this->auth->isLogin()) {
//            $this->success(__("You've logged in, do not login again"), $url);
//        }
        if ($this->request->isPost()) {

            $username = $this->request->param('username');
            $password = $this->request->param('password');
            $keeplogin = $this->request->param('keeplogin');
//            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
//                '__token__' => 'require|token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
//                '__token__' => $token,
            ];
//            if (Config::get('fastadmin.login_captcha')) {
//                $rule['captcha'] = 'require|captcha';
//                $data['captcha'] = $this->request->post('captcha');
//            }
//            $validate = new Validate($rule, [], ['username' => __('Username'), 'password' => __('Password'), 'captcha' => __('Captcha')]);
            $validate = new Validate($rule, [], ['username' => __('Username'), 'password' => __('Password')]);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError());
            }
            AdminLog::setTitle(__('Login'));
            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true) {
                Hook::listen("admin_login_after", $this->request);
                $aes = new Aes(config('AesKey'));
                $token = $aes->encrypt(json_encode(['id' => $this->auth->id, 'username' => $username, 'over_time' => time() + 7 * 24 * 3600]));
//                $this->success(__('Login successful'), $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
                return $this->success(__('Login successful'),'',['token'=>$token,'username' => $username, 'avatar' => $this->auth->avatar]);

            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : __('Username or password is incorrect');
                $this->error($msg);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
//        if ($this->auth->autologin()) {
//            $this->redirect($url);
//        }
//        $background = Config::get('fastadmin.login_background');
//        $background = $background ? (stripos($background, 'http') === 0 ? $background : config('site.cdnurl') . $background) : '';
//        $this->view->assign('background', $background);
//        $this->view->assign('title', __('Login'));
//        Hook::listen("admin_login_init", $this->request);
//        return $this->view->fetch();
    }


    /**
     * 菜单列表
     *
     * @ApiTitle    (菜单列表)
     * @ApiSummary  (菜单列表)
     * @ApiSector   (菜单管理)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api.php/index/index)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    "code": 1,
    "msg": "登录成功!",
    "data": {
    "token": "+jBevpl5orfhjaUzo3KoYq0saVrNRoamb+x2+E1BdNQyFD4w1brBzm8/RLvoGseu3Yv9a5LIU1DEIz82q2wBdw==",
    "username": "admin",
    "avatar": "/assets/img/avatar.png"
    },
    "url": "",
    "wait": 3
    })
     */
    public function index()
    {
        //左侧菜单
        list($menulist, $navlist, $fixedmenu, $referermenu) = $this->auth->getSidebar([
            'dashboard' => 'hot',
            'addon'     => ['new', 'red', 'badge'],
            'auth/rule' => __('Menu'),
            'general'   => ['new', 'purple'],
        ], $this->view->site['fixedpage']);
        $this->success(lang('Success'),'',$menulist);
//        $action = $this->request->request('action');
//        if ($this->request->isPost()) {
//            if ($action == 'refreshmenu') {
//                $this->success('', null, ['menulist' => $menulist, 'navlist' => $navlist]);
//            }
//        }
//        $this->view->assign('menulist', $menulist);
//        $this->view->assign('navlist', $navlist);
//        $this->view->assign('fixedmenu', $fixedmenu);
//        $this->view->assign('referermenu', $referermenu);
//        $this->view->assign('title', __('Home'));
//        return $this->view->fetch();




    }


}
