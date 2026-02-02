# 如何将长风CMS发布到 GitHub

为了将本项目成功开源并推送到 GitHub，请按照以下步骤操作。

## 1. 准备工作
确保您已经在本地安装并配置好了 Git，并拥有一个 GitHub 账号。

## 2. 在 GitHub 上创建仓库
1. 登录 [GitHub](https://github.com/)。
2. 点击页面右上角的 **+** 号，选择 **New repository**。
3. 填写仓库名称（建议：`changfeng-cms`）。
4. 选择 **Public**（公共仓库）。
5. **不要**勾选 "Initialize this repository with a README", 因为我们本地已经有了。
6. 点击 **Create repository**。

## 3. 在本地提交代码
在项目根目录下打开终端，运行以下命令：

```bash
# 初始化 Git (如果还没初始化)
git init

# 添加所有文件到暂存区
git add .

# 提交更改
git commit -m "Initialize Changfeng CMS: TPM8 + Swoole high performance CMS"
```

## 4. 关联 GitHub 并推送
将 `<your-username>` 替换为您自己的 GitHub 用户名：

```bash
# 添加远程仓库
git remote add origin https://github.com/<your-username>/changfeng-cms.git

# 重命名分支为 main
git branch -M main

# 推送到 GitHub
git push -u origin main
```

## 5. 后续维护
- **打标签 (Release)**: 当您发布稳定版时，可以使用 `git tag v1.0.0` 然后 `git push origin v1.0.0`。
- **开源精神**: 如果有人提交 Issue 或 Pull Request，请及时响应。
