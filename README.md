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

### supervisor安装

```
# 1、安装
#$ sudo su - #切换为root用户

yum install epel-release
yum install -y supervisor
systemctl enable supervisord # 开机自启动
systemctl start supervisord # 启动supervisord服务

systemctl status supervisord # 查看supervisord服务状态
ps -ef|grep supervisord # 查看是否存在supervisord进程

# 1.2、排错
#systemctl start supervisord启动时，如果报错pkg_resources.DistributionNotFound: The 'supervisor==3.4.0' distribution was not found and is required by the application

#修改 /usr/bin/supervisorctl /usr/bin/supervisord 2个文件 将第一行的 #!/usr/bin/python  改为 #!/usr/bin/python2.7
#因为supervisor只支持python2.7

# 1.3 配置conf
#创建supervisor所需目录
mkdir /etc/supervisord.d/

#创建supervisor配置文件
echo_supervisord_conf > /etc/supervisord.conf

#编辑supervisord.conf文件
vim /etc/supervisord.conf
#文件内容如下(直接展示完整的文件内容，建议直接复制粘贴，如果想知道详细改动，可逐行对比)

; Sample supervisor config file.
;
; For more information on the config file, please see:
; http://supervisord.org/configuration.html
;
; Notes:
;  - Shell expansion ("~" or "$HOME") is not supported.  Environment
;    variables can be expanded using this syntax: "%(ENV_HOME)s".
;  - Quotes around values are not supported, except in the case of
;    the environment= options as shown below.
;  - Comments must have a leading space: "a=b ;comment" not "a=b;comment".
;  - Command will be truncated if it looks like a config file comment, e.g.
;    "command=bash -c 'foo ; bar'" will truncate to "command=bash -c 'foo ".

[unix_http_server]
file=/var/run/supervisor.sock   ; the path to the socket file
;chmod=0700                 ; socket file mode (default 0700)
;chown=nobody:nogroup       ; socket file uid:gid owner
;username=user              ; default is no username (open server)
;password=123               ; default is no password (open server)

[inet_http_server]         ; inet (TCP) server disabled by default
port=127.0.0.1:9001        ; ip_address:port specifier, *:port for all iface
;username=user              ; default is no username (open server)
;password=123               ; default is no password (open server)

[supervisord]
logfile=/var/log/supervisord.log ; main log file; default $CWD/supervisord.log
logfile_maxbytes=50MB        ; max main logfile bytes b4 rotation; default 50MB
logfile_backups=10           ; # of main logfile backups; 0 means none, default 10
loglevel=info                ; log level; default info; others: debug,warn,trace
pidfile=/var/run/supervisord.pid ; supervisord pidfile; default supervisord.pid
nodaemon=false               ; start in foreground if true; default false
minfds=1024                  ; min. avail startup file descriptors; default 1024
minprocs=200                 ; min. avail process descriptors;default 200
;umask=022                   ; process file creation umask; default 022
;user=chrism                 ; default is current user, required if root
;identifier=supervisor       ; supervisord identifier, default is 'supervisor'
;directory=/tmp              ; default is not to cd during start
;nocleanup=true              ; don't clean up tempfiles at start; default false
;childlogdir=/tmp            ; 'AUTO' child log dir, default $TEMP
;environment=KEY="value"     ; key value pairs to add to environment
;strip_ansi=false            ; strip ansi escape codes in logs; def. false

; The rpcinterface:supervisor section must remain in the config file for
; RPC (supervisorctl/web interface) to work.  Additional interfaces may be
; added by defining them in separate [rpcinterface:x] sections.

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

; The supervisorctl section configures how supervisorctl will connect to
; supervisord.  configure it match the settings in either the unix_http_server
; or inet_http_server section.

[supervisorctl]
serverurl=unix:///var/run/supervisor.sock ; use a unix:// URL  for a unix socket
;serverurl=http://127.0.0.1:9001 ; use an http:// url to specify an inet socket
;username=chris              ; should be same as in [*_http_server] if set
;password=123                ; should be same as in [*_http_server] if set
;prompt=mysupervisor         ; cmd line prompt (default "supervisor")
;history_file=~/.sc_history  ; use readline history if available

; The sample program section below shows all possible program subsection values.
; Create one or more 'real' program: sections to be able to control them under
; supervisor.

;[program:theprogramname]
;command=/bin/cat              ; the program (relative uses PATH, can take args)
;process_name=%(program_name)s ; process_name expr (default %(program_name)s)
;numprocs=1                    ; number of processes copies to start (def 1)
;directory=/tmp                ; directory to cwd to before exec (def no cwd)
;umask=022                     ; umask for process (default None)
;priority=999                  ; the relative start priority (default 999)
;autostart=true                ; start at supervisord start (default: true)
;startsecs=1                   ; # of secs prog must stay up to be running (def. 1)
;startretries=3                ; max # of serial start failures when starting (default 3)
;autorestart=unexpected        ; when to restart if exited after running (def: unexpected)
;exitcodes=0,2                 ; 'expected' exit codes used with autorestart (default 0,2)
;stopsignal=QUIT               ; signal used to kill process (default TERM)
;stopwaitsecs=10               ; max num secs to wait b4 SIGKILL (default 10)
;stopasgroup=false             ; send stop signal to the UNIX process group (default false)
;killasgroup=false             ; SIGKILL the UNIX process group (def false)
;user=chrism                   ; setuid to this UNIX account to run the program
;redirect_stderr=true          ; redirect proc stderr to stdout (default false)
;stdout_logfile=/a/path        ; stdout log path, NONE for none; default AUTO
;stdout_logfile_maxbytes=1MB   ; max # logfile bytes b4 rotation (default 50MB)
;stdout_logfile_backups=10     ; # of stdout logfile backups (0 means none, default 10)
;stdout_capture_maxbytes=1MB   ; number of bytes in 'capturemode' (default 0)
;stdout_events_enabled=false   ; emit events on stdout writes (default false)
;stderr_logfile=/a/path        ; stderr log path, NONE for none; default AUTO
;stderr_logfile_maxbytes=1MB   ; max # logfile bytes b4 rotation (default 50MB)
;stderr_logfile_backups=10     ; # of stderr logfile backups (0 means none, default 10)
;stderr_capture_maxbytes=1MB   ; number of bytes in 'capturemode' (default 0)
;stderr_events_enabled=false   ; emit events on stderr writes (default false)
;environment=A="1",B="2"       ; process environment additions (def no adds)
;serverurl=AUTO                ; override serverurl computation (childutils)

; The sample eventlistener section below shows all possible eventlistener
; subsection values.  Create one or more 'real' eventlistener: sections to be
; able to handle event notifications sent by supervisord.

;[eventlistener:theeventlistenername]
;command=/bin/eventlistener    ; the program (relative uses PATH, can take args)
;process_name=%(program_name)s ; process_name expr (default %(program_name)s)
;numprocs=1                    ; number of processes copies to start (def 1)
;events=EVENT                  ; event notif. types to subscribe to (req'd)
;buffer_size=10                ; event buffer queue size (default 10)
;directory=/tmp                ; directory to cwd to before exec (def no cwd)
;umask=022                     ; umask for process (default None)
;priority=-1                   ; the relative start priority (default -1)
;autostart=true                ; start at supervisord start (default: true)
;startsecs=1                   ; # of secs prog must stay up to be running (def. 1)
;startretries=3                ; max # of serial start failures when starting (default 3)
;autorestart=unexpected        ; autorestart if exited after running (def: unexpected)
;exitcodes=0,2                 ; 'expected' exit codes used with autorestart (default 0,2)
;stopsignal=QUIT               ; signal used to kill process (default TERM)
;stopwaitsecs=10               ; max num secs to wait b4 SIGKILL (default 10)
;stopasgroup=false             ; send stop signal to the UNIX process group (default false)
;killasgroup=false             ; SIGKILL the UNIX process group (def false)
;user=chrism                   ; setuid to this UNIX account to run the program
;redirect_stderr=false         ; redirect_stderr=true is not allowed for eventlisteners
;stdout_logfile=/a/path        ; stdout log path, NONE for none; default AUTO
;stdout_logfile_maxbytes=1MB   ; max # logfile bytes b4 rotation (default 50MB)
;stdout_logfile_backups=10     ; # of stdout logfile backups (0 means none, default 10)
;stdout_events_enabled=false   ; emit events on stdout writes (default false)
;stderr_logfile=/a/path        ; stderr log path, NONE for none; default AUTO
;stderr_logfile_maxbytes=1MB   ; max # logfile bytes b4 rotation (default 50MB)
;stderr_logfile_backups=10     ; # of stderr logfile backups (0 means none, default 10)
;stderr_events_enabled=false   ; emit events on stderr writes (default false)
;environment=A="1",B="2"       ; process environment additions
;serverurl=AUTO                ; override serverurl computation (childutils)

; The sample group section below shows all possible group values.  Create one
; or more 'real' group: sections to create "heterogeneous" process groups.

;[group:thegroupname]
;programs=progname1,progname2  ; each refers to 'x' in [program:x] definitions
;priority=999                  ; the relative start priority (default 999)

; The [include] section can just contain the "files" setting.  This
; setting can list multiple files (separated by whitespace or
; newlines).  It can also contain wildcards.  The filenames are
; interpreted as relative to this file.  Included files *cannot*
; include files themselves.

[include]
files = /etc/supervisord.d/*.ini



#启动supervisor
supervisord -c /etc/supervisord.conf

#查看supervisor是否启动成功
ps -ef|grep supervisord
#root       932     1  0 May10 ?        00:00:09 /bin/python2.7 /bin/supervisord -c /etc/supervisord.conf
#root      7902  6814  0 10:29 pts/0    00:00:00 grep --color=auto supervisord


# 2、将supervisor配置为开机自启动服务

#编辑服务文件
vim /usr/lib/systemd/system/supervisord.service
#内容如下：

[Unit]
Description=Supervisor daemon

[Service]
Type=forking
PIDFile=/var/run/supervisord.pid
ExecStart=/bin/supervisord -c /etc/supervisord.conf
ExecStop=/bin/supervisorctl shutdown
ExecReload=/bin/supervisorctl reload
KillMode=process
Restart=on-failure
RestartSec=42s

[Install]
WantedBy=multi-user.target


#保存退出

#启动服务
systemctl enable supervisord

#成功之后，就可以使用如下命令管理supervisor服务了
# systemctl stop supervisord
# systemctl start supervisord
# systemctl status supervisord
# systemctl reload supervisord
# systemctl restart supervisord

#至此，安装supervisor和配置为supervisor服务的工作就完成了。

#补充：

 因为我们的supervisor使用的是root安装，所以，对于非root用户，如果在执行
 supervisord -c /etc/supervisord.conf
 supervisorctl


 #命令时，会遇到访问被拒（Permission denied）的问题。
 #在命令最前面加上sudo即可
 #2. 如果更改了/etc/supervisord.conf中的端口号，原来的简写命令

# supervisorctl
#就需要在后面指定supervsor配置文件位置，或指定supervisor服务运行的端口号
supervisorctl -c /etc/supervisord.conf
supervisorctl -s http://localhost:7001

#否则会报连接拒绝
# supervisorctl
#http://localhost:9001 refused connection
#supervisor>
```


