<?php

namespace app\controller;

use think\facade\View;
use think\facade\Db;
use think\facade\App;

class Admin extends BaseAdmin
{
    public function index()
    {
        // 获取统计数据
        $stats = [
            'user_count' => Db::name('users')->count(),
            'role_count' => Db::name('ac_roles')->count(),
            'menu_count' => Db::name('ac_rules')->count(),
            'php_ver'    => PHP_VERSION,
            'tp_ver'     => App::version(),
            'swoole_ver' => extension_loaded('swoole') ? swoole_version() : 'N/A',
        ];

        View::assign('stats', $stats);
        return View::fetch();
    }
}
