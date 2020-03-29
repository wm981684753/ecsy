$(document).ready(function(){
	

	$(".zhe-bg").hide();
	$(".tan-box").hide();
	
	$(".esc").click(function(){
		$(".zhe-bg").hide();
		$(".index-bg").hide();
		$(".tang-pic").hide();
		$(".tan-box").hide();
		$(".name-tan").hide();
		$(".pass-tan").hide();
	});	
	$(".esc-btn").click(function(){
		$(".zhe-bg").hide();
		$(".tan-box").hide();
	});	
	$(".shan-btn").click(function(){
		$(".zhe-bg").hide();
		$(".tan-box").hide();
		$(".english").html(''); 
	});	
	$(".down-ewm").click(function(){
		$(".ewm-tan").show();
		$(".zhe-bg").show();
	});	
	$(".cn-li").click(function(){
		$(".cn-li").removeClass("on");
		$(this).addClass("on");
	});	

	$(".data-li").click(function(){
		$(".data-li").removeClass("on");
		$(this).addClass("on");
	});	

	$(".add-add").click(function(){
		$(".add-tan").show();
		$(".zhe-bg").show();
	});	

	$(".add-cn").click(function(){
		$(".cn-tan").show();
		$(".zhe-bg").show();
	});	
	
	$(".change-pass").click(function(){
		$(".pass-tan").show();
		$(".zhe-bg").show();
	});	
	$(".change-pass2").click(function(){
		$(".pass-tan2").show();
		$(".zhe-bg").show();
	});	
	$(".message-li").click(function(){
		$(".message-tan").show();
		$(".zhe-bg").show();
	});	

	$(".change-name").click(function(){
		$(".name-tan").show();
		$(".zhe-bg").show();
	});	
	$(".beifen").click(function(){
		$(".beifen-tan").show();
		$(".zhe-bg").show();
	});	
	
	$(".eng-li").click(function(){
		if($(this).hasClass("on")){
			$(this).removeClass("on");
		}else{	
			$(this).addClass("on");
		}
	});	
	
	$(".Crow-two").hide();
	$(".Crow-three").hide();
	$(".Ctab-one").click(function(){
		$(".Ctab-one").addClass("on");
		$(".Ctab-two").removeClass("on");
		$(".Ctab-three").removeClass("on");
		$(".Crow-one").show();
		$(".Crow-two").hide();
		$(".Crow-three").hide();
	});	

	$(".Ctab-two").click(function(){
		$(".Ctab-one").removeClass("on");
		$(".Ctab-two").addClass("on");
		$(".Ctab-three").removeClass("on");
		$(".Crow-one").hide();
		$(".Crow-two").show();
		$(".Crow-three").hide();
	});	

	$(".Ctab-three").click(function(){
		$(".Ctab-one").removeClass("on");
		$(".Ctab-two").removeClass("on");
		$(".Ctab-three").addClass("on");
		$(".Crow-one").hide();
		$(".Crow-two").hide();
		$(".Crow-three").show();
	});	
	
});
