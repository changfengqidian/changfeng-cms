<?php
namespace app\controller;

use app\BaseController;
use think\facade\Db;

class Seed extends BaseController
{
    public function index()
    {
        // 1. Seed Categories
        $categories = ['Technology', 'Life', 'Travel', 'Food', 'Coding'];
        $catIds = [];
        
        foreach ($categories as $index => $name) {
            $exists = Db::name('cms_category')->where('name', $name)->find();
            if (!$exists) {
                $catIds[] = Db::name('cms_category')->insertGetId([
                    'name' => $name,
                    'sort' => $index,
                    'pid' => 0
                ]);
            } else {
                $catIds[] = $exists['id'];
            }
        }

        // 2. Seed Articles
        $titles = [
            'Getting Started with ThinkPHP 8',
            'Swoole for High Performance',
            'Understanding PHP 8.1 Attributes',
            '10 Tips for Better Coding',
            'Travel Guide to Japan',
            'Best Pizza in New York',
            'How to Build a CMS',
            'The Future of Web Development',
            'Why I Love Open Source',
            'Debugging Like a Pro',
            'Mastering SQL Queries',
            'Frontend vs Backend',
            'Responsive Design Principles',
            'Introduction to Docker',
            'Kubernetes basics',
            'AI in 2026',
            'Healthy Eating Habits',
            'Minimalist Lifestyle',
            'Remote Work Best Practices',
            'Learning a New Language'
        ];

        foreach ($titles as $index => $title) {
            $exists = Db::name('cms_article')->where('title', $title)->find();
            if (!$exists) {
                $cid = $catIds[array_rand($catIds)];
                $data = [
                    'cid' => $cid,
                    'title' => $title,
                    'desc' => "This is a summary for {$title}. It covers the basics and some advanced topics.",
                    'author' => 'Administrator',
                    'status' => 1,
                    'create_time' => time(),
                    'update_time' => time(),
                    'thumb' => 'https://placehold.co/600x400?text=' . urlencode($title)
                ];
                
                $id = Db::name('cms_article')->insertGetId($data);
                
                Db::name('cms_article_data')->insert([
                    'id' => $id,
                    'content' => "<p>Currently reading: <strong>{$title}</strong>.</p><p>Here is some dummy content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p><img src='https://placehold.co/800x600?text=Image+For+{$id}' alt='Detail Image' />"
                ]);
            }
        }

        return 'Database Seeded Successfully! <a href="/seed/clear">Clear Data</a>';
    }

    public function clear()
    {
        Db::name('cms_article')->where('1=1')->delete();
        Db::name('cms_article_data')->where('1=1')->delete();
        // Db::name('cms_category')->where('1=1')->delete(); // Maybe keep categories?
        return 'Articles Cleared! <a href="/seed">Seed Again</a>';
    }
}
