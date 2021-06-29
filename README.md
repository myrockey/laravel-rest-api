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



