<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateUsersTable extends Migrator
{
    /**
     * Change Method.
     */
    public function change()
    {
        // 1. 定义表结构
        $table = $this->table('users', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci']);
        
        // 2. 添加字段
        $table->addColumn('username', 'string', ['limit' => 50, 'null' => false, 'comment' => '用户名'])
              ->addColumn('password', 'string', ['limit' => 255, 'null' => false, 'comment' => '密码'])
              ->addColumn('create_time', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '更新时间'])
              ->addIndex(['username'], ['unique' => true])
              ->create(); // 先创建表

        // 3. 重新获取表对象用于插入数据 (或者复用 $table，但要小心 create 后状态)
        // 在 Phinx/Think-Migration 中，create() 调用后，$table 对象依然可用
        
        // 4. 插入默认管理员数据
        $defaultAdmin = [
            'username'    => 'admin',
            'password'    => password_hash('123456', PASSWORD_DEFAULT),
            'create_time' => time(),
            'update_time' => time(),
        ];
        
        // 使用 insert 方法插入数据
        $table->insert([$defaultAdmin]);
        $table->saveData(); // 必须调用 saveData 来提交数据插入
    }
}
