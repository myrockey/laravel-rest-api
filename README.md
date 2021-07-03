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