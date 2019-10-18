CREATE TABLE `user`
(
    `id`           int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '用户ID',
    `username`     varchar(32)         NOT NULL DEFAULT '' COMMENT '昵称',
    `avatar`       varchar(128)        NOT NULL DEFAULT '' COMMENT '用户头像',
    `sex`          tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
    `age`          tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '年龄',
    `place`        varchar(32)         NOT NULL DEFAULT '' COMMENT '居住地',
    `email`        varchar(128)        NOT NULL DEFAULT '' COMMENT '邮箱',
    `password`     char(40)            NOT NULL DEFAULT '' COMMENT '用户密码(sha1加密)',
    `signature`    varchar(128)        NOT NULL DEFAULT '' COMMENT '签名',
    `introduce`    varchar(5000)       NOT NULL DEFAULT '' COMMENT '个人介绍',
    `status`       tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：未验证邮箱，1：已验证邮箱，2：被封禁，3：已删除',
    `confirmation` varchar(32)         NOT NULL DEFAULT '' COMMENT '激活码',
    `created_at`   timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at`   timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '最后登录时间',
    PRIMARY KEY (`id`),
    KEY `username` (`username`)
) ENGINE = InnoDB COMMENT ='用户表';

CREATE TABLE `group_type`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '组类别ID',
    `name`       varchar(32)         NOT NULL DEFAULT '' COMMENT '组类别名称',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：使用中，1：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`name`)
) ENGINE = InnoDB COMMENT ='组类别表';

CREATE TABLE `group`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '组ID',
    `u_id`       int(10) unsigned    NOT NULL COMMENT '创建者ID',
    `gt_id`      int(10) unsigned    NOT NULL COMMENT '组类别ID',
    `name`       varchar(32)         NOT NULL DEFAULT '' COMMENT '组名称',
    `avatar`     varchar(128)        NOT NULL DEFAULT '' COMMENT '组头像',
    `introduce`  varchar(5000)       NOT NULL DEFAULT '' COMMENT '组介绍',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常，1：待审核，2：审核未通过，3：被封禁，:4：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`name`)
) ENGINE = InnoDB COMMENT ='组表';

CREATE TABLE `group_label`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '组标签ID',
    `g_id`       int(10) unsigned    NOT NULL COMMENT '组ID',
    `name`       varchar(32)         NOT NULL DEFAULT '' COMMENT '组标签名称',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常，1：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`name`)
) ENGINE = InnoDB COMMENT ='组标签表';

CREATE TABLE `group_friendship`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '友情组ID',
    `go_id`      int(10) unsigned    NOT NULL COMMENT '主人组ID',
    `gf_id`      int(10) unsigned    NOT NULL COMMENT '朋友组ID',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常，1：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`go_id`),
    KEY (`gf_id`)
) ENGINE = InnoDB COMMENT ='友情组表';

CREATE TABLE `inform`
(
    `id`           int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '通知ID',
    `code`         varchar(32)         NOT NULL DEFAULT '' COMMENT '唯一标识码',
    `uf_id`        int(10) unsigned    NOT NULL COMMENT '发起人ID',
    `ut_id`        int(10) unsigned    NOT NULL COMMENT '接收人ID',
    `relevance_id` int(10) unsigned    NOT NULL COMMENT '关联记录ID',
    `type`         tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '通知类型',
    `content`      varchar(2048)       NOT NULL DEFAULT '' COMMENT '通知内容',
    `is_dispose`   tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要处理 0：不需要 1：需要',
    `disposer_id`  int(10) unsigned    NOT NULL DEFAULT '0' COMMENT '处理者ID',
    `status`       tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：未读，1：已读，1：已同意，2：已拒绝，3：已删除',
    `created_at`   timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at`   timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY (`code`),
    KEY (`ut_id`)
) ENGINE = InnoDB COMMENT ='通知表';

CREATE TABLE `group_member`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '组成员ID',
    `u_id`       int(10) unsigned    NOT NULL COMMENT '成员ID',
    `g_id`       int(10) unsigned    NOT NULL COMMENT '组ID',
    `role`       tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '成员角色 0-成员 1-管理员 2-组长',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0-正常 1-已申请 2-已拒绝 3：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`u_id`),
    KEY (`g_id`)
) ENGINE = InnoDB COMMENT ='组成员表';

