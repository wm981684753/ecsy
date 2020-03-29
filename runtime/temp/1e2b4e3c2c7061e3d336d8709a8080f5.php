<?php /*a:1:{s:63:"D:\wwwroot\cesyx01.cn\application\index\view\user\register.html";i:1585326627;}*/ ?>
<!DOCTYPE html><html><head><meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1"><title></title><link href="/statics/css/reg.css" rel="stylesheet" type="text/css"><script src="/static/plugs/jquery/jquery.min.js"></script><script src="/public/js/ui.js"></script><link rel="stylesheet" href="/public/css/ui.css"><link rel="stylesheet" href="/public/js/layer_mobile/need/layer.css"><script src="/public/js/layer_mobile/layer.js"></script><script src="/public/js/common.js"></script><link rel="stylesheet" type="text/css" href="/static/theme/css/style.css"/></head><body style="background-color: #FFFFFF;"><div class="zhuce"><div class="zhuece_tou"><a href="javascript:history.back(-1)"><img src="/static/theme/img/尖括号-左.png" style="padding-left: 10px;"/></a><span>注册</span></div></div><div class="zhuce_zhong"><div class="zhuce_zhongz"><ul><li class="li2"><img class="img2" src="/static/theme/img/shouji.png"/><input class="input" type="text" id="tel" placeholder="填写手机号" /></li><li class="li1"><img class="img2"  src="/static/theme/img/yanzhengma2.png"/><input  class="input" type="text" id="verify" placeholder="填写验证码" /><span class="hqyzm"><div class="aui-psd"><div style="background: none;border: none;" id="yzm" class="verify_btn" type="button" >获取验证码</div></div></span></li><li class="li1"><img class="img2" src="/static/theme/img/钥匙.png"/><input  class="input" type="password" id="pwd" placeholder="填写登录密码" /></li><li class="li1"><img class="img2" src="/static/theme/img/suo.png"/><input  class="input" type="password" id="deposit_pwd" placeholder="填写交易密码" /></li><li class="li1"><img class="img2" src="/static/theme/img/邀请码.png"/><input  class="input" type="text" id="invite" value="<?php echo htmlentities($invite_code); ?>" placeholder="邀请码" /></li></ul><div class="xuanzhong"><img src="/static/theme/img/xuanzhong.png"/><span >注册代表您同意<font color="#EF5F3A">用户协议</font></span></div></div></div><div class="denglu"><div class="dengluz" id="reg"><button class="but register_btn">确认注册</button></div><div class="xiaz"><span><a href="http://qd.zm297.cn">下载APP</a></span><span><a href="./login.html">去登录</a></span></div></div></body><script>    var t=60,clock=null;

    $(".register_btn").click(function() {
        var tel = $('#tel').val(),
            user_name = $('#tel').val(),
            pwd = $('#pwd').val(),
            deposit_pwd= $('#deposit_pwd').val()
        verify = $('#verify').val(),
            invite_code = $('#invite').val();
        if (tel == "") {
            QS_toast.show("请输入账号", true)
        }
        else if (pwd == "") {
            QS_toast.show("请输入密码", true)
        }
        else if(verify==""){
            QS_toast.show("请输入验证码", true)

        } else {
            $.ajax({
                url: "<?php echo url('do_register'); ?>",
                type: "POST",
                dataType: "JSON",
                data: { tel:tel, pwd:pwd,user_name:user_name,invite_code:invite_code,verify:verify,deposit_pwd:deposit_pwd },
                success: function(res) {
                    console.log(res)
                    if (res.code == 0) {
                        QS_toast.show(res.info, true)
                        var timer = setTimeout(function() {
                            location.href = "<?php echo url('user/login'); ?>"
                        }, 1800)
                    }else{
                        QS_toast.show(res.info, true)
                    }
                },
                error: function(err) { console.log(err) }
            })
        }

    })


    // 获取验证码
    $('.verify_btn').click(function () {
        var tel = $("#tel").val()
        if (clock) return;
        $.ajax({
            url: urlPost("send/sendsms"),
            type: "POST",
            dataType: "JSON",
            data: { tel:tel },
            success: function(res) {
                console.log(res)
                if (res.code == 0) {
                    QS_toast.show(res.info, true)
                    clock = setInterval(verify_time, 1000);
                }else{
                    QS_toast.show(res.info, true)
                }
            },
            error: function(err) { console.log(err) }
        })
    })


    function verify_time() {
        $(".verify_btn").text(`已发送(${t})`).css('color', '#777777');
        t--;
        if (t <= 0) {
            clearInterval(clock);
            clock = null;
            t = 60;
            $(".verify_btn").text('获取验证码').css('color', '#de5a57');;
        }
    }
</script></html>