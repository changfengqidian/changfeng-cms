<?php

namespace app\controller;

use app\BaseController;
use think\facade\Session;
use think\facade\View;
use think\facade\Db;

class BaseAdmin extends BaseController
{
    protected $adminId;
    protected $adminInfo;
    protected $allowRules = []; // 用户拥有的权限ID集合

    protected function initialize()
    {
        // 1. 登录检测
        if (!Session::has('admin_id')) {
            if (request()->isAjax()) {
                exit(json_encode(['code' => 0, 'msg' => 'Please login first', 'url' => (string)url('login/index')]));
            }
            redirect((string)url('login/index'))->send();
            
        }

        $this->adminId = Session::get('admin_id');
        $this->adminInfo = Db::name('users')->find($this->adminId);

        // 2. 初始化权限
        $this->initAuth();

        // 3. 获取当前URI
        $controller = strtolower(request()->controller());
        $action = strtolower(request()->action());
        $currentUri = $controller . '/' . $action;

        // 4. 权限拦截 (超级管理员ID=1豁免)
        if ($this->adminId != 1) {
            $this->checkAuth($currentUri);
        }

        // 5. 获取菜单 (从数据库)
        $menus = $this->getMenus();
        
        // 6. 获取站点配置
        $siteConfig = Db::name('ac_config')->column('value', 'name');
        
        // 7. 共享视图变量
        View::assign([
            'admin_name'   => Session::get('admin_name'),
            'current_uri'  => $currentUri,
            'menus'        => $menus,
            'site_title'   => $siteConfig['site_title'] ?? 'Admin System',
            'site_config'  => $siteConfig
        ]);
    }

    /**
     * 初始化权限列表
     */
    protected function initAuth()
    {
        if ($this->adminId == 1) {
            $this->allowRules = '*'; // 超级管理员拥有所有权限
            return;
        }

        $roleId = $this->adminInfo['role_id'] ?? 0;
        if ($roleId) {
            $role = Db::name('ac_roles')->find($roleId);
            if ($role && $role['status'] == 1 && !empty($role['rules'])) {
                $this->allowRules = explode(',', $role['rules']);
            }
        }
    }

    /**
     * 权限检查
     */
    protected function checkAuth($uri)
    {
        // 忽略首页和部分通用接口
        $ignore = ['admin/index', 'admin/welcome'];
        if (in_array($uri, $ignore)) {
            return;
        }

        // 查找当前URI对应的规则ID
        // 注意：这里假设数据库 ac_rules 表中的 name 字段存储的是 "controller/action"
        $rule = Db::name('ac_rules')->where('name', $uri)->find();

        if (!$rule) {
            // 如果规则表中没有这个URL，默认策略：
            // 1. 允许访问 (宽松模式) -> 适合开发
            // 2. 禁止访问 (严格模式) -> 适合生产
            // 这里我们选择：如果该 URI 确实是某个节点记录过的，就必须校验；如果没有记录，暂且放行(或者你可以改为禁止)
            return; 
        }

        // 如果规则存在，必须检查是否有权限
        if (!in_array($rule['id'], $this->allowRules)) {
             if (request()->isAjax()) {
                return json_encode(['code' => 0, 'msg' => 'Permission Denied']);
            }
            return 'Permission Denied'; // 或者渲染一个漂亮的 403 页面
        }
    }

    /**
     * 获取菜单树 (仅返回有权限的)
     */
    protected function getMenus()
    {
        $query = Db::name('ac_rules')
            ->where('status', 1)
            ->where('type', 1) // 仅菜单
            ->order('sort', 'asc');
            
        // 如果不是超管，过滤权限
        if ($this->allowRules !== '*') {
            $query->where('id', 'in', $this->allowRules);
        }

        $rules = $query->select()->toArray();
            
        return $this->buildTree($rules);
    }

    /**
     * 构建树形结构
     */
    protected function buildTree($elements, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['pid'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
