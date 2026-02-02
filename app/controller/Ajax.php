<?php

namespace app\controller;

use think\facade\Request;
use think\facade\Filesystem;

class Ajax extends BaseAdmin
{
    public function upload()
    {
        $file = Request::file('file');
        try {
            validate(['file' => 'filesize:10240|fileExt:jpg,jpeg,png,gif'])
                ->check(['file' => $file]);

            $savename = \think\facade\Filesystem::disk('public')->putFile('uploads', $file);
            if ($savename) {
                // Return format for WangEditor/Generic
                $url = '/storage/' . str_replace('\\', '/', $savename);
                return json([
                    'errno' => 0, 
                    'data' => [
                        ['url' => $url, 'alt' => '', 'href' => '']
                    ]
                ]);
            }
        } catch (\think\exception\ValidateException $e) {
            return json(['errno' => 1, 'message' => $e->getMessage()]);
        }
        
        return json(['errno' => 1, 'message' => 'Upload failed']);
    }
}
