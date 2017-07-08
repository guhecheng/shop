use shop;

/*用户表*/
DROP TABLE IF EXISTS user;
CREATE TABLE IF NOT EXISTS user (
  userid INT NOT NULL AUTO_INCREMENT COMMENT '用户id',
  openid varchar(50) not null default '' comment '用户openid',
  uname varchar(100) not null default '' comment '用户名',
  nickname varchar(100) not null default '' comment '真实姓名',
  avatar varchar(255) not null default 0 comment '用户图片',
  money int not null default 0 comment '账户余额',
  phone varchar(15) not null default '' comment '手机号码',
  email varchar(100) not null default '' comment '电子邮箱',
  sex tinyint not null default 0 comment '性别，0：未知，1:男，2:女',
  city varchar(100) not null default '' comment '城市',
  province varchar(100) not null default '' comment '省份',
  country varchar(100) not null default '' comment '国家',
  location varchar(255) not null default '' comment '详细地址',
  ip varchar(50) not null default '' comment 'ip地址',
  level tinyint not null default 0 comment '会员等级',
  score double not null default '0.0' comment '积分',
  card_no varchar(20) not null default '' comment '会员卡号',
  is_delete tinyint not null default 0 comment '状态, 0: 正常, 1:禁用',
  is_old tinyint not null default 0 comment '老会员, 0:否，1:是',
  create_time TIMESTAMP DEFAULT current_timestamp comment '创建时间',
  update_time datetime comment '更新时间',
  last_login_time datetime comment '最后登陆时间',
  primary key(userid)
) engine=innodb charset=utf8mb4;
insert into user(openid, uname, avatar, sex, money) values('oBm9hvzL3JRgqrjfzlIPpktM4EgQ', 'GG',
'http://wx.qlogo.cn/mmopen/ia0MXiakBq7O08cJPQmKzunVdjRdevbPHzYX4ZDdKkSqiclCf5AAQbCRY41rd7jqXN2Uib5k6o6a6uUQEdjA0DMSDeicpDX7OibA0J/0', 1, 0);
/*商品类型*/
drop table if exists goodstype;
create table if not exists goodstype (
  typeid int not null auto_increment comment '类型id',
  typename varchar(50) not null default '' comment '类型名称',
  typepid int not null default 0 comment '父id',
  is_delete tinyint not null default 0 comment '是否显示,0显示,1:否',
  sort int not null default 0 comment '排序',
  primary key(typeid)
) engine=innodb charset=utf8;

/*商品*/
drop table if exists goods;
create table if not exists goods (
  goodsid int not null auto_increment comment '商品id',
  goodsname varchar(100) not null default '' comment '商品名',
  goodsicon varchar(255) not null default '' comment '图标地址',
  goodspic varchar(255) not null default '' comment '图片列表地址',
  goodsdesc text comment '商品介绍',
  typeid int not null default 0 comment '商品类型',
  price int not null default 0 comment '基本价格',
  is_hot tinyint default 0 comment '是否推荐,0:否，1:是',
  is_ad tinyint not null default 0 comment '是否广告位,0:否，1:是',
  is_sale tinyint default 0 comment '是否上架，0:否，1:是',
  is_delete tinyint default 0 comment '是否删除,0:否，1:是',
  is_discount tinyint default 0 comment '是否参与会员折扣, 0: 否， 1:是',
  discount tinyint not null default 0 comment '折扣价格',
  score_award tinyint default 0 comment '积分奖励倍数, 0:无, 1:一倍，2:2倍..',
  create_time timestamp default current_timestamp comment '创建时间',
  primary key(goodsid)
) engine=innodb charset=utf8;

/*商品属性值表*/
drop table if exists propertyvalue;
create table if not exists propertyvalue (
  value_id int not null auto_increment,
  value_name varchar(255) not null default '' comment '属性名',
  key_id int not null default 0 comment '属性名id',
  status tinyint default 0 comment '是否使用, 0:是, 1:否',
  is_delete tinyint default 0 comment '是否删除',
  create_time timestamp default current_timestamp comment '创建时间',
  primary key(value_id)
) engine=innodb charset=utf8;
/*商品属性名*/
drop table if exists propertykey;
create table if not exists propertykey (
  key_id int not null auto_increment,
  key_name varchar(255) not null default '' comment '属性名',
  type_id int not null default 0 comment '类型id',
  is_enum tinyint default 0 comment '是否参与选择',
  is_delete tinyint default 0 comment '是否删除',
  create_time timestamp default current_timestamp comment '创建时间',
  primary key(key_id)
) engine=innodb charset=utf8;

