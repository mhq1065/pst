# pst
## 用途

收到vc和打印店病毒启发，花了两天写了这个简易下载站，可以在有网络/校园网络的地方部署使用

## 已完成功能

- 显示所有文件名称大小
- 文件上传（重命名，下载口令）
- 文件下载（下载口令）

## 未完成的功能

- 删除
- 后台管理员页面
- 页面美化（长期，如果我还记得的话）


## 技术栈

- laravel
- vue
- mysql

## 部署(开发环境)

- composer install
- php artisan key:generate
- php artisan migrate:run
- php artisan storage:link
- php artisan serve
- npm install
- npm run dev


## 部署

下次写