<?php /*a:2:{s:67:"D:\wwwroot\cesyx01.cn\application\admin\view\deal\deal_console.html";i:1585051965;s:54:"D:\wwwroot\cesyx01.cn\application\admin\view\main.html";i:1575947686;}*/ ?>
<div class="layui-card layui-bg-gray"><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><form onsubmit="return false;" data-auto="true" method="post" class='layui-form layui-card' autocomplete="off"><div class="layui-card-body padding-40"><label class="layui-form-item block relative"><span class="color-green margin-right-10">收款银行卡号</span><input name="master_cardnum" required value="<?php echo config('master_cardnum'); ?>" class="layui-input"></label><label class="layui-form-item block relative"><span class="color-green margin-right-10">收款人</span><input name="master_name" required value="<?php echo config('master_name'); ?>" class="layui-input"></label><label class="layui-form-item block relative"><span class="color-green margin-right-10">所属银行</span><input name="master_bank" required value="<?php echo config('master_bank'); ?>" class="layui-input"></label><label class="layui-form-item block relative"><span class="color-green margin-right-10">银行地址</span><input name="master_bk_address" required value="<?php echo config('master_bk_address'); ?>" class="layui-input"></label><hr/><label class="layui-form-item block relative"><span class="color-green margin-right-10">交易所需余额</span><!-- <span class="nowrap color-desc">StoreTitle</span> --><input name="deal_min_balance" required value="<?php echo config('deal_min_balance'); ?>" class="layui-input"><input type="hidden" name="_csrf_" value="<?php echo systoken('admin/deal/deal_console'); ?>"><p class="help-block">交易所需余额</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">未支付订单的支付等待时长（秒）</span><!-- <span class="nowrap color-desc">OrderWaitTime</span> --><input name="deal_timeout" required placeholder="请输入订单支付等待时长" value="<?php echo config('deal_timeout'); ?>" class="layui-input"><p class="help-block">订单支付等待时长，新下订单未在此时间内容完成支付将会被自动取消</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">超时订单冻结时间</span><!-- <span class="nowrap color-desc">OrderWaitTime</span> --><input name="order_freeze" required placeholder="请输入订单支付等待时长" value="<?php echo config('order_freeze'); ?>" class="layui-input"><p class="help-block">订单支付等待时长，新下订单未在此时间内容完成支付将会被自动取消</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">匹配范围</span><!-- <span class="nowrap color-desc">OrderClearTime</span> --><div id="deal_rule" style="position:relative; margin: 10px 0px;" class="demo-slider"></div><div id="test-slider-tips2" style="position:relative; margin: 10px 0px;" class="help-block">匹配范围：<?php echo config('deal_min_num'); ?>% ~ <?php echo config('deal_max_num'); ?>%</div><input type="hidden" name="deal_min_num" id='min' value="<?php echo config('deal_min_num'); ?>"><input type="hidden" name="deal_max_num" id='max' value="<?php echo config('deal_max_num'); ?>"></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">当日交易次数限制</span><input name="deal_count" required value="<?php echo config('deal_count'); ?>" class="layui-input"><p class="help-block">当日交易次数限制</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">奖励交易次数</span><input name="deal_reward_count" required value="<?php echo config('deal_reward_count'); ?>" class="layui-input"><p class="help-block">奖励交易次数</p></label><!--<label class="layui-form-item margin-top-20 block relative">--><!--<span class="color-green margin-right-10">直推会员推广佣金</span>--><!--&lt;!&ndash; <span class="nowrap color-desc">OrderClearTime</span> &ndash;&gt;--><!--<input name="1_reward" max="1" min="0" step="0.01" required value="<?php echo config('1_reward'); ?>" type="number" class="layui-input">--><!--<p class="help-block">推广佣金</p>--><!--</label>--><!--<label class="layui-form-item margin-top-20 block relative">--><!--<span class="color-green margin-right-10">上两级会员推广佣金</span>--><!--&lt;!&ndash; <span class="nowrap color-desc">OrderClearTime</span> &ndash;&gt;--><!--<input name="2_reward" max="1" min="0" step="0.01" required value="<?php echo config('2_reward'); ?>" type="number" class="layui-input">--><!--<p class="help-block">推广佣金</p>--><!--</label>--><!--<label class="layui-form-item margin-top-20 block relative">--><!--<span class="color-green margin-right-10">上三级会员推广佣金</span>--><!--&lt;!&ndash; <span class="nowrap color-desc">OrderClearTime</span> &ndash;&gt;--><!--<input name="3_reward" max="1" min="0" step="0.01" required value="<?php echo config('3_reward'); ?>" type="number" class="layui-input">--><!--<p class="help-block">推广佣金</p>--><!--</label>--><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">普通会员交易佣金</span><!-- <span class="nowrap color-desc">OrderClearTime</span> --><input name="vip_1_commission" max="1" min="0" step="0.01" required value="<?php echo config('vip_1_commission'); ?>" type="number" class="layui-input"><p class="help-block">交易佣金</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">上一级会员交易佣金</span><!-- <span class="nowrap color-desc">OrderClearTime</span> --><input name="1_d_reward" max="1" min="0" step="0.01" required value="<?php echo config('1_d_reward'); ?>" type="number" class="layui-input"><p class="help-block">交易佣金</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">上二级会员交易佣金</span><!-- <span class="nowrap color-desc">OrderClearTime</span> --><input name="2_d_reward" max="1" min="0" step="0.01" required value="<?php echo config('2_d_reward'); ?>" type="number" class="layui-input"><p class="help-block">交易佣金</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">上三级会员交易佣金</span><!-- <span class="nowrap color-desc">OrderClearTime</span> --><input name="3_d_reward" max="1" min="0" step="0.01" required value="<?php echo config('3_d_reward'); ?>" type="number" class="layui-input"><p class="help-block">交易佣金</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">上四级会员交易佣金</span><!-- <span class="nowrap color-desc">OrderClearTime</span> --><input name="4_d_reward" max="1" min="0" step="0.01" required value="<?php echo config('4_d_reward'); ?>" type="number" class="layui-input"><p class="help-block">交易佣金</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">上五级会员交易佣金</span><!-- <span class="nowrap color-desc">OrderClearTime</span> --><input name="5_d_reward" max="1" min="0" step="0.01" required value="<?php echo config('5_d_reward'); ?>" type="number" class="layui-input"><p class="help-block">交易佣金</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">冻结时间</span><!-- <span class="nowrap color-desc">OrderClearTime</span> --><input name="deal_feedze" required value="<?php echo config('deal_feedze'); ?>" class="layui-input"><p class="help-block">冻结时间</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">允许违规次数</span><input name="deal_error" required placeholder="请输入已发货订单自动确认收货时长" value="<?php echo config('deal_error'); ?>" class="layui-input"><p class="help-block">允许违规次数</p></label><label class="layui-form-item margin-top-20 block relative"><span class="color-green margin-right-10">提交订单延时时间(单位/秒)</span><p>
               远程主机分配时间: <input name="deal_zhuji_time" style="width:100px;display: inline-block" required placeholder="" value="<?php echo config('deal_zhuji_time'); ?>" class="layui-input">
               等待商家响应时间 <input name="deal_shop_time" style="width:100px;display: inline-block" required placeholder="" value="<?php echo config('deal_shop_time'); ?>" class="layui-input"></p><p class="help-block">时间由2部分组成,默认都是5秒,总共10秒</p></label><div class="layui-form-item text-center margin-top-20"><button class="layui-btn" type="submit">保存配置</button></div></div></form><script>
    layui.use('slider', function(){
      var $ = layui.$
      ,slider = layui.slider,
      min = "<?php echo config('deal_min_num'); ?>",
      max = "<?php echo config('deal_max_num'); ?>";
      //默认滑块
      //开启范围选择
      slider.render({
        elem: '#deal_rule'
        ,value: [min,max] //初始值
        ,range: true //范围选择
        ,change: function(vals){
          $('#test-slider-tips2').html('匹配范围：'+ vals[0] + '% ~ '+ vals[1]+'%');
          $('#min').val(vals[0]);
          $('#max').val(vals[1]);
        }
      });      
    });
</script></div></div>