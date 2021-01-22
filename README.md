# 秒杀系统

此系统为`互联网软件开发技术与实践-2020秋`期末课程项目作业。

本系统分为两个部分：后台管理系统和商品抢购系统。



## 环境信息

本系统的测试环境如下：

- `CentOS 7.4.1708`

- `Apache 2.4.6`

- `PHP 5.4.16`
- `MySQL 5.7.32`

- `RabbitMQ 3.6.6`



## 后台管理系统

后台管理系统的代码在`backend`文件夹下，使用`ThinkPHP 5.0`,`XAadmin 2.2`和`layui 2.5.7`搭建完成。



### 部署方法

1. 将站点根目录配置到`backend/public/`文件夹下

2. 调整`backend/runtime/`文件夹的权限

   ```shell
   chmod -R 777 runtime
   ```


3. `PHP`安装相应的`mysql`和`Redis`模块扩展



### 配置

**ThinkPHP基础配置**

配置文件：`backend/application/config.php`

```php
// token 相关
'life_time'              => 60*60*24*7, # 此处为token的有效期，单位为秒
'key'                    => '',					# key, iss, aud为生成token的验证信息，自行设置即可
'iss'                    => '',
'aud'                    => '',
//MD5
'md5_salt'               => '',					# 该参数为md5操作时的盐，自行修改即可
// 上传图片保存路径
'img_path'               => '',
// redis
'redis_ip'               => '127.0.0.1',
'redis_port'             => 6379,
```



**ThinkPHP数据库配置**

配置文件：`backend/application/database.php`

```php
// 数据库名
'database'        => '', 						# 填写所用数据库名
// 用户名
'username'        => '',						# 数据库的用户名和密码
// 密码
'password'        => '',
// 数据库表前缀
'prefix'          => '',						# 在查表时，表名前会自动添加前缀
```





## 商品抢购系统

后台管理系统的代码在`market`文件夹下，主要使用`ThinkPHP 5.0`, `Vue.js v2.6.12`和`Element UI 2.15.0`搭建完成。

同时，还有用来处理抢购订单的`golang`代码，在`golang/`文件夹下，`send`中为生产者代码，`receive`中为消费者代码。

### 部署方法

1. 将站点根目录配置到`market/public/`文件夹下

2. 调整`market/runtime/`文件夹的权限

   ```shell
   chmod -R 777 runtime
   ```

3. 添加`go`的`RabbitMQ`和`MySQL`依赖

   ```shell
   go get github.com/streadway/amqp
   go get -u github.com/jinzhu/gorm
   ```

4. 在生产/消费订单的服务器上分别运行`send.go`和`receive.go`



### 配置

**ThinkPHP基础配置**

配置文件：`market/application/config.php`

```php
// token 相关
'life_time'              => 60*60*24*7, # 此处为token的有效期，单位为秒
'key'                    => '',					# key, iss, aud为生成token的验证信息，自行设置即可
'iss'                    => '',
'aud'                    => '',
//MD5
'md5_salt'               => '',					# 该参数为md5操作时的盐，自行修改即可
// redis
'redis_ip'               => '127.0.0.1',
'redis_port'             => 6379,
 // 商品图片
'img_path'               => '',					# 商品图片存放的根目录，末尾要加上反斜杠'/'
'deafult_img'            => '',					# 默认状态下的商品图片相对位置
 // 订单接收地址
 'order_url'             => '127.0.0.1:5000/order',				# 处理订单的服务器地址
```



**ThinkPHP数据库配置**

配置文件：`market/application/database.php`

```php
// 数据库名
'database'        => '', 						# 填写所用数据库名
// 用户名
'username'        => '',						# 数据库的用户名和密码
// 密码
'password'        => '',
// 数据库表前缀
'prefix'          => '',						# 在查表时，表名前会自动添加前缀
```



**go配置**

将代码`send.go`中(`line 29`)

```go
conn, err := amqp.Dial("amqp://username:password@ip:port/")
```

`username`, `password`, `ip`和`port`填写为自己RabbitMQ的信息



将代码`receive.go`中(`line 37`)

```go
dsn := "username:password@tcp(ip:port)/market?charset=utf8mb4&parseTime=True&loc=Local&timeout=20s"
```

`username:password@tcp(ip:port)`部分填写为自己`MySQL`的信息

`line 93`

```go
conn, err := amqp.Dial("amqp://username:password@ip:port/")
```

填写自己`RabbitMQ`的信息



## 数据库格式

数据库共有8个表，命名及数据格式如下



### market_admin