### 配置supervisor
#### laravel 的队列任务进程
```

#Supervisor 的配置文件通常位于 /etc/supervisord.d/ 目录下 .ini结尾。
#在该目录中，你可以创建任意数量的配置文件，用来控制 supervisor 将如何监控你的进程。
#例如，创建一个 laravel-worker.ini 文件使之启动和监控一个 queue:work 进程：


#supervisor 比较适合监控业务应用，且只能监控前台程序，
#php fork方式实现的daemon不能用它监控，否则supervisor> status 会提示：BACKOFF  Exited too quickly (process log may have details)
```

### 中间件示例
```
#创建一个中间件
php artisan make:middleware CheckRepeat
```

### 控制器

```
#控制器并 不是强制要求继承基础类 。 但是， 如果控制器没有继承基础类，你将无法使用一些便捷的功能，比如 middleware, validate 和 dispatch 方法。

#单个行为控制器
#如果你想定义一个只处理单个行为的控制器，你可以在控制器中放置一个 __invoke 方法：
    <?php

    namespace App\Http\Controllers;

    use App\User;
    use App\Http\Controllers\Controller;

    class ShowProfile extends Controller
    {
        /**
         * 展示给定用户的资料.
         *
         * @param  int  $id
         * @return View
         */
        public function __invoke($id)
        {
            return view('user.profile', ['user' => User::findOrFail($id)]);
        }
    }

#也可以直接命令行：
php artisan make:controller ShowProfileController --invokable


#资源控制器
#Laravel 资源路由将典型的「CURD (增删改查)」路由分配给具有单行代码的控制器。 例如，你希望创建一个控制器来处理应用保存的 "照片" 的所有 HTTP 请求。使用 Artisan 命令 make:controller ， 我们可以快速创建这样一个控制器：

php artisan make:controller PhotoController --resource

#这个命令会生成一个控制器 app/Http/Controllers/PhotoController.php。 其中包括每个可用资源操作的方法。

#接下来，你可以给控制器注册一个资源路由：

Route::resource('photos', 'PhotoController');


#API 资源路由
当声明用于 APIs 的资源路由时，通常需要排除显示 HTML 模板的路由， 如 create 和 edit。 为了方便起见，你可以使用 apiResource 方法自动排除这两个路由：

Route::apiResource('photos', 'PhotoController');
#你可以通过传递一个数组给 apiResources 方法的方式来一次性注册多个 API 资源控制器：

Route::apiResources([
    'photos' => 'PhotoController',
    'posts' => 'PostController'
]);
#为了快速生成一个不包含 create 和 edit 方法的 API 资源控制器，可以在执行 make:controller 命令时加上 --api 选项：

php artisan make:controller API/PhotoController --api

#路由缓存#
#注意：基于闭包的路由无法被缓存。要使用路由缓存。你需要将任何闭包路由转换成控制器路由。

#如果你的应用只使用了基于控制器的路由，那么你应该利用路由缓存。使用路由缓存将极大地减少注册所有应用路由所需的时间。某些情况下，路由注册的速度甚至会快 100 倍。要生成路由缓存，只需要执行 route:cache ：

php artisan route:cache
#运行此命令之后，每个请求都将加载缓存的路由文件。记住，如果你添加了任何的新路由，则需要生成新的路由缓存。因此，你只应在项目部署期间运行 route:cache 命令。

#你可以使用 route:clear 命令来清除路由缓存：

php artisan route:clear


```