/*商品属性表*/
drop table if exists goodsproperty;
create table if not exists goodsproperty (
  property_id int not null auto_increment,
  goods_id int not null default 0 comment '商品id',
  key_id int not null default 0 comment '商品属性名id',
  value_id int not null default 0 comment '商品属性值id',
  is_sku tinyint not null default 0 comment '是否sku',
  sku_id int not null default 0 comment 'skuid',
  is_delete tinyint default 0 comment '是否删除',
  primary key(property_id)
) engine=innodb charset=utf8;

/*产品sku*/
drop table if exists goodssku;
create table if not exists goodssku (
  sku_id int not null auto_increment,
  goods_id int not null default 0 comment '商品id',
  num int not null default 0 comment '数量',
  price int not null default 0 comment '价格',
  primary key(sku_id)
) engine=innodb charset=utf8;

/*购物车*/
drop table if exists cart;
create table if not exists cart (
  cartid int not null auto_increment comment '购物车id',
  goodsid int not null default 0 comment '商品id',
  uid int not null default 0 comment '用户id',
  skuid int not null default 0 comment 'skuid',
  goodscount int not null default 0 comment '商品数量',
  oldprice decimal(10,2) default 0.00 comment '商品价格',
  create_time timestamp default current_timestamp comment '创建时间',
  is_delete tinyint not null default 0 comment '是否删除',
  primary key(cartid)
) engine=innodb charset=utf8;


/*流水记录*/
drop table if exists usertransmoney;
create table if not exists usertransmoney (
  trans_id int not null auto_increment,
  uid int not null default 0 comment '用户id',
  trans_type tinyint default 0 comment '用户交易类型,0:购物，1:充值, 2:其他',
  trans_money int not null default 0 comment '金额分',
  order_no varchar(100) not null default 0 comment '订单号',
  create_time timestamp default current_timestamp,
  primary key(trans_id)
) engine=innodb charset=utf8;

insert into usertransmoney(uid, trans_type, trans_money, order_no) VALUES
  (1, 0, 100, '001'), (1, 1, 200, '');


/*积分记录*/
drop table if exists scorechange;
create table if not exists scorechange (
  score_id int not null auto_increment,
  score int not null default 0 comment '积分',
  type tinyint not null default 0 comment '支付方式,0:其他，1 微信支付,2:会员卡支付',
  uid int not null default 0 comment '用户id',
  create_time timestamp default current_timestamp comment '创建时间',
  primary key(score_id)
) engine=innodb charset=utf8;


/*地址管理*/
drop table if exists useraddress;
create table if not exists useraddress (
  address_id  INT          NOT NULL AUTO_INCREMENT,
  uid int not null default 0 comment '用户id',
  address VARCHAR(255) NOT NULL DEFAULT ''
  COMMENT '地址',
  location varchar(255) not null default '' comment '详细地址',
  phone       VARCHAR(100) NOT NULL DEFAULT ''
  COMMENT '联系电话',
  is_default  TINYINT               DEFAULT 0
  COMMENT '是否默认, 0:否，1:是',
  name        VARCHAR(100) NOT NULL DEFAULT ''
  COMMENT '收货人',
  is_delete   TINYINT               DEFAULT 0
  COMMENT '是否删除，0:否，1:是',
  create_time timestamp default current_timestamp comment '创建时间',
  update_time timestamp comment '更新时间',
  PRIMARY KEY (address_id),
  INDEX uid(uid)
) engine=innodb charset=utf8;

/*管理员账号*/
drop table if exists adminuser;
create table if not exists adminuser (
  admin_id int not null auto_increment,
  name varchar(100) not null default '' comment '用户账号',
  password varchar(100) not null default '' comment '账号密码',
  create_time timestamp default current_timestamp comment '创建时间',
  uid int not null default 0 comment '对应微信号id',
  is_disabled tinyint default 0 comment '允许，0 禁用1 删除2',
  primary key(admin_id),
  unique key name(name)
) engine=innodb charset=utf8;

/*会员卡*/
drop table if exists card;
create table if not exists card (
  card_id int not null auto_increment,
  card_name varchar(100) not null default '' comment '卡片名',
  card_score int not null default 0 comment '卡片积分',
  card_level int not null default 0 comment '卡片等级',
  card_img varchar(255) not null default '' comment '图片地址',
  is_delete tinyint default 0 comment '是否删除',
  create_time timestamp default current_timestamp comment '创建时间',
  PRIMARY KEY (card_id)
) engine=innodb charset=utf8;

/*订单表*/
drop table if exists `order`;
create table if not exists `order` (
  order_id int not null auto_increment,
  order_no char(50) not null default '' comment '订单号',
  goodsid int not null default 0 comment '商品id',
  skuid int not null default 0 comment 'skuid',
  count int not null default 0 comment '购买数量',
  price int not null default 0 comment '商品价格',
  create_time timestamp default current_timestamp comment '创建时间',
  primary key(order_id)
) engine=innodb charset=utf8;


