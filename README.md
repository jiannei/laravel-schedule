# laravel-schedule

> ⏱ More flexible way to manage your Laravel schedule tasks. - 更灵活地管理 Laravel 应用中的任务调度。

![StyleCI](https://github.styleci.io/repos/377056763/shield?style=flat&branch=main)
[![Latest Stable Version](http://poser.pugx.org/jiannei/laravel-schedule/v)](https://packagist.org/packages/jiannei/laravel-schedule)
[![Total Downloads](http://poser.pugx.org/jiannei/laravel-schedule/downloads)](https://packagist.org/packages/jiannei/laravel-schedule)
[![Monthly Downloads](http://poser.pugx.org/jiannei/laravel-schedule/d/monthly)](https://packagist.org/packages/jiannei/laravel-schedule)
[![Version](http://poser.pugx.org/jiannei/laravel-schedule/version)](https://packagist.org/packages/jiannei/laravel-schedule)
[![License](http://poser.pugx.org/jiannei/laravel-schedule/license)](https://packagist.org/packages/jiannei/laravel-schedule)

## 社区讨论文章

- [是时候使用 Lumen 8 + API Resource 开发项目了！](https://learnku.com/articles/45311)
- [教你更优雅地写 API 之「路由设计」](https://learnku.com/articles/45526)
- [教你更优雅地写 API 之「规范响应数据」](https://learnku.com/articles/52784)
- [教你更优雅地写 API 之「枚举使用」](https://learnku.com/articles/53015)
- [教你更优雅地写 API 之「记录日志」](https://learnku.com/articles/53669)
- [教你更优雅地写 API 之「灵活地任务调度」](https://learnku.com/articles/58403)

## 功能

- 通过 schedules 数据表来管理任务调度
- 支持开启关闭调度任务
- 支持通过数据表来更新任务执行间隔
- 支持 laravel 任务调度时的 timezone、environments、withoutOverlapping、onOneServer、runInBackground、evenInMaintenanceMode、Output（任务输出到文件、邮件）配置

Tips: 使用该扩展只是多了一种可以基于数据表维护任务调度的方式，不会破坏原先 Laravel 定义任务调度的方式，两种方式甚至可以组合使用。

## 安装和配置

- 安装

```shell
$ composer require jiannei/laravel-schedule -vvv
```

- 发布服务

```shell
$ php artisan vendor:publish --provider="Jiannei\Schedule\Laravel\Providers\LaravelServiceProvider"
```

- 执行迁移

```shell
php artisan migrate
```

## 使用

为了能够更容易地集成到各种项目中，该扩展包并未直接提供一个 web 界面来维护 schedules 数据表，你可以按你的方式把 schedules 的配置维护进去就好。

举例说明项目中的使用方式：

- 维护应用程序中的 schedule 调度配置到 schedules 数据表

```sql
INSERT INTO `schedules` (`id`, `description`, `command`, `parameters`, `expression`, `active`, `timezone`, `environments`, `without_overlapping`, `on_one_server`, `in_background`, `in_maintenance_mode`, `output_file_path`, `output_append`, `output_email`, `output_email_on_failure`, `created_at`, `updated_at`) VALUES (1, '爬取 Github daily 趋势', 'github:trending', NULL, '*/10 * * * *', 1, 'Asia/Shanghai', NULL, 0, 0, 1, 1, 'github_trending.log', 1, NULL, 0, NULL, NULL);
INSERT INTO `schedules` (`id`, `description`, `command`, `parameters`, `expression`, `active`, `timezone`, `environments`, `without_overlapping`, `on_one_server`, `in_background`, `in_maintenance_mode`, `output_file_path`, `output_append`, `output_email`, `output_email_on_failure`, `created_at`, `updated_at`) VALUES (2, '爬取 Github weekly 趋势', 'github:trending', '--since=weekly', '59 23 * * *', 1, 'Asia/Shanghai', NULL, 0, 0, 1, 1, 'github_trending.log', 1, NULL, 0, NULL, NULL);
INSERT INTO `schedules` (`id`, `description`, `command`, `parameters`, `expression`, `active`, `timezone`, `environments`, `without_overlapping`, `on_one_server`, `in_background`, `in_maintenance_mode`, `output_file_path`, `output_append`, `output_email`, `output_email_on_failure`, `created_at`, `updated_at`) VALUES (3, '爬取 Github monthly 趋势', 'github:trending', '--since=monthly', '59 23 * * *', 1, 'Asia/Shanghai', NULL, 0, 0, 1, 1, 'github_trending.log', 1, NULL, 0, NULL, NULL);
```

> 说明：
> - command，应用程序中已经定义并且需要自动调度的 command
> - parameters，对应 command 执行时指定的参数
> - expression，执行时间间隔，cron 表达式
> - active，0 开启，1 关闭
> - output_file_path，指定 command 的输出的文件路径
> - output_append，command 输出到文件时是否进行追加
> - output_email，command 输出发送邮件
> - output_email_on_failure，只在 command 执行失败时发送输出到邮件
> - 其他参数均可以在 laravel schedule 文档中找到相应的函数说明：https://laravel.com/docs/8.x/scheduling#introduction

- 通过`php artisan schedule:list`检验是否配置成功

```shell
+------------------------------------------------------------------------+--------------+--------------------------+----------------------------+
| Command                                                                | Interval     | Description              | Next Due                   |
+------------------------------------------------------------------------+--------------+--------------------------+----------------------------+
| '/www/server/php/80/bin/php' 'artisan' github:trending                 | */10 * * * * | 爬取 Github daily 趋势   | 2021-06-22 21:40:00 +08:00 |
| '/www/server/php/80/bin/php' 'artisan' github:trending --since=weekly  | 59 23 * * *  | 爬取 Github weekly 趋势  | 2021-06-22 23:59:00 +08:00 |
| '/www/server/php/80/bin/php' 'artisan' github:trending --since=monthly | 59 23 * * *  | 爬取 Github monthly 趋势 | 2021-06-22 23:59:00 +08:00 |
+------------------------------------------------------------------------+--------------+--------------------------+----------------------------+
```

- 重启应用程序中的 `php artisan schedule:run`

以上，基于 schedules 数据表来管理调度任务就完成了。

## 协议

MIT 许可证（MIT）。有关更多信息，请参见[协议文件](LICENSE)。