### 任务调度 （定时任务处理 队列任务 等）
```
#过去，你可能需要在服务器上为每一个调度任务去创建 Cron 条目。因为这些任务的调度不是通过代码控制的，你要查看或新增任务调度都需要通 SSH 远程登录到服务器上去操作，所以这种方式很快会让人变得痛苦不堪。
#Laravel 的命令行调度器允许你在 Laravel 中清晰明了地定义命令调度。在使用这个任务调度器时，你只需要在你的服务器上创建单个 Cron 入口。你的任务调度在 app/Console/Kernel.php 的 schedule 方法中进行定义。为了帮助你更好的入门，这个方法中有个简单的例子。
# 查看任务列表
php artisan schedule:list
# 执行
php artisan schedule:work

```

### 用户认证
```
#1.web端快速开始 安装 Laravel 的 laravel/jetstream 扩展包提供了一种快速方法，可以使用一些简单的命令来支持你进行身份验证所需的所有路由和视图：
composer require laravel/jetstream

#如下：二选一 安装
// 使用 Livewire 栈安装 Jetstream...
php artisan jetstream:install livewire

// 使用 Inertia 栈安装 Jetstream...
php artisan jetstream:install inertia

## 注意在 windows 下composer require laravel/jetstream 时会报错，缺少ext-pcntl 通过配置 还是推荐直接在linux下安装包

#2. php artisan migrate 新建数据表

#3.访问web页面，需要先运行前段界面 npm install && npm run dev
```

