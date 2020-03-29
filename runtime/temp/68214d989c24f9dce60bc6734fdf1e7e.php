<?php /*a:1:{s:61:"D:\wwwroot\cesyx01.cn\application\index\view\ctrl\junior.html";i:1578925446;}*/ ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge"><meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" /><title>直属下线</title><link rel="stylesheet" href="/public/css/style.css"><script src="/static/plugs/jquery/jquery.min.js"></script><script src="/public/js/ui.js"></script><link rel="stylesheet" href="/public/css/ui.css"><script src="/public/js/common.js"></script><style>
        nav {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            border-bottom: 1px solid #eeeeee;
        }

        nav>p {
            width: 33%;
            height: 2rem;
            line-height: 2rem;
            text-align: center;
        }

        .nav_active {
            color: #00bcd4;
            border-bottom: 1px solid #00bcd4;
        }

        .cont>div {
            display: nthree;
        }

        .list li {
            padding: .2rem .5rem;
            min-height: 2.7rem;
            height: auto;
            position: relative;
        }

        .head {
            width: 2rem;
            height: 2rem;
            background: #eeeeee;
            margin: auto .5rem auto 0;
        }

        .list li>div {
            display: flex;
            flex-direction: row;
            position: relative;
            justify-content: space-between;
        }

        .list li>div>div {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .last {
            margin-left: auto;
            text-align: right;
        }

        .user-tel,
        .user-times {
            font-size: .5rem;
            color: #777777;
        }

        .list li>div:nth-child(3) {
            margin-left: auto;
        }

        .user-num {
            color: #00bcd4;
        }

        .list .down_list {
            /* position: absolute;
            top: 2.7rem; */
            display: none;
            background: white;
            width: 100%;
            /* left: 0; */
        }
        .active>div{
        }
        .active>div::after{
            content:"";
            position: absolute;
            width: 1rem;
            height: 1rem;
            left: 0;right: 0;bottom: .2rem;margin: auto;
            background-image: url(/public/img/right.png);
            background-size: cover;
            background-repeat: no-repeat;
            transform: rotate(90deg)
        }
        .active>.down_list {
            display: block;
        }
    </style></head><body><header><div class="back" onclick="window.history.back(-1)"></div><span>直属下线</span></header><div class="container"><div class="cont"><div style=" display: block;"><ul class="list1 list"></ul></div></div></div></body><script>
    $(function() {
        $.ajax({
            url: urlPost("ctrl/bot_user"),
            type: "GET",
            dataType: "JSON",
            data: {},
            success: function(res) {
                console.log(res)
                if (res.code == 0) {
                    var list = res.data;
                    list.map(function(val) {
                        $(".list1").append(`
                            <li class="one" data-id="one" id="${val.id}"><div><div class="head"><img src="${val.headpic}" alt=""></div><div><p class="user-name">${val.username}</p><p class="user-tel">${val.tel}</p></div><div class="last"><p class="user-times">${timeTransform(val.addtime)}</p><p>直推人数: <span class="user-num">${val.childs}</span></p></div></div><ul class="down_list"></ul></li>`
                        )
                    })
                }else{
                    QS_toast.show(res.info,true)
                }
            },
            error: function(err) { console.log(err); submit = true }
        })
    })


    $("nav>p").click(function () {
        var _ind = $(this).index();
        $(this).addClass('nav_active').siblings().removeClass('nav_active');
        $(".cont>div").eq(_ind).show().siblings().hide()
    })

    // 一代下级下拉
    $(".list").on('click', '.one', function(e) {
        var _this = $(e.target),
            id = _this.attr('id') || _this.parents('.one').attr('id');
        $.ajax({
            url: urlPost("ctrl/bot_user"),
            type: "POST",
            dataType: "JSON",
            data: { id:id },
            success: function(res) {
                console.log(res)
                if (res.code == 0) {
                    var list = res.data;
                    $(e.target).parents('.one').children(".down_list").html("")
                    list.map(function(val) {
                        $(e.target).parents('.one').children(".down_list").append(`
                            <li class="two" data-id="two" id="${val.id}"><div><div class="head"><img src="${val.headpic}" alt=""></div><div><p class="user-name">${val.username}</p><p class="user-tel">${val.tel}</p></div><div class="last"><p class="user-times">${timeTransform(val.addtime)}</p><p>直推人数: <span class="user-num">${val.childs}</span></p></div></div><ul class="down_list"></ul></li>
                        `)
                    })
                }else{
                    QS_toast.show(res.info,true)
                    $(e.target).parents('.one').removeClass('active')
                }
            },
            error: function(err) { console.log(err) }
        })
        if (_this.attr('data-id') == 'one') {
            $(e.target).toggleClass('active')
        } else {
            $(e.target).parents('li').toggleClass('active')
        }
    });

    // 二代下拉
    $(".list").on('click', '.two', function(e) {
        e.stopPropagation()
        var _this = $(e.target),
            id = _this.attr('id') || _this.parent('.two').attr('id');
        $.ajax({
            url: urlPost("ctrl/bot_user"),
            type: "POST",
            dataType: "JSON",
            data: { id:id },
            success: function(res) {
                console.log(res)
                if (res.code == 0) {
                    var list = res.data;
                    $(e.target).parents('.two').children(".down_list").html("")
                    list.map(function(val) {
                        $(e.target).parents('.two').children(".down_list").append(`
                            <li class="three" data-id="three" id="${val.id}"><div><div class="head"><img src="${val.headpic}" alt=""></div><div><p class="user-name">${val.username}</p><p class="user-tel">${val.tel}</p></div><div class="last"><p class="user-times">${timeTransform(val.addtime)}</p><p>直推人数: <span class="user-num">${val.childs}</span></p></div><ul class="down_list"></ul></li>
                        `)
                    })
                }else{
                    QS_toast.show(res.info,true)
                    $(e.target).parents('.two').removeClass('active')
                }
            },
            error: function(err) { console.log(err) }
        })

        if (_this.attr('data-id') == 'two') {
            $(e.target).toggleClass('active')
        } else {
            $(e.target).parents('.two').toggleClass('active')
        }
    })
</script></html>