/*订单对应*/

DROP TABLE IF EXISTS `orderinfo`;

CREATE TABLE `orderinfo` (
  `info_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` char(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `uid` int(11) NOT NULL,
  `express_no` varchar(50) NOT NULL DEFAULT '' COMMENT '快递号',
  `express_company` varchar(50) NOT NULL DEFAULT '' COMMENT '快递公司',
  `price` int(11) NOT NULL DEFAULT '0' COMMENT '价格',
  `express_price` int(11) NOT NULL DEFAULT '0' COMMENT '快递价格',
  `discount` int(11) NOT NULL DEFAULT '0' COMMENT '折扣',
  `discount_price` int(11) NOT NULL DEFAULT '0' COMMENT '折扣价格',
  `recv_name` varchar(100) NOT NULL DEFAULT '' COMMENT '收货人',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `location` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `status` tinyint(4) DEFAULT '0' COMMENT '订单状态, 0: 创建，1:待支付，2:已支付,3:待发货，4:已发货，5:已收货',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `pay_time` datetime DEFAULT NULL COMMENT '支付时间',
  `send_time` datetime DEFAULT NULL COMMENT '发货时间',
  `pay_type` tinyint(4) DEFAULT '0' COMMENT '支付方式,0:微信，1:支付宝， 2:其他',
  `cancel_time` datetime DEFAULT NULL COMMENT '取消时间',
  PRIMARY KEY (`info_id`),
  UNIQUE KEY `order_no` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Log日志*/
drop table if exists log;
create table if not exists log (
  log_id int not null auto_increment,
  reqest_url varchar(255) not null default '',
  info varchar(255) not null default ''
) ENGINE=innodb charset=utf8;


/*权限列表*/
drop table if exists auth;
create table if not exists auth (
  auth_id int not null AUTO_INCREMENT,
  auth_name varchar(20) not null default '' comment '权限名称',
  auth_url varchar(50) not null default '' comment '权限列表对应url地址',
  is_show tinyint not null default 0  comment '是否展示在菜单项',
  auth_pid int not null default 0 comment '权限列表上级',
  PRIMARY KEY(auth_id)
) ENGINE=INNODB charset=utf8;

/*权限用户对应表*/
drop table if exists admin_auth;
CREATE TABLE if NOT EXISTS admin_auth (
  id int NOT NULL AUTO_INCREMENT,
  admin_id INT NOT NULL DEFAULT 0 comment '管理员id',
  auth_id int NOT NULL DEFAULT 0 COMMENT '对应权限列表id',
  PRIMARY KEY (id)
) engine=innodb charset=utf8;

/*用户信息表*/
drop table if exists olduser;
create table if not exists olduser (
  id int not null AUTO_INCREMENT,
  name varchar(100) not null default '' comment '用户名',
  phone varchar(20) not null default '' comment '电话',
  password varchar(50) not null default '' comment '用户密码',
  create_time TIMESTAMP DEFAULT current_timestamp COMMENT '创建时间',
  PRIMARY KEY (id)
) ENGINE=innodb charset=utf8;
/*用户孩子表*/
drop table if exists children;
create table if not exists children(
  relate_id int not null AUTO_INCREMENT,
  name varchar(20) not null default '' COMMENT '姓名',
  birth_date date comment '生日',
  sex tinyint not null default 0 comment '性别，0:未知，1:男,2:女',
  school varchar(100) not null default '' comment '学校',
  age tinyint not null default 0 comment '年龄',
  parent_id int not null default 0 comment '父辈id关联olduer表的id',
  uid int not null default 0 comment '用户表id',
  like_brands varchar(255) not null default '' comment '喜欢的品牌',
  relate tinyint DEFAULT 0 COMMENT '与用户关系,0:未知,1:儿,2:女，3:侄子',
  PRIMARY KEY (relate_id)
) ENGINE=innodb charset=utf8;



/*消息处理*/
drop table if exists message;
create table if not exists message (
  id int not null AUTO_INCREMENT,
  content varchar(255) not null default 0 COMMENT '内容',
  to_id int not null DEFAULT 0 comment '接收者,0:所有人',
  create_time TIMESTAMP DEFAULT current_timestamp  comment '创建时间',
  send_time TIMESTAMP comment '发送时间',
  is_send TINYINT default 0 comment '1已发送',
  is_delete TINYINT DEFAULT 0 comment '1已删除',
  PRIMARY KEY (id)
) ENGINE = innodb charset=utf8;