### JWT 实现 Laravel 认证（前后端分离项目必备）
```
# 通过 composer 安装 jwt
composer require tymon/jwt-auth
#注意 安装时报错提示缺少ext-pnctl
"config": {
        "preferred-install": "dist",
        "sort-packages": true,
        "optimize-autoloader": true,
        "platform": {
            "ext-pcntl": "7.2",
            "ext-posix": "7.2"
        }
    },



#添加服务提供商（Laravel 5.4 或更低版本）
#将服务提供者添加到配置文件中的 providers 阵列中 config/app.php，如下所示：
#config/app.php

'providers' => [
    ...
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
]


#发布配置
#运行以下命令以发布程序包配置文件：
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
#现在，您应该拥有一个 config/jwt.php 文件，该文件可让您配置此软件包的基础。
#注意：提示缺少ext-pnctl ，直接忽略平台检测
git checkout -- vendor/composer/platform_check.php

#生成 JWT 密钥
php artisan jwt:secret

#配置授权
#注意：仅在使用 Laravel 5.2 及更高版本时，此方法才有效。
#在 config/auth.php 文件内部，您需要进行一些更改，以配置 Laravel 使用 jwt 防护来增强您的应用程序身份验证。
#config/auth.php

'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

#.
#.
#.

'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],



#添加一些基本的身份验证路由
#让我们添加一些路由，routes/api.php 如下所示：
#routes/api.php

Route::group([ 'namespace' => 'Api' ], function($router) {
    $router->post('login', 'AuthController@store');
    $router->match([ 'patch', 'put' ], 'refresh', 'AuthController@update');
    $router->delete('logout', 'AuthController@destroy');
    $router->any('me', 'UserController@show');
});


#创建 AuthController
#我们可以通过手动或运行 artisan 命令来创建：
php artisan make:controller Api\\AuthController

#创建 AuthPresenter
#在 app 目录下新建 Presenters 文件夹，接着在 Presenters 文件夹下新建 AuthPresenter.php 文件。
app/Presenters/AuthPresenter.php


#创建 AuthRequest
#我们可以通过手动或运行 artisan 命令来创建：
php artisan make:request AuthRequest



#创建 UserController
#我们可以通过手动或运行 artisan 命令来创建
php artisan make:controller Api\\UserController

#更新 Controller
#app/Http/Controllers/Controller.php

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function authUser()
    {
        try {
            $user = Auth::userOrFail();
        } catch (UserNotDefinedException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }

        return $user;
    }
}

#好了，下面进入测试环节。

```

