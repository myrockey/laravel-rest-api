### 1、数据库迁移 migrate 使用
##### database/migrations目录下的文件
```
#1、新建test表的文件生成 配置参考代码
php artisan make:migration create_test

#2、更新test表的文件生成 配置参考代码
php artisan make:migration update_test

#3、还未执行的文件，执行迁移数据创建更新表到数据库（前提配置.env文件下的数据库信息）
php artisan migrate
# 执行指定文件
php artisan migrate --path=./database/migrations/2021_06_29_024957_update_test.php

#4、如果要回滚迁移操作 ()
php artisan migrate:rollback
#通过向 rollback 命令加上 step 参数，可以回滚指定数量的迁移。

#例如，以下命令将回滚最后五个迁移：
php artisan migrate:rollback --step=5

#5、其他命令
#创建migrations表到数据库
php artisan migrate:install

#查看迁移状态
php artisan migrate:status

#重置数据库所有迁移(会根据migrations表把所有数据库的数据表删除)
php artisan migrate:reset

# 使用单个命令同时进行回滚和迁移操作
#命令 migrate:refresh 首先会回滚已运行过的所有迁移，
#随后会执行 migrate。这一命令可以高效地重建你的整个数据库：
php artisan migrate:refresh

# 重置数据库，并运行所有的 seeds...（seed就是insert的模拟数据记录）
php artisan migrate:refresh --seed

#通过在命令 refresh 中使用 step 参数，你可以回滚并重新执行指定数量的迁移操作。
#例如，下列命令会回滚并重新执行最后五个迁移操作：
php artisan migrate:refresh --step=5

#删除所有表然后执行迁移 (慎用小心)
#命令 migrate:fresh 会删去数据库中的所有表，随后执行命令 migrate：
php artisan migrate:fresh
# 运行所有的 seeds...（seed就是insert的模拟数据记录）
php artisan migrate:fresh --seed
```

### 2、备份表结构
```
# 备份表结构
php artisan schema:dump

#  转储当前数据库架构并删除所有现有迁移。。。（会删除database/migrations目录）
php artisan schema:dump --prune
```
### 3、生成数据 数据填充
```
# 1、创建文件
php artisan make:seeder TestSeeder
# 2、编辑代码 在run方法里
#2.1 可以单条生成
// 1、单条插入 太慢
        DB::table('test')->insert([
            'name' => Str::random(10),
            'msg' => Str::random(10).' test',
            'status' => rand(0,1),
            'ext1' => rand(0,999),
            'ext2' => Str::random(10).' test',
        ]);
#2.2 通过模型工厂生成批量数据


#2.2.1 新建模型model
php artisan make:model Test
#2.2.2 新建模型工厂类
php artisan make:factory TestFactory
#2.2.3 在run方法里代码：
// 2、模型工厂创建数据 批量
        Test::factory()
            ->count(50)
            ->create();

# 3、执行文件 (所有) 调用的时 DatabaseSeeder下面包含的类
php artisan db:seed
# 3.1 执行指定文件
php artisan db:seed --class=TestSeeder

##您还可以使用 migrate:fresh 命令结合 --seed 选项，
#这将删除数据库中所有表并重新运行所有迁移。此命令对于完全重建数据库非常有用：
php artisan migrate:fresh --seed

#在生产环境中强制运行填充
#一些填充操作可能会导致原有数据的更新或丢失。为了保护生产环境数据库的数据，在 生产环境 中运行填充命令前会进行确认。
#可以添加 --force 选项来强制运行填充命令：
php artisan db:seed --force

```


### 队列 使用
#### 1、config/queue.php 下配置
```
# 要使用 database 队列驱动程序
php artisan queue:table

php artisan migrate

#要使用 redis 队列驱动程序，需要在 config/database.php 配置文件中配置一个 redis 数据库连接
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => '{default}',
    'retry_after' => 90,
],

```

#### 2、创建任务
```
#应用程序的所有的可排队任务都被存储在了 app/Jobs 目录中。
#如果 app/Jobs 目录不存在，当您运行 make:job Artisan 命令时，将会自动创建它。您可以使用 Artisan CLI 来生成一个新的队列任务：
php artisan make:job TestQueueJob

```
#### 3、任务中间件
```
#创建中间件 限制队列执行速度
php artisan make:middleware RateLimited
#修改代码

```
#### 4、任务处理
```
#执行默认连接和默认队列
php artisan queue:work


#执行指定连接和默认队列
php artisan queue:work redis


#执行默认连接和指定队列
php artisan queue:work --queue=processing


```
#### 5、任务事件
```
#任务失败事件
# 如果你想要注册一个将在任务失败时调用的事件，你可以使用 Queue::failing 方法。
#这是一个通过电子邮件或 Slack 通知你的团队的好机会。例如，我们可以在 Laravel 里的 AppServiceProvider 里附加一个回调到这个事件：

#任务执行前后事件
#使用 Queue facade 上的 before 和 after 方法，可以指定在处理排队任务之前或之后执行的回调。
#如果要为控制面板执行附加日志记录或增量统计，这些回调会是绝佳时机。通常，你应该从 服务提供者 调用这些方法。例如，我们可以使用 Laravel 的 AppServiceProvider：

#使用 Queue facade 上的 looping 方法，你可以指定在 worker 尝试从队列获取任务之前执行的回调。例如，你可以注册一个闭包来回滚以前失败的任务留下的任何事务：
```
#### 6、重试失败的任务
```
#重试失败的任务
#要查看所有插入到 failed_jobs 数据库表中的失败任务，可以使用 queue:failed Artisan 命令：
php artisan queue:failed

#如果需要，您可以传递多个 ID 或一个 ID 范围 (使用数字 ID 时) 到命令：
php artisan queue:retry 5 6 7 8 9 10
php artisan queue:retry --range=5-10

#要重试所有失败的任务，请执行 queue:retry 命令，并将 all 作为 ID 传递：
php artisan queue:retry all

#如果你想删除一个失败的任务，你可以使用 queue:forget 命令：
php artisan queue:forget 5

#要删除所有失败的任务，您可以使用 queue:flush 命令：
php artisan queue:flush

```

