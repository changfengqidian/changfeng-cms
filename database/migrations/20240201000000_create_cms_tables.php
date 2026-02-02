<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateCmsTables extends Migrator
{
    public function change()
    {
        // 1. CMS 栏目表 (Category)
        $table = $this->table('cms_category', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => 'CMS栏目表']);
        $table->addColumn('pid', 'integer', ['default' => 0, 'comment' => '父级ID'])
              ->addColumn('name', 'string', ['limit' => 50, 'comment' => '栏目名称'])
              ->addColumn('alias', 'string', ['limit' => 50, 'default' => '', 'comment' => '别名/目录名(URL)'])
              ->addColumn('icon', 'string', ['limit' => 50, 'default' => '', 'comment' => '图标'])
              ->addColumn('image', 'string', ['limit' => 255, 'default' => '', 'comment' => '栏目图'])
              ->addColumn('desc', 'string', ['limit' => 255, 'default' => '', 'comment' => '描述'])
              ->addColumn('seo_title', 'string', ['limit' => 255, 'default' => '', 'comment' => 'SEO标题'])
              ->addColumn('seo_keywords', 'string', ['limit' => 255, 'default' => '', 'comment' => 'SEO关键词'])
              ->addColumn('seo_desc', 'string', ['limit' => 255, 'default' => '', 'comment' => 'SEO描述'])
              ->addColumn('sort', 'integer', ['default' => 50, 'comment' => '排序'])
              ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '1启用 0禁用'])
              ->addColumn('create_time', 'integer', ['default' => 0])
              ->addColumn('update_time', 'integer', ['default' => 0])
              ->addIndex(['alias'])
              ->create();

        // 2. CMS 文章主表 (Article)
        $table = $this->table('cms_article', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => 'CMS文章主表']);
        $table->addColumn('cid', 'integer', ['default' => 0, 'comment' => '栏目ID'])
              ->addColumn('title', 'string', ['limit' => 255, 'comment' => '标题'])
              ->addColumn('short_title', 'string', ['limit' => 100, 'default' => '', 'comment' => '副标题'])
              ->addColumn('thumb', 'string', ['limit' => 255, 'default' => '', 'comment' => '封面图'])
              ->addColumn('author', 'string', ['limit' => 50, 'default' => '', 'comment' => '作者'])
              ->addColumn('source', 'string', ['limit' => 255, 'default' => '', 'comment' => '来源'])
              ->addColumn('link_url', 'string', ['limit' => 255, 'default' => '', 'comment' => '外链URL(如有)'])
              ->addColumn('desc', 'string', ['limit' => 255, 'default' => '', 'comment' => '摘要'])
              ->addColumn('seo_title', 'string', ['limit' => 255, 'default' => '', 'comment' => 'SEO标题'])
              ->addColumn('seo_keywords', 'string', ['limit' => 255, 'default' => '', 'comment' => 'SEO关键词'])
              ->addColumn('seo_desc', 'string', ['limit' => 255, 'default' => '', 'comment' => 'SEO描述'])
              ->addColumn('views', 'integer', ['default' => 0, 'comment' => '浏览量'])
              ->addColumn('likes', 'integer', ['default' => 0, 'comment' => '点赞数'])
              ->addColumn('is_top', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否置顶'])
              ->addColumn('is_hot', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否热门'])
              ->addColumn('is_recommend', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否推荐'])
              ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '状态: 0草稿 1发布 -1删除'])
              ->addColumn('pub_time', 'integer', ['default' => 0, 'comment' => '发布时间'])
              ->addColumn('create_time', 'integer', ['default' => 0])
              ->addColumn('update_time', 'integer', ['default' => 0])
              ->addColumn('delete_time', 'integer', ['null' => true, 'comment' => '软删除时间'])
              ->addIndex(['cid'])
              ->addIndex(['pub_time'])
              ->create();

        // 3. CMS 文章内容副表 (Article Data) - 垂直分表，提高主表查询速度
        // 注意：这里手动指定id与主表一致，不使用自增
        $table = $this->table('cms_article_data', ['id' => false, 'primary_key' => 'id', 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => 'CMS文章内容表']);
        $table->addColumn('id', 'integer', ['null' => false, 'signed' => false, 'comment' => '对应主表ID'])
              ->addColumn('content', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG, 'comment' => '富文本内容'])
              ->addColumn('markdown', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG, 'null' => true, 'comment' => 'Markdown源码'])
              ->create();

        // 4. CMS 标签表 (Tag)
        $table = $this->table('cms_tag', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => 'TAG标签表']);
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '标签名'])
              ->addColumn('count', 'integer', ['default' => 0, 'comment' => '引用次数'])
              ->addColumn('create_time', 'integer', ['default' => 0])
              ->addIndex(['name'], ['unique' => true])
              ->create();

        // 5. 文章标签关联表 (Article Tag Map)
        $table = $this->table('cms_article_tag', ['id' => false, 'engine' => 'InnoDB', 'comment' => '文章标签关联表']);
        $table->addColumn('article_id', 'integer')
              ->addColumn('tag_id', 'integer')
              ->addIndex(['article_id', 'tag_id'], ['unique' => true])
              ->create();
              
        // --- 插入后台菜单规则 ---
        // 假设 PID=0, ID自动递增。这里我们大概估算一下ID，或者先查询PID=0的最大ID?
        // 为了省事，我们直接插入顶层菜单“内容管理”
        
        // 注意：生产环境应检查是否已存在
        $cmsMenu = [
            'pid' => 0, 'title' => '内容管理', 'name' => '#', 'icon' => 'fas fa-newspaper', 
            'type' => 1, 'sort' => 10, 'create_time' => time()
        ];
        
        // 这里只是演示，实际运行时需要获取插入后的ID作为子菜单的PID
        // Phinx Insert 无法直接返回ID，这里我们仅插入数据表结构，菜单建议在后台手动添加或者用Seeder
    }
}
