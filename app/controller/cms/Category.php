<?php

namespace app\controller\cms;

use app\controller\BaseAdmin;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;

class Category extends BaseAdmin
{
    /**
     * 栏目列表 (Tree)
     */
    public function index()
    {
        if (Request::isAjax()) {
            $list = Db::name('cms_category')
                ->order('sort', 'asc')
                ->order('id', 'asc')
                ->select();
            return json(['code' => 1, 'data' => $list]);
        }
        return View::fetch(root_path() . 'view/admin/cms/category/index.html');
    }

    /**
     * 保存 (Add/Edit)
     */
    public function save()
    {
        $data = Request::param();
        
        // 简单验证
        if (empty($data['name'])) {
            return json(['code' => 0, 'msg' => 'Name is required']);
        }

        if (empty($data['id'])) {
            // Add
            $data['create_time'] = time();
            $data['update_time'] = time();
            Db::name('cms_category')->insert($data);
        } else {
            // Edit
            $data['update_time'] = time();
            Db::name('cms_category')->update($data);
        }
        return json(['code' => 1, 'msg' => 'Success']);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $id = Request::param('id');
        // 检查是否有子栏目
        $hasChild = Db::name('cms_category')->where('pid', $id)->find();
        if ($hasChild) {
            return json(['code' => 0, 'msg' => 'Please delete sub-categories first']);
        }
        // 检查是否有文章
        $hasArticle = Db::name('cms_article')->where('cid', $id)->find();
        if ($hasArticle) {
            return json(['code' => 0, 'msg' => 'Please delete articles under this category first']);
        }

        Db::name('cms_category')->delete($id);
        return json(['code' => 1, 'msg' => 'Deleted']);
    }
}
