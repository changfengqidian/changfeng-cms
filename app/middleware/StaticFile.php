<?php

namespace app\middleware;

use think\Response;

class StaticFile
{
    public function handle($request, \Closure $next)
    {
        $path = $request->pathinfo();

        // 只处理 /static 开头的请求
        if (strpos($path, 'static/') === 0) {
            // 防止目录遍历攻击
            $path = str_replace(['../', '..\\'], '', $path);

            // 构建完整的文件路径
            $filePath = public_path() . $path;

            // 检查文件是否存在
            if (file_exists($filePath) && is_file($filePath)) {
                // 获取文件扩展名
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);

                // 获取 Content-Type
                $contentType = $this->getContentType($extension);

                // 读取文件内容
                $content = file_get_contents($filePath);

                // 返回响应
                return Response::create($content, 'html', 200)
                    ->header([
                        'Content-Type' => $contentType,
                        'Cache-Control' => 'public, max-age=86400',
                    ]);
            }
        }

        return $next($request);
    }

    private function getContentType($extension)
    {
        $types = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'html' => 'text/html; charset=utf-8',
            'htm' => 'text/html; charset=utf-8',
            'ico' => 'image/x-icon',
        ];

        return $types[$extension] ?? 'application/octet-stream';
    }
}
