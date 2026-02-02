<?php

namespace app\controller\cms;

use app\controller\BaseAdmin;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;

class Tag extends BaseAdmin
{
    public function index()
    {
        if (Request::isAjax()) {
            $page = Request::param('page', 1);
            $limit = Request::param('limit', 10);
            $name = Request::param('name');

            $where = [];
            if ($name) $where[] = ['name', 'like', "%{$name}%"];

            $count = Db::name('cms_tag')->where($where)->count();
            $list = Db::name('cms_tag')
                ->where($where)
                ->page($page, $limit)
                ->order('id', 'desc')
                ->select();

            return json(['code' => 0, 'count' => $count, 'data' => $list]);
        }
        return View::fetch(root_path() . 'view/admin/cms/tag/index.html');
    }

    public function save()
    {
        $data = Request::param();
        if (empty($data['name'])) return json(['code' => 0, 'msg' => 'Name required']);

        if (empty($data['id'])) {
            $data['create_time'] = time();
            try {
                Db::name('cms_tag')->insert($data);
            } catch (\Exception $e) {
                return json(['code' => 0, 'msg' => 'Tag already exists']);
            }
        } else {
            Db::name('cms_tag')->update($data);
        }
        return json(['code' => 1, 'msg' => 'Success']);
    }

    public function delete()
    {
        $id = Request::param('id');
        Db::name('cms_tag')->delete($id);
        // Remove relations
        Db::name('cms_article_tag')->where('tag_id', $id)->delete();
        return json(['code' => 1, 'msg' => 'Deleted']);
    }
}