### 加密解密
```
#Laravel 的加密机制使用的是 OpenSSL 所提供的 AES-256 和 AES-128 加密。强烈建议你使用 Laravel 内建的加密工具，而不是用其它的加密算法。所有 Laravel 加密之后的结果都会使用消息认证码 (MAC) 签名，使其底层值不能在加密后再次修改。
#设置
#在使用 Laravel 的加密工具之前，你必须先设置 config/app.php 配置文件中的 key 选项。你应当使用 php artisan key:generate 命令来生成密钥，这条 Artisan 命令会使用 PHP 的安全随机字节生成器来构建密钥。如果这个 key 值没有被正确设置，则所有由 Laravel 加密的值都会是不安全的。

#加密一个值
#你可以使用 Crypt 门面提供的 encryptString 来加密一个值。所有加密的值都使用 OpenSSL 的 AES-256-CBC 来进行加密。此外，所有加密过的值都会使用消息认证码 (MAC) 来签名，以检测加密字符串是否被篡改过：
 <?php

 namespace App\Http\Controllers;

 use App\Http\Controllers\Controller;
 use App\Models\User;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Crypt;

 class UserController extends Controller
 {
     /**
      * Store a secret message for the user.
      *
      * @param  Request  $request
      * @param  int  $id
      * @return Response
      */
     public function storeSecret(Request $request, $id)
     {
         $user = User::findOrFail($id);

         $user->fill([
             'secret' => Crypt::encryptString($request->secret),
         ])->save();
     }
 }



#解密一个值
#你可以使用 Crypt 门面提供的 decryptString 来进行解密。如果该值不能被正确解密，例如 MAC 无效时，会抛出异常 Illuminate\Contracts\Encryption\DecryptException：

 use Illuminate\Contracts\Encryption\DecryptException;
 use Illuminate\Support\Facades\Crypt;

 try {
     $decrypted = Crypt::decryptString($encryptedValue);
 } catch (DecryptException $e) {
     //
 }

```

