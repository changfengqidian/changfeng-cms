<?php

use think\migration\Migrator;
use think\facade\Db;

class SeedCmsMenu extends Migrator
{
    public function up()
    {
        // 1. 插入顶级菜单：内容管理
        // 先检查是否存在
        $exist = Db::name('ac_rules')->where('title', '内容管理')->find();
        
        if (!$exist) {
            $pid = Db::name('ac_rules')->insertGetId([
                'pid' => 0,
                'title' => '内容管理',
                'name' => '#', 
                'icon' => 'fas fa-newspaper',
                'type' => 1,
                'sort' => 10,
                'status' => 1,
                'create_time' => time(),
                'update_time' => time()
            ]);
        } else {
            $pid = $exist['id'];
        }

        // 2. 插入子菜单
        $subMenus = [
            [
                'pid' => $pid,
                'title' => '栏目管理',
                'name' => 'cms.category/index',
                'icon' => 'fas fa-sitemap',
                'type' => 1,
                'sort' => 1,
                'status' => 1,
            ],
            [
                'pid' => $pid,
                'title' => '文章管理',
                'name' => 'cms.article/index',
                'icon' => 'fas fa-file-alt',
                'type' => 1,
                'sort' => 2,
                'status' => 1,
            ],
            [
                'pid' => $pid,
                'title' => '标签管理',
                'name' => 'cms.tag/index',
                'icon' => 'fas fa-tags',
                'type' => 1,
                'sort' => 3,
                'status' => 1,
            ],
            [
                'pid' => $pid,
                'title' => '回收站',
                'name' => 'cms.article/recycle',
                'icon' => 'fas fa-trash-alt',
                'type' => 1,
                'sort' => 99,
                'status' => 1,
            ]
        ];

        foreach ($subMenus as $menu) {
            // 避免重复插入
            $subExist = Db::name('ac_rules')->where('name', $menu['name'])->find();
            if (!$subExist) {
                $menu['create_time'] = time();
                $menu['update_time'] = time();
                Db::name('ac_rules')->insert($menu);
            }
        }
    }

    public function down()
    {
        // 简单的回滚逻辑：删除内容管理及其子菜单
        $parent = Db::name('ac_rules')->where('title', '内容管理')->find();
        if ($parent) {
            Db::name('ac_rules')->where('pid', $parent['id'])->delete();
            Db::name('ac_rules')->delete($parent['id']);
        }
    }
}
