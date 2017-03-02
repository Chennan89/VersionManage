<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="renderer" content="webkit">
	<title>登录</title>
	<link rel="stylesheet" href="Public/css/style.css">
	<!-- <link rel="stylesheet" href="Public/font/font-awesome.css"> -->
	<link rel="stylesheet" href="Public/css/font-awesome.min.css">
	<link rel="stylesheet" href="Public/css/style-metro.css">
	<link rel="stylesheet" href="Public/css/timeline.css">
	<style>
		body{min-width:1100px;}
		.collapse{background-image:url(./Public/image/portlet-collapse-icon-white.png);}
		.uncollapse{background-image:url(./Public/image/portlet-expand-icon-white.png);}
		.edit, .save{display:none;}
		.timeline > li .timeline-content{font-size:20px; color:#333; outline:#EEE solid; line-height:30px; min-height:30px;}
		/* .timeline li.newEdition .timeline-body:after{content:none;} */
	</style>
</head>
<body>
	<div id="J_main" class="g-main" style="width:80%; margin:50px auto 10px; text-align:right;">
		<?php 
		//include('inc/footer.inc.html') 
		?>
		<!--
		新建大版本的时候 自动创建该大版本的文件夹比如VN, 并自动copy当前的luatest文件夹到新建的VN下,
		自动新建一个VN_remarks.php的文件用于保存该大版本下的所有版本的备注信息
		另，-->
		<input type="text" placeholder="medium" class="m-wrap small" id="baseVersionName">
		<a href="#" class="btn blue" id="baseVersion"><i class="icon-plus"></i>新建大版本</a>
		<input type="text" placeholder="medium" class="m-wrap small" id="versionName">
		<a href="#" class="btn blue" id="version"><i class="icon-plus"></i>新建小版本版本</a>
	</div>

	<div class="row-fluid" style="width:80%;margin:0 auto;">

		<div class="span12">

			<ul class="timeline">
				
			</ul>

		</div>

	</div>


	<!-- js -->
	<!--也可以通过 data-main置顶默认要先执行的js文件，把app换成js/main.js就不用单独引入这个文件了
	<script data-main="app" src="lib/require.js"></script>
	-->
	<!--<script data-main="Public/js/main" src="Public/js/lib/require.min.js"></script>-->
	<script src="Public/js/lib/require.min.js"></script>
	<script src="Public/js/main.js"></script>
	<?php //include('inc/ite9.inc.html') ?>
	<script>
		//var module = 'login';
		require(['login'],function(login){

		});
	</script>
	<!-- /js -->
</body>
</html>