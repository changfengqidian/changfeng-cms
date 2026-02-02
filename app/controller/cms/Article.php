<?php

namespace app\controller\cms;

use app\controller\BaseAdmin;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;

class Article extends BaseAdmin
{
    /**
     * 文章列表
     */
    public function index()
    {
        if (Request::isAjax()) {
            $page = Request::param('page', 1);
            $limit = Request::param('limit', 10);
            $cid = Request::param('cid');
            $status = Request::param('status'); // 0,1,-1
            $keywords = Request::param('keywords');

            $where = [];
            if ($status !== null && $status !== '') $where[] = ['a.status', '=', $status];
            if ($cid) $where[] = ['a.cid', '=', $cid];
            if ($keywords) $where[] = ['a.title', 'like', "%{$keywords}%"];

            // 软删除逻辑：如果不传 status，默认显示非删除的
            if ($status === null) {
                $where[] = ['a.status', '<>', -1];
            }

            $count = Db::name('cms_article')->alias('a')->where($where)->count();
            $list = Db::name('cms_article')
                ->alias('a')
                ->leftJoin('cms_category c', 'a.cid = c.id')
                ->field('a.*, c.name as category_name')
                ->where($where)
                ->page($page, $limit)
                ->order('a.id', 'desc')
                ->select();

            return json([
                'code' => 0, 
                'count' => $count, 
                'data' => $list
            ]);
        }

        // 获取栏目树供筛选
        $cates = Db::name('cms_category')->field('id,name,pid')->select()->toArray();
        View::assign('cates', $this->buildTree($cates));
        
        return View::fetch(root_path() . 'view/admin/cms/article/index.html');
    }

    /**
     * 回收站 (菜单入口)
     * ...
     */
    public function recycle()
    {
        // 强制注入 status=-1 参数，复用 index 逻辑
        Request::withGet(array_merge(Request::get(), ['status' => -1]));
        return $this->index();
    }

    /**
     * 添加/编辑页面
     */
    public function edit()
    {
        $id = Request::param('id');
        $article = [];
        $content = '';
        
        if ($id) {
            $article = Db::name('cms_article')->find($id);
            // 获取内容
            $contentData = Db::name('cms_article_data')->where('id', $id)->find();
            $content = $contentData ? $contentData['content'] : '';
        }

        // 栏目树
        $cates = Db::name('cms_category')->order('sort', 'asc')->select()->toArray();
        $cateTree = $this->buildTree($cates);

        View::assign([
            'article' => $article,
            'content' => $content,
            'cates'   => $cateTree
        ]);

        return View::fetch(root_path() . 'view/admin/cms/article/edit.html');
    }

    /**
     * 保存逻辑 (核心)
     */
    public function save()
    {
        $data = Request::param();
        $content = $data['content'] ?? '';
        unset($data['content']); // 主表不存 content

        // 基础验证
        if (empty($data['title']) || empty($data['cid'])) {
            return json(['code' => 0, 'msg' => 'Title and Category are required']);
        }

        // 自动提取摘要 (如果为空)
        if (empty($data['desc'])) {
            $text = strip_tags($content);
            $data['desc'] = mb_substr($text, 0, 150);
        }
        
        // 自动提取图片作为封面 (如果为空)
        if (empty($data['thumb'])) {
            preg_match('/<img.*?src="(.*?)".*?>/is', $content, $matches);
           if (!empty($matches[1])) {
               $data['thumb'] = $matches[1];
           }
        }

        Db::startTrans();
        try {
            if (empty($data['id'])) {
                // Insert
                $data['create_time'] = time();
                $data['update_time'] = time();
                $data['author'] = session('admin_name'); // 默认作者
                
                $id = Db::name('cms_article')->insertGetId($data);
                
                // Save Content
                Db::name('cms_article_data')->insert([
                    'id' => $id,
                    'content' => $content
                ]);
            } else {
                // Update
                $id = $data['id'];
                $data['update_time'] = time();
                
                // 安全过滤：不能更新 create_time
                unset($data['create_time']);
                
                Db::name('cms_article')->update($data);
                
                // Update Content (使用 save 指令，如果不存在则 insert，存在则 update)
                // 但这里我们简单点，检查是否存在
                $exists = Db::name('cms_article_data')->where('id', $id)->find();
                if ($exists) {
                    Db::name('cms_article_data')->where('id', $id)->update(['content' => $content]);
                } else {
                    Db::name('cms_article_data')->insert(['id' => $id, 'content' => $content]);
                }
            }
            Db::commit();
            return json(['code' => 1, 'msg' => 'Success', 'url' => (string)url('cms.article/index')]); // Cast URL to string to fix potential object issues
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 软删除 / 恢复
     */
    public function set_status()
    {
        $id = Request::param('id');
        $status = Request::param('status'); // 1: publish, 0: draft, -1: trash
        
        $update = ['status' => $status, 'update_time' => time()];
        if ($status == -1) {
            $update['delete_time'] = time();
        } else {
            $update['delete_time'] = null; // Restore
        }
        
        Db::name('cms_article')->where('id', $id)->update($update);
        return json(['code' => 1, 'msg' => 'Status Updated']);
    }
}