CREATE TABLE `group_ban`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '组违禁词ID',
    `g_id`       int(10) unsigned    NOT NULL COMMENT '组ID',
    `u_id`       int(10) unsigned    NOT NULL COMMENT '添加者ID',
    `word`       varchar(32)         NOT NULL DEFAULT '' COMMENT '违禁词',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0-正常 1-已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`g_id`)
) ENGINE = InnoDB COMMENT ='组违禁词表';

CREATE TABLE `group_log`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '组日志ID',
    `g_id`       int(10) unsigned    NOT NULL COMMENT '组ID',
    `u_id`       int(10) unsigned    NOT NULL COMMENT '操作者ID',
    `type`       tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '日志类型',
    `content`    varchar(2048)       NOT NULL DEFAULT '' COMMENT '日志内容',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0-正常 1-已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`g_id`),
    KEY (`type`)
) ENGINE = InnoDB COMMENT ='组日志表';

CREATE TABLE `message`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '用户消息ID',
    `uf_id`      int(10) unsigned    NOT NULL COMMENT '发起人ID',
    `ut_id`      int(10) unsigned    NOT NULL COMMENT '接收人ID',
    `content`    varchar(2048)       NOT NULL DEFAULT '' COMMENT '内容',
    `group_code` varchar(16)         NOT NULL DEFAULT '' COMMENT '分组码(md5(min(uf_id,ut_id), max(uf_id,ut_id)))',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：未读，1：已读，3：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`uf_id`),
    KEY (`ut_id`)
) ENGINE = InnoDB COMMENT ='用户消息表';

CREATE TABLE `topic`
(
    `id`          int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '主题ID',
    `u_id`        int(10) unsigned    NOT NULL COMMENT '作者ID',
    `g_id`        int(10) unsigned    NOT NULL COMMENT '小组ID',
    `title`       varchar(128)        NOT NULL DEFAULT '' COMMENT '标题',
    `content`     text(65535)         NOT NULL DEFAULT '' COMMENT '内容',
    `is_top`      tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常 1：置顶',
    `can_comment` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常 1：禁止回复',
    `status`      tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常 1：被封禁 2：已删除',
    `created_at`  timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at`  timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`u_id`),
    KEY (`g_id`)
) ENGINE = InnoDB COMMENT ='主题表';

CREATE TABLE `comment`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '评论ID',
    `u_id`       int(10) unsigned    NOT NULL COMMENT '评论者ID',
    `t_id`       int(10) unsigned    NOT NULL COMMENT '主题id',
    `parent_id`  int(10) unsigned    NOT NULL COMMENT '父级ID，评论则为主题id，回复则为评论id',
    `content`    varchar(5000)       NOT NULL DEFAULT '' COMMENT '内容',
    `image`      varchar(128)        NOT NULL DEFAULT '' COMMENT '图片路径',
    `type`       tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1：评论 2：回复',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常 1：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`u_id`),
    KEY (`parent_id`)
) ENGINE = InnoDB COMMENT ='评论表';


CREATE TABLE `like`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '点赞ID',
    `u_id`       int(10) unsigned    NOT NULL COMMENT '点赞者ID',
    `t_id`       int(10) unsigned    NOT NULL COMMENT '主题id',
    `target_id`  int(10) unsigned    NOT NULL COMMENT '目标ID，评论则为主题id，回复则为评论id',
    `type`       tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1：评论 2：回复',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常 1：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`u_id`),
    KEY (`target_id`)
) ENGINE = InnoDB COMMENT ='点赞表';


CREATE TABLE `collect`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '收藏ID',
    `u_id`       int(10) unsigned    NOT NULL COMMENT '收藏者ID',
    `t_id`       int(10) unsigned    NOT NULL COMMENT '主题id',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常 1：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY (`u_id`)
) ENGINE = InnoDB COMMENT ='收藏表';


CREATE TABLE `report`
(
    `id`         int(10) unsigned    NOT NULL AUTO_INCREMENT COMMENT '举报ID',
    `u_id`       int(10) unsigned    NOT NULL COMMENT '举报者ID',
    `target_id`  int(10) unsigned    NOT NULL COMMENT '目标id',
    `type`       tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '目标类型：0：用户 1：小组 2：讨论 3：评论',
    `content`    varchar(2048)       NOT NULL DEFAULT '' COMMENT '内容',
    `status`     tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：正常 1：已删除',
    `created_at` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp           NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT ='举报表';