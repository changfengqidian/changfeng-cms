<?php

namespace app\Controller;

use app\BaseController;
use think\Request;

class StaticFile  extends BaseController
{
    public function index(Request $request)
    {
        // 获取请求的路径 - 使用 pathinfo 方法
        $path = ltrim($request->pathinfo(), '/');

        // 防止目录遍历攻击
        $path = str_replace(['../', '..\\'], '', $path);

        // 静态资源根目录 - 使用相对路径而不是硬编码绝对路径
        $staticPath = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'static';

        // 构建完整的文件路径
        $filePath = $staticPath . DIRECTORY_SEPARATOR . $path;

        // 检查文件是否存在且在指定目录内
        $realStaticPath = realpath($staticPath);
        $realFilePath = realpath($filePath);

        if ($realFilePath === false || !str_starts_with($realFilePath, $realStaticPath)) {
            return response('', 404);
        }

        if (file_exists($realFilePath) && is_file($realFilePath)) {
            // 读取文件内容
            $fileContent = file_get_contents($realFilePath);

            // 设置正确的 Content-Type
            $contentType = $this->getContentType($realFilePath);

            // 返回文件内容 - 使用正确的 response 函数调用
            return response($fileContent, 200, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=3600'
            ], '');
        }

        // 如果文件不存在，返回404
        return response('', 404);
    }

    private function getContentType($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];

        return $types[$extension] ?? 'application/octet-stream';
    }
}
