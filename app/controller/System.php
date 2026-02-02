<?php

namespace app\controller;

use think\facade\View;
use think\facade\Db;
use think\facade\Request;

class System extends BaseAdmin
{
    // --- 菜单管理 ---
    public function menu()
    {
        if (Request::isAjax()) {
            $list = Db::name('ac_rules')->order('sort', 'asc')->select();
            return json(['data' => $list]); // 简单返回，前端处理树形
        }
        return View::fetch('admin/system/menu');
    }

    public function menu_save()
    {
        $data = Request::param();
        if (empty($data['id'])) {
            $data['create_time'] = time();
            Db::name('ac_rules')->insert($data);
        } else {
            $data['update_time'] = time();
            Db::name('ac_rules')->update($data);
        }
        return json(['code' => 1, 'msg' => 'Success']);
    }

    public function menu_del()
    {
        $id = Request::param('id');
        Db::name('ac_rules')->delete($id);
        return json(['code' => 1, 'msg' => 'Deleted']);
    }

    // --- 角色管理 ---
    public function role()
    {
        if (Request::isAjax()) {
            $list = Db::name('ac_roles')->select();
            return json(['data' => $list]);
        }
        return View::fetch('admin/system/role');
    }

    public function role_save()
    {
        $data = Request::param();
        // 如果是规则ID数组，转为字符串
        if (isset($data['rules']) && is_array($data['rules'])) {
            $data['rules'] = implode(',', $data['rules']);
        }
        
        if (empty($data['id'])) {
            $data['create_time'] = time();
            Db::name('ac_roles')->insert($data);
        } else {
            $data['update_time'] = time();
            Db::name('ac_roles')->update($data);
        }
        return json(['code' => 1, 'msg' => 'Success']);
    }

    // --- 用户管理 --- 
    public function user()
    {
        if (Request::isAjax()) {
            $list = Db::name('users')
                ->alias('u')
                ->leftJoin('ac_roles r', 'u.role_id = r.id')
                ->field('u.*, r.name as role_name')
                ->select();
            return json(['data' => $list]);
        }
        $roles = Db::name('ac_roles')->select();
        View::assign('roles', $roles);
        return View::fetch('admin/system/user');
    }

    public function user_save()
    {
        $data = Request::param();
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']); // 不修改密码
        }

        if (empty($data['id'])) {
            $data['create_time'] = time();
            Db::name('users')->insert($data);
        } else {
            $data['update_time'] = time();
            Db::name('users')->update($data);
        }
        return json(['code' => 1, 'msg' => 'Success']);
    }

    // --- 站点配置 ---
    public function config()
    {
        if (Request::isPost()) {
            $params = Request::param();
            foreach ($params as $name => $val) {
                Db::name('ac_config')->where('name', $name)->update(['value' => $val]);
            }
            return json(['code' => 1, 'msg' => 'Saved']);
        }
        
        $configs = Db::name('ac_config')->select();
        return View::fetch('admin/system/config', ['configs' => $configs]);
    }
}
