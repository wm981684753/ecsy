<?php /*a:2:{s:58:"D:\wwwroot\cesyx01.cn\application\index\view\my\index.html";i:1585330932;s:62:"D:\wwwroot\cesyx01.cn\application\index\view\public\floor.html";i:1585305335;}*/ ?>
<!DOCTYPE html><html><head><meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1"><title></title><link rel="stylesheet" href="/static/theme/css/style.css" /><link rel="stylesheet" type="text/css" href="http://cesyx01.cn/statics/css/hui.css"><script src="/static/plugs/jquery/jquery.min.js"></script><script src="/static/plugs/layui/layui.all.js"></script><script src="/static/plugs/require/require.js"></script><script src="/static/admin.js"></script><script src="/public/js/common.js"></script></head><style>	.aui-flex-three{padding: 1.0rem;margin-bottom: 0}

        *{font-size: 12px}
        .aui-palace-grid-text h2{font-size: 12px}
        .aui-take-button button{font-size: 12px;padding:5px 10px}
        .aui-palace-grid{width:25%}
	</style><body><!--头部--><div class="toubu"><div class="toubu_zuo"><span><a href="javascript:history.back(-1)"><img src="/static/theme/img/changyongicon-.png" /></a></span></div><div class="toubu_zhong"><span style="color:#ffffff">我的</span></div><div class="toubu_you"><span><img src="/static/theme/img/informatiom_.png"/></span></div><!--网名 个人中心--><div class="toubu_toux"><div class="grzx" ><img src="/static/theme/img/头像.png" /><div class="wangming"><span class="wm"><?php echo htmlentities($info['username']); ?></span><p class="dh"><?php echo htmlentities($info['tel']); ?></p></div></div><div class="grzx1"><a href="<?php echo url('ctrl/my_data'); ?>"><span style="color:#ffffff">个人中心></span></a></div></div></div><div class="zshy"><div class="zshyz"><img src="/static/theme/img/vip.png" /><span><?php echo htmlentities($level_name); ?></span></div></div><div class="wdyue"><div class="wdye"><span>我的余额</span></div><div class="yue"><ul><li><span><?php echo htmlentities($info['balance']); ?></span><p>余额(元)</p></li></ul></div><div class="djye"><ul><li><span><?php echo htmlentities($info['freeze_balance']); ?></span><p>冻结余额(元)</p></li></ul></div></div><div class="guangg"><span>我的团队躺着也收益，快邀请好友一起赚钱</span></div><div class="cygn"><p id="p1">常用功能</p><ul class="ul1"><li><a href="<?php echo url('ctrl/junior'); ?>"><img src="/static/theme/img/我的分享.png"/><p>我的团队</p></a></li><li><a href="<?php echo url('ctrl/wallet'); ?>"><img src="/static/theme/img/我的钱包.png"/><p>我的钱包</p></a></li><li><a href="<?php echo url('ctrl/my_data'); ?>"><img src="/static/theme/img/我的资料.png"/><p>个人资料</p></a></li><li><a href="<?php echo url('ctrl/recharge_admin'); ?>"><img src="/static/theme/img/管理.png"/><p>充值管理</p></a></li></ul><ul class="ul2"><li><a href="<?php echo url('ctrl/deposit_admin'); ?>"><img src="/static/theme/img/提现.png"><p>提现</p></a></li><li><a href="<?php echo url('my/invite'); ?>"><img src="/static/theme/img/团队佣金.png"/><p>邀请好友</p></a></li><li><a href="<?php echo url('ctrl/receive_site'); ?>"><img src="/static/theme/img/地址管理.png"/><p>地址管理</p></a></li><li><a href="<?php echo url('ctrl/set'); ?>"><img src="/static/theme/img/设置.png"/><p>设置</p></a></li><li><a href="javascript:void(0)" id="exit"><img src="/public/img/tc.png"/><p>退出</p></a></li></ul></div><style>
    @font-face {
        font-family: "iconfont";
        src: url('//at.alicdn.com/t/font_1463191_xdm42ti8gyp.eot?t=1571290693237');
        /* IE9 */
        src: url('//at.alicdn.com/t/font_1463191_xdm42ti8gyp.eot?t=1571290693237#iefix') format('embedded-opentype'), /* IE6-IE8 */ url('data:application/x-font-woff2;charset=utf-8;base64,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') format('woff2'), url('//at.alicdn.com/t/font_1463191_xdm42ti8gyp.woff?t=1571290693237') format('woff'), url('//at.alicdn.com/t/font_1463191_xdm42ti8gyp.ttf?t=1571290693237') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+ */ url('//at.alicdn.com/t/font_1463191_xdm42ti8gyp.svg?t=1571290693237#iconfont') format('svg');
        /* iOS 4.1- */
    }

    .iconfont {
        font-family: "iconfont" !important;
        font-size: 18px;
        font-style: normal;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    #footer-logo{
        text-align: center;
        text-align: -webkit-center;

    }

    .icon-dianji:before {
        content: "\e600";
    }

    .icon-shouye:before {
        content: "\e615";
    }

    .icon-tubiao-:before {
        content: "\e64f";
    }

    .icon-weibiaoti-:before {
        content: "\e614";
    }

    .icon-dingdan-yichenggong:before {
        content: "\e68d";
    }
    .hui-footer-text{padding-top:0}
    .floor-active div{color:#8c1bab}
</style><div id="hui-footer" style="height:50px;padding:2px 0;"><a href="<?php echo url('index/home'); ?>" id="nav-home" class=""><div class="hui-footer-icons hui-icons-home iconfont icon-shouye mui-active"></div><div class="hui-footer-text">首页</div></a><a href="<?php echo url('order/index'); ?>" id="nav-news"><div class="hui-footer-icons hui-icons-news iconfont icon-dingdan-yichenggong"></div><div class="hui-footer-text">记录</div></a><div style="width:20%; text-align:center;">
        &nbsp;
    </div><a href="<?php echo url('support/index'); ?>" id="nav-forum" ><div class="hui-footer-icons hui-icons-forum iconfont icon-tubiao-"></div><div class="hui-footer-text">客服</div></a><a href="<?php echo url('my/index'); ?>" id="nav-my"><div class="hui-footer-icons hui-icons-my iconfont icon-weibiaoti-"></div><div class="hui-footer-text">我的</div></a></div><a href="<?php echo url('rot_order/index',array('type'=>1)); ?>" id="footer-logo"><div id="footer-logo2" style="height: 54px;"><img src="http://cesyx01.cn/statics/img/qd.png" style="height: 40px;width:40%;margin-top:10px"></div></a><script src="/public/js/common.js"></script><script>
    $(".floor li").click(function(){
        $(this).addClass('floor-active').siblings().removeClass('floor-active')
    })
</script></body><script>    $(function() {
        $('#hui-footer a').eq(3).addClass("floor-active")
    })

    $("#exit").click(function(){
        layer.open({
            content: '确定退出登录吗？'
            , btn: ['确定','取消'],
            shadeClose: false
            , yes: function (index) {
                location.href="<?php echo url('user/logout'); ?>"
            },
            no:function(){
                layer.close();
            }
        });
    })
</script></html>