| 字段          | 类型         | 空   | 默认 | 链接到 | 注释                    |
| ------------- | ------------ | ---- | ---- | ------ | ----------------------- |
| id *(主键)*   | mediumint(6) | 否   |      |        |                         |
| username      | varchar(20)  | 否   |      |        |                         |
| password      | varchar(32)  | 否   |      |        |                         |
| encrypt       | varchar(6)   | 否   |      |        |                         |
| lastloginip   | int(10)      | 否   | 0    |        |                         |
| lastlogintime | int(10)      | 否   | 0    |        |                         |
| email         | varchar(40)  | 否   |      |        |                         |
| mobile        | varchar(11)  | 否   |      |        |                         |
| realname      | varchar(50)  | 否   |      |        |                         |
| openid        | varchar(255) | 否   |      |        |                         |
| status        | tinyint(1)   | 否   | 1    |        | 是否有效(2:无效,1:有效) |
| updatetime    | int(10)      | 否   | 0    |        |                         |

#### **索引**

| 键名     | 类型  | 唯一 | 紧凑 | 字段     | 基数 | 排序规则 | 空   | 注释 |
| -------- | ----- | ---- | ---- | -------- | ---- | -------- | ---- | ---- |
| PRIMARY  | BTREE | 是   | 否   | id       | 4    | A        | 否   |      |
| username | BTREE | 否   | 否   | username | 4    | A        | 否   |      |
| status   | BTREE | 否   | 否   | status   | 1    | A        | 否   |      |



### market_admin_group

| 字段        | 类型         | 空   | 默认   | 链接到 | 注释                                |
| ----------- | ------------ | ---- | ------ | ------ | ----------------------------------- |
| id *(主键)* | tinyint(3)   | 否   |        |        |                                     |
| name        | varchar(50)  | 否   |        |        |                                     |
| description | text         | 是   | *NULL* |        |                                     |
| rules       | varchar(500) | 是   |        |        | 用户组拥有的规则id，多个规则 , 隔开 |
| listorder   | smallint(5)  | 否   | 0      |        |                                     |
| updatetime  | int(11)      | 是   | *NULL* |        |                                     |

#### **索引**

| 键名      | 类型  | 唯一 | 紧凑 | 字段      | 基数 | 排序规则 | 空   | 注释 |
| --------- | ----- | ---- | ---- | --------- | ---- | -------- | ---- | ---- |
| PRIMARY   | BTREE | 是   | 否   | id        | 2    | A        | 否   |      |
| listorder | BTREE | 否   | 否   | listorder | 1    | A        | 否   |      |



### market_admin_group_access

| 字段     | 类型         | 空   | 默认 | 链接到 | 注释     |
| -------- | ------------ | ---- | ---- | ------ | -------- |
| uid      | int(10)      | 否   |      |        | 用户id   |
| group_id | mediumint(8) | 否   |      |        | 用户组id |

#### 索引

| 键名         | 类型  | 唯一 | 紧凑 | 字段     | 基数 | 排序规则 | 空   | 注释 |
| ------------ | ----- | ---- | ---- | -------- | ---- | -------- | ---- | ---- |
| uid_group_id | BTREE | 是   | 否   | uid      | 3    | A        | 否   |      |
| group_id     | 4     | A    | 否   |          |      |          |      |      |
| uid          | BTREE | 否   | 否   | uid      | 3    | A        | 否   |      |
| group_id     | BTREE | 否   | 否   | group_id | 3    | A        | 否   |      |



### market_admin_log

| 字段        | 类型         | 空   | 默认 | 链接到 | 注释 |
| ----------- | ------------ | ---- | ---- | ------ | ---- |
| id *(主键)* | int(10)      | 否   |      |        |      |
| m           | varchar(15)  | 否   |      |        |      |
| c           | varchar(20)  | 否   |      |        |      |
| a           | varchar(20)  | 否   |      |        |      |
| querystring | varchar(255) | 否   |      |        |      |
| userid      | mediumint(8) | 否   | 0    |        |      |
| username    | varchar(20)  | 否   |      |        |      |
| ip          | int(10)      | 否   |      |        |      |
| time        | int(11)      | 否   | 0    |        |      |

#### 索引

| 键名    | 类型  | 唯一 | 紧凑 | 字段 | 基数 | 排序规则 | 空   | 注释 |
| ------- | ----- | ---- | ---- | ---- | ---- | -------- | ---- | ---- |
| PRIMARY | BTREE | 是   | 否   | id   | 823  | A        | 否   |      |



### market_good