### 数据库
```
#Laravel 支持原生的 SQL 查询、流畅的查询构造器 和 Eloquent ORM 这些操作在各种数据库后台与数据库的交互变得非常简单。目前 Laravel 支持以下四种数据库：
#MySQL 5.6+ （版本策略）
#PostgreSQL 9.4+ （版本策略）
#SQLite 3.8.8+
#SQL Server 2017+ （版本策略）

#配置
#数据库的配置文件在 config/database.php 文件中，你可以在这个文件中定义所有的数据库连接配置，并指定默认的数据库连接。这个文件中提供了大部分 Laravel 能够支持的数据库配置示例。

#URLs 形式配置
#通常，数据库连接使用多个配置值，例如 host、database、username、password 等。这些配置值中的每一个都有其相应的环境变量。这意味着在生产服务器上配置数据库连接信息时，需要管理多个环境变量。
#一些托管数据库提供程序（如 Heroku）提供单个数据库「URL」，该 URL 在单个字符串中包含数据库的所有连接信息。示例数据库 URL 可能如下所示：
mysql://root:password@127.0.0.1/forge?charset=UTF-8

#这些 URLs 通常遵循标准模式约定：
driver://username:password@host:port/database?options
#为了方便起见，Laravel 支持这些 URLs，作为使用多个配置选项配置数据库的替代方法。如果存在 url（或相应的 DATABASE_URL 环境变量）配置选项，则将使用该选项提取数据库连接和凭证信息。

## 读写分离
#有时候你希望 SELECT 语句使用一个数据库连接，而 INSERT、UPDATE 和 DELETE 语句使用另一个数据库连接。在 Laravel 中，无论你是使用原生查询，查询构造器，还是 Eloquent ORM，都能轻松的实现。
# 为了弄明白读写分离是如何配置的，我们先来看个例子：
'mysql' => [
    'read' => [
        'host' => [
            '192.168.1.1',
            '196.168.1.2',
        ],
    ],
    'write' => [
        'host' => [
            '196.168.1.3',
        ],
    ],
    'sticky' => true,
    'driver' => 'mysql',
    'database' => 'database',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
],

#注意在以上的例子中，配置数组中增加了三个键，分别是 read、write 和 sticky。read 和 write 都包含一个键为 host 的数组。而 read 和 write 的其他数据库选项都在键为 mysql 的数组中。
#如果你想重写主数组中的配置，只需要修改 read 和 write 数组即可。所以，这个例子中：192.168.1.1 和 192.168.1.2 将作为 「读」 连接主机，而 192.168.1.3 将作为 「写」 连接主机。这两个连接会共享 mysql 数组的各项配置，如数据库的凭证（用户名 / 密码），前缀，字符编码等。
#sticky 选项
#sticky 是一个 可选值，它用于立即读取在当前请求周期内已写入数据库的记录。若 sticky 选项被启用，并且当前请求周期内执行过「写」操作，那么任何「读」操作都将使用「写」连接。这样可确保同一个请求周期内写入的数据可以被立即读取到，从而避免主从同步延迟导致数据不一致的问题。不过是否启用它，取决于应用程序的需求。

## 使用多数据库连接
#当使用多数据库连接时，你可以通过 DB Facade 门面的 connection 方法访问每一个连接。传递给 connection 方法的参数 name 应该是 config/database.php 配置文件中 connections 数组中的一个值：
$users = DB::connection('foo')->select(...);

# 你也可以使用一个连接实例上的 getPdo 方法访问底层的 PDO 实例：
$pdo = DB::connection()->getPdo();

## 执行原生 SQL 查询
#一旦配置好数据库连接后，便可以使用 DB facade 门面运行查询。DB facade 为每种类型的查询提供了相应的方法：select，update，insert，delete 和 statement。

#执行 Select 查询
#你可以使用 DB Facade 的 select 方法来运行基础的查询语句：
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * 显示应用程序中所有用户的列表
     *
     * @return Response
     */
    public function index()
    {
        $users = DB::select('select * from users where active = ?', [1]);

        return view('user.index', ['users' => $users]);
    }
}

#传递给 select 方法的第一个参数就是一个原生的 SQL 查询，而第二个参数则是需要绑定到查询中的参数值。通常，这些值用于约束 where 语句。参数绑定可以防止 SQL 注入。
#select 方法将始终返回一个 array 数组，数组中的每个结果都是一个 stdClass 对象，可以像下面这样访问结果中的数值：

 foreach ($users as $user) {
     echo $user->name;
 }

## 使用命名绑定
#除了使用 ? 表示参数绑定外，你还可以使用命名绑定的形式来执行一个查询：
$results = DB::select('select * from users where id = :id', ['id' => 1]);

#执行 Insert 语句
#你可以使用 DB Facade 的 insert 方法来执行 insert 语句。与 select 方法一样，该方法将原生 SQL 查询作为其第一个参数，并将绑定的数据作为第二个参数：
DB::insert('insert into users (id, name) values (?, ?)', [1, 'Dayle']);

#执行 Update 语句
#update 方法用于更新数据库中现有的记录。该方法返回该执行语句影响的行数
$affected = DB::update('update users set votes = 100 where name = ?', ['John']);

#执行 Delete 语句
#delete 方法用于从数据库中删除记录。与 update 方法一样，返回受该执行语句影响的行数：
$deleted = DB::delete('delete from users');

#执行普通语句
#有些数据库语句不会有任何返回值。对于这些语句，你可以使用 DB Facade 的 statement 方法来运行：
DB::statement('drop table users');

##运行未预处理的语句
#有时你可能希望在不绑定任何值的情况下运行语句。对于这些类型的操作，可以使用 DB Facade 的 unprepared 方法：
DB::unprepared('update users set votes = 100 where name = "Dries"');

##隐式提交
#在事务中使用 DB 外观的 statement 和 unprepared 方法时，必须小心避免导致 [隐式提交] 的语句 (https://dev.mysql.com/doc/refman/8.0/en/implicit-commit.html)。 这些语句将导致数据库引擎间接提交整个事务，从而使 Laravel 不知道数据库的事务级别。这种语句的一个例子是创建数据库表：
DB::unprepared('create table a (col varchar(1) null)');

##监听查询事件
#如果你想监控程序执行的每一个 SQL 查询，你可以使用 listen 方法。这个方法对于记录查询或调试非常有用。你可以在 服务提供器 中注册你的查询监听器：
<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 注册所有应用的服务
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * 引导所有应用的服务
     *
     * @return void
     */
    public function boot()
    {
        DB::listen(function ($query) {
            // $query->sql
            // $query->bindings
            // $query->time
        });
    }
}



## 数据库事务
#你可以使用 DB facade 的 transaction 方法在数据库事务中运行一组操作。如果事务的闭包 Closure 中出现一个异常，事务将会回滚。如果事务闭包 Closure 执行成功，事务将自动提交。一旦你使用了 transaction， 就不必担心手动回滚或提交的问题：
DB::transaction(function () {
    DB::table('users')->update(['votes' => 1]);

    DB::table('posts')->delete();
});

##处理死锁
#transaction 方法接受一个可选的第二个参数，该参数用来表示事务发生死锁时重复执行的次数。一旦定义的次数尝试完毕，就会抛出一个异常：
DB::transaction(function () {
    DB::table('users')->update(['votes' => 1]);

    DB::table('posts')->delete();
}, 5);

## 手动使用事务
#如果你想要手动开始一个事务，并且对回滚和提交能够完全控制，那么你可以使用 DB Facade 的 beginTransaction 方法：
DB::beginTransaction();

#你可以使用 rollBack 方法回滚事务：
DB::rollBack();

#最后，你可以使用 commit 方法提交事务：
DB::commit();
#技巧：DB facade 的事务方法同样适用于 查询构造器 和 Eloquent ORM。

#连接到数据库 CLI
#如果要连接到数据库的 CLI，可以使用 db Artisan 命令：
php artisan db

#如果需要，可以指定数据库连接名称以连接到不是默认连接的数据库连接：
php artisan db mysql

```