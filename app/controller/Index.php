<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;

class Index extends BaseController
{
    protected function initialize()
    {
        // Global variables for layout
        $cates = Db::name('cms_category')->where('pid', 0)->order('sort', 'asc')->select();
        View::assign('cates', $cates);

        try {
            $tags = Db::name('cms_tag')->limit(10)->order('id', 'desc')->select();
            View::assign('tags', $tags);
        } catch (\Exception $e) {
            View::assign('tags', []);
        }
    }

    public function index()
    {
        $cid = Request::param('cid');
        
        $where = [['a.status', '=', 1]];
        if ($cid) {
            $where[] = ['a.cid', '=', $cid];
        }
        
        $tag = Request::param('tag');
        // Tag filtering logic if needed, complex join required usually
        
        $articles = Db::name('cms_article')
            ->alias('a')
            ->leftJoin('cms_category c', 'a.cid = c.id')
            ->field('a.*, c.name as category_name')
            ->where($where)
            ->order('a.create_time', 'desc')
            ->paginate(8); 
            
        $total = Db::name('cms_article')->where('status', 1)->count();
        
        $currentCateName = 'All Articles';
        if($cid){
            $cate = Db::name('cms_category')->find($cid);
            if($cate) $currentCateName = $cate['name'];
        }

        View::assign([
            'articles' => $articles,
            'total' => $total,
            'title' => $currentCateName . ' - Changfeng CMS'
        ]);

        return View::fetch('index/index');
    }

    public function read()
    {
        $id = Request::param('id');
        if(!$id) return redirect((string)url('index'));

        $article = Db::name('cms_article')
            ->alias('a')
            ->leftJoin('cms_category c', 'a.cid = c.id')
            ->field('a.*, c.name as category_name')
            ->where('a.id', $id)
            ->where('a.status', 1) // Only published
            ->find();

        if (!$article) {
            return 'Article not found or deleted.';
        }

        // Fetch content
        $contentData = Db::name('cms_article_data')->where('id', $id)->find();
        $article['content'] = $contentData ? $contentData['content'] : '';

        // Next article
        $next = Db::name('cms_article')
            ->where('id', '>', $id)
            ->where('status', 1)
            ->order('id', 'asc')
            ->find();

        View::assign([
            'article' => $article,
            'next_article' => $next,
            'title' => $article['title']
        ]);

        return View::fetch('index/read');
    }
}
