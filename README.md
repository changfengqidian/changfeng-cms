# Changfeng CMS (长风CMS) - 高性能内容管理系统

![ThinkPHP](https://img.shields.io/badge/ThinkPHP-8.0-blue.svg) ![Swoole](https://img.shields.io/badge/Swoole-5.x-green.svg) ![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg) ![License](https://img.shields.io/badge/License-Apache%202.0-red.svg)

## 📖 项目简介
**Changfeng CMS (长风CMS)** 是一款基于 **ThinkPHP 8** 框架与 **Swoole** 高性能引擎打造的现代化内容管理系统。它不仅拥有极致的响应速度，还集成了成熟的 AdminLTE 后台与响应式 Bootstrap 5 前台，旨在为开发者提供一个**高并发、易扩展、开箱即用**的企业级 Web 开发基石。

## ✨ 核心亮点
*   **⚡ 极速响应**: 依托 Swoole 常驻内存机制，避免了传统 PHP-FPM 的重复加载开销，性能提升显著。
*   **📱 全端适配**: 经过精心设计的前台 UI，完美适配 PC、平板、手机等各种屏幕尺寸。
*   **🎨 优雅界面**: 后台采用 AdminLTE 3，前台采用 Bootstrap 5，视觉体验专业且现代。
*   **🛠️ 开箱即用**: 内置完善的文章管理、栏目分类、标签系统，以及一键数据填充功能。

---

## 🚀 快速上手 (Quick Start)

### 1. 环境准备
确保您的服务器满足以下要求：
*   PHP >= 8.0
*   Swoole 扩展 (推荐 5.x 版本)
*   MySQL >= 5.7
*   Composer

### 2. 获取代码并安装
```bash
# 克隆项目
git clone https://github.com/your-repo/tp8-swoole-cms.git
cd tp8-swoole-cms

# 安装依赖
composer install
```

### 3. 配置数据库
复制配置文件并修改：
```bash
cp .example.env .env
```
编辑 `.env` 文件，填入您的 MySQL 账号密码。

### 4. 启动服务
#### 方式 A: 使用 Docker (推荐)
如果您已经拉取了镜像，可以直接运行：
```bash
docker run -d -p 9551:9501 --name tp8-swoole-container tp8w-tp8-swoole:latest
```
此时访问地址为：`http://127.0.0.1:9551/`

#### 方式 B: 直接运行
```bash
# 启动服务 (默认端口 9501)
php think swoole
```

### 5. 🧪 一键填充测试数据 (Seeding)
为了让您快速预览网站效果，我们提供了一键数据填充功能。
**操作步骤：**
1.  确保服务已启动。
2.  在浏览器访问：`http://127.0.0.1:9501/seed`
3.  系统将自动：
    *   创建 5 个热门分类（技术、生活、旅行等）。
    *   生成 20 篇带有随机封面和摘要的测试文章。
4.  出现 "Database Seeded Successfully" 即表示成功！
5.  现在访问首页 `http://127.0.0.1:9501/` 即可看到填充后的效果。

---

## 🖥️ 访问入口
*   **网站首页**: `http://127.0.0.1:9501/`
*   **后台管理**: `http://127.0.0.1:9501/login/index` (或点击前台右上角 "Admin Login")
    *   初始账号: `admin`
    *   初始密码: `123456`

---

## 💡 如何将本项目打造成顶级 CMS？(进阶指南)
如果您希望基于本项目开发一个成熟的商业级 CMS，以下是我们根据行业最佳实践给出的**架构演进建议**：

### 1. 🏗️ 架构层面
*   **模块化设计**: 将 CMS 核心（Core）、博客（Blog）、商城（Shop）拆分为独立的 Service Provider，实现真正的低耦合。
*   **插件系统 (Hook)**: 引入钩子机制，让开发者可以在不修改核心代码的情况下，通过插件扩展功能（如：“文章发布后自动推送到百度收录”）。
*   **API 优先**: 采用前后端分离的设计思想，完善 RESTful API，为未来开发 APP 或小程序打好基础。

### 2. 🛡️ 性能与安全
*   **多级缓存**: 引入 Redis 缓存文章详情和配置信息，减少数据库查询压力。
*   **静态化 (Static Generator)**: 对于访问量巨大的文章详情页，可以生成纯 HTML 静态文件，甚至直接上 CDN。
*   **RBAC 权限控制**: 实现基于角色的精细化权限管理，精确控制每个管理员能看到哪些菜单、能操作哪些按钮。

### 3. 📈 内容与运营 (SEO & DX)
*   **SEO 深度优化**: 支持自定义 URL 伪静态（Slug）、自动生成 SiteMap、TDK（Title/Description/Keywords）智能提取。
*   **Markdown & 富文本**: 引入更强大的编辑器（如 Editor.js 或 ByteMD），支持 Markdown 实时预览，提升写作体验。
*   **资源管理**: 集成阿里云 OSS / 腾讯云 COS 对象存储，实现图片、视频的云端分发。

### 4. 🤝 开发者生态
*   **文档自动化**: 使用 Swagger/Apifox 自动生成 API 文档。
*   **脚手架工具**: 提供 `php think make:module` 等指令，一键生成增删改查代码。

## 🗺️ 官方开发路线图 (Roadmap)
- [x] 基础文章管理 (CRUD)
- [x] 分类与标签系统
- [x] 响应式前台 (Bootstrap 5)
- [x] 数据库填充 (Seedeer)
- [ ] JWT 身份认证系统
- [ ] 插件钩子 (Hook) 机制
- [ ] 微信小程序接口适配
- [ ] 系统配置可视化面板

---

## 🤝 贡献与支持
如果您觉得这个项目对您有帮助，请给我们一个 Star ⭐️！
欢迎提交 Issue 和 PR 共同完善这个项目。

## 📄 许可证
本项目采用 [Apache 2.0](LICENSE.txt) 开源许可证。
