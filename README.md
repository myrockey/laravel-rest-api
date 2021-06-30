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
#Supervisor 的配置文件通常位于 /etc/supervisor/conf.d 目录下。
#在该目录中，你可以创建任意数量的配置文件，用来控制 supervisor 将如何监控你的进程。
#例如，创建一个 laravel-worker.conf 文件使之启动和监控一个 queue:work 进程：


```
