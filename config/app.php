<?php

return [
    // 应用调试模式
    'app_debug'                 => true,
    // 应用Trace调试
    'app_trace'                 => false,
    // 0按名称成对解析 1按顺序解析
    'url_param_type'            => 1,
    // 当前 ThinkAdmin 版本号
    'thinkadmin_ver'            => 'v5',

    'empty_controller'          => 'Error',

    'pwd_str'                   => '!qws6F!xffD2vx80?95jt',  //盐

    'pwd_error_num'             => 10,    //密码连续错误次数

    'allow_login_min'           => 5,     //密码连续错误达到次数后的冷却时间，分钟

    'default_filter'            => 'trim',

    'zhangjun_sms' => [
        'userid'        => '????',
        'account'       => '?????',
        'pwd'           => '????',
        'content'       => '【????】您的验证码为：',
        'min'           => 5,  //短信有效时间，分钟
    ],
    //短信宝
    'smsbao' => [
        'user' => 'a1999', //账号  无需md5
        'pass' => 'a1887415157', //密码
        'sign' => '云淘客', //签名
    ],


    //bi支付
    'bipay' => [
        'appKey' => 'a1999',          //bi支付 商户appkey
        'appSecret' => 'a1887415157', //密钥
    ],

    'version' => '20100106',  //版本号


    'verify'    => true,
    'mix_time'=>'5',                    //匹配订单最小延迟
    'max_time'=>'10',                   //匹配订单最大延迟
    'min_recharge'=>'100',              //最小充值金额
    'max_recharge'=>'5000',             //最大充值金额
    'deal_min_balance'=>'100',          //交易所需最小余额
    'deal_min_num'=>'10',               //匹配区间
    'deal_max_num'=>'60',               //匹配区间
    'deal_count'=>'50',                 //当日交易次数限制
    'deal_reward_count'=>'3',          //推荐新用户获得额外的交易次数
    'deal_timeout'=>'10',              //订单超时时间
	'order_freeze'=>'43200',
    'deal_feedze'=>'0',              //交易冻结时长
    'deal_error'=>'3',                  //允许违规操作次数
    'vip_1_commission'=>'0.003',          //交易佣金
    'min_deposit'=>'100',               //最低提现额度
    '1_reward'=>'0',                  //直推上级推荐奖励
    '2_reward'=>'0',                 //上两级推荐奖励
    '3_reward'=>'0',                 //上三级推荐奖励
    '1_d_reward'=>'0.0008',               //上级会员获得交易奖励
    '2_d_reward'=>'0.0004',               //上二级会员获得交易奖励
    '3_d_reward'=>'0.0002',               //上三级会员获得交易奖励
    '4_d_reward'=>'0',               //上四级会员获得交易奖励
    '5_d_reward'=>'0',                  //上五级会员获得交易奖励
    'master_cardnum'=>'622200000000000124',             //银行卡号
    'master_name'=>'王五麻子',                              //收款人
    'master_bank'=>'中国银行',                          //所属银行
    'master_bk_address'=>'5',         //银行地址
    'deal_zhuji_time'=>'1',         //远程主机分配时间
    'deal_shop_time'=>'1',          //等待商家响应时间
];