| 字段        | 类型         | 空   | 默认   | 链接到 | 注释                    |
| ----------- | ------------ | ---- | ------ | ------ | ----------------------- |
| id *(主键)* | int(11)      | 否   |        |        |                         |
| name        | varchar(50)  | 否   |        |        | 商品名称                |
| description | text         | 是   | *NULL* |        | 商品描述                |
| amount      | int(11)      | 否   | 0      |        | 商品数量                |
| sold        | int(11)      | 否   | 0      |        | 已售数量                |
| price       | float        | 否   |        |        | 价格                    |
| start_time  | int(11)      | 否   |        |        | 开始时间                |
| image       | varchar(200) | 是   | *NULL* |        | 图片地址                |
| state       | int(11)      | 否   | 2      |        | 状态（1可用， 2不可用） |
| recommand   | int(11)      | 否   | 1      |        | 推荐商品                |
| updatetime  | int(11)      | 否   | 0      |        |                         |
| skid        | varchar(50)  | 是   | *NULL* |        |                         |

#### 索引

| 键名    | 类型  | 唯一 | 紧凑 | 字段 | 基数 | 排序规则 | 空   | 注释 |
| ------- | ----- | ---- | ---- | ---- | ---- | -------- | ---- | ---- |
| PRIMARY | BTREE | 是   | 否   | id   | 2    | A        | 否   |      |



### market_menu

| 字段        | 类型         | 空   | 默认   | 链接到 | 注释                      |
| ----------- | ------------ | ---- | ------ | ------ | ------------------------- |
| id *(主键)* | smallint(6)  | 否   |        |        |                           |
| name        | char(40)     | 否   |        |        |                           |
| parentid    | smallint(6)  | 是   | 0      |        |                           |
| icon        | varchar(20)  | 否   |        |        |                           |
| c           | varchar(20)  | 是   | *NULL* |        |                           |
| a           | varchar(20)  | 是   | *NULL* |        |                           |
| data        | varchar(50)  | 否   |        |        |                           |
| tip         | varchar(255) | 否   |        |        | 提示                      |
| group       | varchar(50)  | 是   |        |        | 分组                      |
| listorder   | smallint(6)  | 否   | 999    |        |                           |
| display     | tinyint(1)   | 否   | 1      |        | 是否显示(1:显示,2:不显示) |
| updatetime  | int(11)      | 是   | *NULL* |        |                           |

#### 索引

| 键名      | 类型  | 唯一 | 紧凑 | 字段      | 基数 | 排序规则 | 空   | 注释 |
| --------- | ----- | ---- | ---- | --------- | ---- | -------- | ---- | ---- |
| PRIMARY   | BTREE | 是   | 否   | id        | 24   | A        | 否   |      |
| listorder | BTREE | 否   | 否   | listorder | 5    | A        | 否   |      |
| parentid  | BTREE | 否   | 否   | parentid  | 7    | A        | 是   |      |





### market_order

| 字段        | 类型    | 空   | 默认 | 链接到 | 注释          |
| ----------- | ------- | ---- | ---- | ------ | ------------- |
| id *(主键)* | int(11) | 否   |      |        |               |
| user_id     | int(11) | 否   |      |        | 用户id        |
| good_id     | int(11) | 否   |      |        | 商品id        |
| state       | int(11) | 否   |      |        | 1:成功 2:失败 |

#### 索引

| 键名    | 类型  | 唯一 | 紧凑 | 字段 | 基数 | 排序规则 | 空   | 注释 |
| ------- | ----- | ---- | ---- | ---- | ---- | -------- | ---- | ---- |
| PRIMARY | BTREE | 是   | 否   | id   | 1    | A        | 否   |      |



### market_user

| 字段          | 类型         | 空   | 默认 | 链接到 | 注释                    |
| ------------- | ------------ | ---- | ---- | ------ | ----------------------- |
| id *(主键)*   | mediumint(6) | 否   |      |        |                         |
| username      | varchar(20)  | 否   |      |        |                         |
| password      | varchar(32)  | 否   |      |        |                         |
| encrypt       | varchar(6)   | 否   |      |        |                         |
| lastloginip   | int(10)      | 否   | 0    |        |                         |
| lastlogintime | int(10)      | 否   | 0    |        |                         |
| email         | varchar(40)  | 否   |      |        |                         |
| mobile        | varchar(11)  | 否   |      |        |                         |
| realname      | varchar(50)  | 否   |      |        |                         |
| openid        | varchar(255) | 否   |      |        |                         |
| status        | tinyint(1)   | 否   | 1    |        | 是否有效(2:无效,1:有效) |
| updatetime    | int(10)      | 否   | 0    |        |                         |

#### 索引

| 键名     | 类型  | 唯一 | 紧凑 | 字段     | 基数 | 排序规则 | 空   | 注释 |
| -------- | ----- | ---- | ---- | -------- | ---- | -------- | ---- | ---- |
| PRIMARY  | BTREE | 是   | 否   | id       | 2    | A        | 否   |      |
| username | BTREE | 否   | 否   | username | 2    | A        | 否   |      |
| status   | BTREE | 否   | 否   | status   | 1    | A        | 否   |      |