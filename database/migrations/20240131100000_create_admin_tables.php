<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAdminTables extends Migrator
{
    public function change()
    {
        // 1. 角色表
        $table = $this->table('ac_roles', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '角色表']);
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '角色名称'])
              ->addColumn('desc', 'string', ['limit' => 255, 'default' => '', 'comment' => '描述'])
              ->addColumn('rules', 'text', ['null' => true, 'comment' => '权限节点ID集合,逗号分隔'])
              ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '状态 1启用 0禁用'])
              ->addColumn('create_time', 'integer', ['default' => 0])
              ->addColumn('update_time', 'integer', ['default' => 0])
              ->create();

        // 2. 菜单权限规则表
        $table = $this->table('ac_rules', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '菜单规则表']);
        $table->addColumn('pid', 'integer', ['default' => 0, 'comment' => '父级ID'])
              ->addColumn('title', 'string', ['limit' => 50, 'comment' => '菜单标题'])
              ->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '规则名/路由'])
              ->addColumn('icon', 'string', ['limit' => 50, 'default' => 'fas fa-circle', 'comment' => '图标'])
              ->addColumn('type', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '类型 1菜单 2按钮/接口'])
              ->addColumn('sort', 'integer', ['default' => 50, 'comment' => '排序'])
              ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '状态'])
              ->addColumn('create_time', 'integer', ['default' => 0])
              ->addColumn('update_time', 'integer', ['default' => 0])
              ->create();
        
        // 3. 站点配置表
        $table = $this->table('ac_config', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '系统配置表']);
        $table->addColumn('group', 'string', ['limit' => 50, 'default' => 'basic', 'comment' => '配置分组'])
              ->addColumn('name', 'string', ['limit' => 50, 'comment' => '配置标识'])
              ->addColumn('title', 'string', ['limit' => 50, 'comment' => '配置标题'])
              ->addColumn('value', 'text', ['null' => true, 'comment' => '配置值'])
              ->addColumn('type', 'string', ['limit' => 20, 'default' => 'string', 'comment' => '输入框类型']) 
              ->addIndex(['name'], ['unique' => true])
              ->create();
              
        // 4. 更新users表，增加角色ID (使用原生SQL简单处理，或者用hasColumn检查)
        // 简单起见，我们假设users表已由上一个migration创建
        if ($this->hasTable('users')) {
             $table = $this->table('users');
             if (!$table->hasColumn('role_id')) {
                 $table->addColumn('role_id', 'integer', ['default' => 0, 'comment' => '角色ID', 'after' => 'password'])
                       ->addColumn('nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '昵称', 'after' => 'username'])
                       ->addColumn('avatar', 'string', ['limit' => 255, 'default' => '', 'comment' => '头像', 'after' => 'nickname'])
                       ->save();
             }
        }
        
        // --- 初始化基础数据 ---
        
        // 插入基础配置
        $configs = [
            ['group' => 'basic', 'name' => 'site_title', 'title' => '站点标题', 'value' => 'ThinkPHP8 Admin', 'type' => 'string'],
            ['group' => 'basic', 'name' => 'site_keyword', 'title' => 'SEO关键词', 'value' => 'ThinkPHP,Swoole,Admin', 'type' => 'string'],
            ['group' => 'basic', 'name' => 'site_desc', 'title' => '站点描述', 'value' => '基于ThinkPHP8开发的后台管理系统', 'type' => 'textarea'],
        ];
        $this->table('ac_config')->insert($configs)->saveData();
        
        // 插入基础菜单
        // 1. 首页
        // 2. 系统管理 (用户、角色、菜单、配置)
        $rules = [
            [
                'id' => 1, 'pid' => 0, 'title' => 'Dashboard', 'name' => 'admin/index', 'icon' => 'fas fa-tachometer-alt', 
                'type' => 1, 'sort' => 0, 'create_time' => time()
            ],
            [
                'id' => 2, 'pid' => 0, 'title' => '系统管理', 'name' => '#', 'icon' => 'fas fa-cogs', 
                'type' => 1, 'sort' => 99, 'create_time' => time()
            ],
        ];
        $this->table('ac_rules')->insert($rules)->saveData();
        
        // 插入系统管理的子菜单
        $subRules = [
            ['pid' => 2, 'title' => '菜单管理', 'name' => 'system/menu', 'icon' => 'fas fa-list', 'type' => 1],
            ['pid' => 2, 'title' => '角色管理', 'name' => 'system/role', 'icon' => 'fas fa-user-tag', 'type' => 1],
            ['pid' => 2, 'title' => '用户管理', 'name' => 'system/user', 'icon' => 'fas fa-users', 'type' => 1],
            ['pid' => 2, 'title' => '站点配置', 'name' => 'system/config', 'icon' => 'fas fa-sliders-h', 'type' => 1],
        ];
         $this->table('ac_rules')->insert($subRules)->saveData();
    }
}
