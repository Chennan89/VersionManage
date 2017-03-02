<?php
	//得到备注信息
	function getRemark($remarkID, $remarkFolder){
		$file="c:/wamp/www/Versions/remarks/{$remarkFolder}_remarks.php";
		if(file_exists($file)){
			require_once($file);
			if(!is_null(json_decode($remarks))){
				$remarks=json_decode($remarks);
			}
		}else{
			$remarks=null;
		}
		if(empty($remarks->{$remarkID}) && empty($remarkFolder)){//第二个条件防止 真正版本注释为空时 设置为 大版本
			//$remark='大版本';
			return;
		}else{
			$remark=$remarks->{$remarkID}? $remarks->{$remarkID}: '暂无备注信息';
		}
		//echo $remarks->{'v1.1'};die;
		return "<div class='timeline-content'>".$remark."</div>";
	}

	//修改或新增备注信息
	function setRemark($newRemark, $remarkID, $remarkFolder){
		$file="c:/wamp/www/Versions/remarks/{$remarkFolder}_remarks.php";
		if(file_exists($file)){
			//取出内容 对相应内容修改数据 然后重新放回去
			$remarks=file_get_contents($file);
		}else{
			echo 2;
		}
		//echo $remarks;
		$remarks=rtrim($remarks, 'return $remarks;'."'\r\n");
		$remarks=ltrim($remarks, '<?php $remarks='."'");
		
		if(!is_null(json_decode($remarks))){
			$remarks=json_decode($remarks);
		}

		$remarks->{$remarkID}=$newRemark;
		$remarks=json_encode($remarks);
		$content = '<?php $remarks=\''.$remarks.'\';return $remarks;';
		file_put_contents($file, $content);
		return '{"code":200, "msg":"修改成功"}';
	}

	if($_POST['refer']=='getRemark'){
		$remarkID=$_POST['remarkID'];
		$remarkFolder=$_POST['remarkFolder'];
		$remark = getRemark($remarkID, $remarkFolder);
		echo $remark;
	}else if($_POST['refer']=='setRemark'){
		$newRemark=$_POST['remark'];//备注信息
		$remarkID=$_POST['remarkID'];
		$remarkFolder=$_POST['remarkFolder'];
		$remark = setRemark($newRemark, $remarkID, $remarkFolder);
		echo $remark;
	}else if($_POST['refer']=='newBaseVersion'){
		//新建大版本
		//1. 自动创建该大版本的文件夹比如../Versions/VN, 并自动copy当前的luatest文件夹到新建的VN下
		//2. 自动在目录../Versions/remarks中新建一个VN_remarks.php的文件用于保存该大版本下的所有版本的备注信息
		$targetDir = $_POST['targetDir'];
		$versionName = $_POST['versionName'];
		if(!is_dir($targetDir.'/'.$versionName)){
			//不存在同名的大版本 就创建
			mkdir($targetDir.'/'.$versionName);

			if(is_dir($targetDir.'/remarks')){
				touch($targetDir.'/remarks/'.$versionName.'_remarks.php');//大版本 要建他的备注文件
				file_put_contents($targetDir.'/remarks/'.$versionName.'_remarks.php', '<?php $remarks=\'{}\';return $remarks;');
			}
			echo '{"code":200, "msg":"创建成功"}';
		}else{
			echo '{"code":400, "msg":"已存在同名大版本名"}';
		}
	}else if($_POST['refer']=='newVersion'){
		//新建小版本
		//第一次创建的时候小版本目录（这是创建大目录后的第一个子目录）的时候，把luatest中内容复制到当前小版本目录
		//创建一个目录 内容是luatest相对于大版本中第一个 基本目录 的变量
		$targetDir = $_POST['targetDir'];
		$versionName = $_POST['versionName'];
		if(!is_dir($targetDir.'/'.$versionName)){
			//不存在同名的大版本 就创建
			mkdir($targetDir.'/'.$versionName);

			if(is_dir($targetDir.'/remarks')){
				touch($targetDir.'/remarks/'.$versionName.'_remarks.php');//大版本 要建他的备注文件
				file_put_contents($targetDir.'/remarks/'.$versionName.'_remarks.php', '<?php $remarks=\'{}\';return $remarks;');
			}
			echo '{"code":200, "msg":"创建成功"}';
		}else{
			echo '{"code":400, "msg":"已存在同名大版本名"}';
		}
	}else{
		echo '{"code":400, "msg":"参数不合法"}';
	}