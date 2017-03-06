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
			return '{"code":200, "msg":"请您三思啊"}';
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

	class FileUtil {

	    /**
	     * 建立文件夹
	     *
	     * @param string $aimUrl
	     * @return viod
	     */
	    function createDir($aimUrl) {
	        $aimUrl = str_replace('', '/', $aimUrl);
	        $aimDir = '';
	        $arr = explode('/', $aimUrl);//var_dump($arr);
	        $result = true;
	        foreach ($arr as $str) {
	            $aimDir .= $str . '/';
	            if (!file_exists($aimDir)) {//echo $aimDir.'----';
	                $result = mkdir($aimDir);
	            }
	        }
	        return $result;
	    }

	    /**
	     * 建立文件
	     *
	     * @param string $aimUrl 
	     * @param boolean $overWrite 该参数控制是否覆盖原文件
	     * @return boolean
	     */
	    function createFile($aimUrl, $overWrite = false) {
	        if (file_exists($aimUrl) && $overWrite == false) {
	            return false;
	        } elseif (file_exists($aimUrl) && $overWrite == true) {
	            FileUtil :: unlinkFile($aimUrl);
	        }
	        $aimDir = dirname($aimUrl);
	        FileUtil :: createDir($aimDir);
	        touch($aimUrl);
	        return true;
	    }

	    /**
	     * 移动文件夹
	     *
	     * @param string $oldDir
	     * @param string $aimDir
	     * @param boolean $overWrite 该参数控制是否覆盖原文件
	     * @return boolean
	     */
	    function moveDir($oldDir, $aimDir, $overWrite = false) {
	        $aimDir = str_replace('', '/', $aimDir);
	        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
	        $oldDir = str_replace('', '/', $oldDir);
	        $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
	        if (!is_dir($oldDir)) {
	            return false;
	        }
	        if (!file_exists($aimDir)) {
	            FileUtil :: createDir($aimDir);
	        }
	        @ $dirHandle = opendir($oldDir);
	        if (!$dirHandle) {
	            return false;
	        }
	        while (false !== ($file = readdir($dirHandle))) {
	            if ($file == '.' || $file == '..') {
	                continue;
	            }
	            if (!is_dir($oldDir . $file)) {
	                FileUtil :: moveFile($oldDir . $file, $aimDir . $file, $overWrite);
	            } else {
	                FileUtil :: moveDir($oldDir . $file, $aimDir . $file, $overWrite);
	            }
	        }
	        closedir($dirHandle);
	        return rmdir($oldDir);
	    }

	    /**
	     * 移动文件
	     *
	     * @param string $fileUrl
	     * @param string $aimUrl
	     * @param boolean $overWrite 该参数控制是否覆盖原文件
	     * @return boolean
	     */
	    function moveFile($fileUrl, $aimUrl, $overWrite = false) {

	        if (!file_exists($fileUrl)) {
	            return false;
	        }
	        if (file_exists($aimUrl) && $overWrite = false) {
	            return false;
	        } elseif (file_exists($aimUrl) && $overWrite = true) {
	            FileUtil :: unlinkFile($aimUrl);
	        }
	        $aimDir = dirname($aimUrl);
	        FileUtil :: createDir($aimDir);
	        rename($fileUrl, $aimUrl);
	        return true;
	    }

	    /**
	     * 删除文件夹
	     *
	     * @param string $aimDir
	     * @return boolean
	     */
	    function unlinkDir($aimDir) {
	        $aimDir = str_replace('', '/', $aimDir);
	        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
	        if (!is_dir($aimDir)) {
	            return false;
	        }
	        $dirHandle = opendir($aimDir);
	        while (false !== ($file = readdir($dirHandle))) {
	            if ($file == '.' || $file == '..') {
	                continue;
	            }
	            if (!is_dir($aimDir . $file)) {
	                FileUtil :: unlinkFile($aimDir . $file);
	            } else {
	                FileUtil :: unlinkDir($aimDir . $file);
	            }
	        }
	        closedir($dirHandle);
	        return @rmdir($aimDir);
	    }

	    /**
	     * 删除文件
	     *
	     * @param string $aimUrl
	     * @return boolean
	     */
	    function unlinkFile($aimUrl) {
	        if (file_exists($aimUrl)) {
	            @unlink($aimUrl);
	            return true;
	        } else {
	            return false;
	        }
	    }

	    /**
	     * 复制文件夹
	     *
	     * @param string $oldDir
	     * @param string $aimDir
	     * @param  string $aimName 目标文件夹名称
	     * @param boolean $overWrite 该参数控制是否覆盖原文件
	     * @return boolean
	     */
	    function copyDir($oldDir, $aimDir, $overWrite = false) {
	        
	        $aimDir = str_replace('', '/', $aimDir);
	        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
	        $oldDir = str_replace('', '/', $oldDir);
	        $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
	        
	        if (!is_dir($oldDir)) {
	            return false;
	        }

	        if (!file_exists($aimDir)) {
	            FileUtil :: createDir($aimDir);
	        }
	        $dirHandle = opendir($oldDir);
	        while (false !== ($file = readdir($dirHandle))) {
	            if ($file == '.' || $file == '..') {
	                continue;
	            }
	            if (!is_dir($oldDir . $file)) {
	                FileUtil :: copyFile($oldDir . $file, $aimDir . $file, $overWrite);
	            } else {
	                FileUtil :: copyDir($oldDir . $file, $aimDir . $file, $overWrite);
	            }
	        }
	        //return closedir($dirHandle);
	        closedir($dirHandle);
	        return true;
	    }

	    /**
	     * 复制文件
	     *
	     * @param string $fileUrl
	     * @param string $aimUrl
	     * @param boolean $overWrite 该参数控制是否覆盖原文件
	     * @return boolean
	     */
	    function copyFile($fileUrl, $aimUrl, $overWrite = false) {
	        if (!file_exists($fileUrl)) {
	            return false;
	        }
	        if (file_exists($aimUrl) && $overWrite == false) {
	            return false;
	        } elseif (file_exists($aimUrl) && $overWrite == true) {
	            FileUtil :: unlinkFile($aimUrl);
	        }
	        $aimDir = dirname($aimUrl);
	        FileUtil :: createDir($aimDir);
	        copy($fileUrl, $aimUrl);
	        //20170110   同时复制文件的上次修改时间
	        touch($aimUrl, filemtime($fileUrl));
	        return true;
	    }

	    //看是否字符转换成功
	    function checkiconv($param, $mode){
	        if($mode=='utf2gbk'){
	            $rep = iconv('UTF-8', "GBK", $param);
	        }else if($mode=='gbk2utf'){
	            $rep = iconv('GBK', "UTF-8", $param);
	        }
	        
	        if($rep!==FALSE){
	            $param=$rep;
	        }else{
	            $param=$param;
	        }
	        return $param;
	    }

	    function getMsg(){
	        $IP = $_SERVER['REMOTE_ADDR'];
			session_start();
	        $uName = $_SESSION['uName'];
	        date_default_timezone_set('Asia/Shanghai');
	        $date = date('Y-m-d H:i:s');
	        //$message = $IP.'    '.$date.'   ';//日志信息
	        $message = $uName.'    '.$date.'   ';//日志信息
	        return $message;
	    }

	    function pasteFiles($fileUtil, $copyFrom, $toDir, $method){
	        $posL = strrpos($copyFrom, '/');//最后一个斜杠位置  后面就是完整的名字了
	        //echo $copyFrom;die;
	        $copyFrom = $fileUtil->checkiconv($copyFrom, 'utf2gbk');//UTF-8转成GBK 才能操作文件名    
	        $toDir=$fileUtil->checkiconv($toDir, 'utf2gbk');
	        $toDir = rtrim($toDir, '/');
	            
	        if(is_dir($copyFrom)){
	            $copyFromUTF = $copyFrom = $fileUtil->checkiconv($copyFrom, 'gbk2utf');
	            $file_name=substr($copyFrom, $posL+1);//直接得到目录名称
	            $copyFrom = $fileUtil->checkiconv($copyFrom, 'utf2gbk');//UTF-8转成GBK 才能操作文件名    
	            $file_name=$fileUtil->checkiconv($file_name, 'utf2gbk');

	            $pasteTo = $toDir.'/'.$file_name;//echo $pasteTo;//die;
	        
	            $dir = dirname($copyFrom);
	            if($dir == dirname($pasteTo)){
	                date_default_timezone_set('Asia/Shanghai');
	                $time = date('ymdHis', time());
	                $pasteTo = $dir.'/'.$file_name.'_copy_'.$time;
	            }else if(file_exists(dirname($pasteTo)."/".$file_name)){
	                date_default_timezone_set('Asia/Shanghai');
	                $time = date('ymdHis', time());
	                $pasteTo = $pasteTo.'_copy_'.$time;
	            }
	            $msg = $copyFromUTF.' 到 '.$fileUtil->checkiconv($pasteTo, 'gbk2utf').', ';
	            //echo $copyFrom.'----'.$pasteTo."<br />";
	            //$res = $fileUtil->copyDir($copyFrom, $pasteTo, $file_name, true);
	            if($method=='copy'){
	                $res = $fileUtil->copyDir($copyFrom, $pasteTo, true);
	            }else if($method=='cut'){
	                //moveDir($oldDir, $aimDir, $overWrite = false)
	                $res = $fileUtil->moveDir($copyFrom, $pasteTo, true);
	            }
	            
	        }else{
	            $copyFromUTF = $copyFrom = $fileUtil->checkiconv($copyFrom, 'gbk2utf');
	            $posLp = strrpos($copyFrom, '.');//最后一个点位置  为了去掉后缀名
	            $file_name=substr($copyFrom, $posL+1, $posLp);
	            $copyFrom = $fileUtil->checkiconv($copyFrom, 'utf2gbk');//UTF-8转成GBK 
	            $file_name=$fileUtil->checkiconv($file_name, 'utf2gbk');

	            $pasteTo = $toDir.'/'.$file_name;//echo $pasteTo;//die;

	            $dir = dirname($copyFrom);
	            //if($dir == dirname($pasteTo)){//复制到同目录
	            if($dir == dirname($pasteTo)){//复制到同目录
	                date_default_timezone_set('Asia/Shanghai');
	                $time = date('ymdHis', time());
	                //获得扩展名
	                //扩展名前点
	                $rpos = strrpos($file_name, '.');
	                $fname = substr($file_name, 0, $rpos);
	                $ext = substr($file_name, $rpos);//带点
	                $pasteTo = $dir.'/'.$fname.'_copy_'.$time.$ext;
	            }else if(file_exists(dirname($pasteTo)."/".$file_name)){
	                //不到同目录 要看是不是有同名文件 如果有同名文件 也需要重命名
	                date_default_timezone_set('Asia/Shanghai');
	                $time = date('ymdHis', time());
	                //获得扩展名
	                //扩展名前点
	                $rpos = strrpos($file_name, '.');
	                $fname = substr($file_name, 0, $rpos);
	                $ext = substr($file_name, $rpos);//带点
	                $pasteTo = $toDir.'/'.$fname.'_copy_'.$time.$ext;
	            }

	            $msg = $copyFromUTF.' 到 '.$fileUtil->checkiconv($pasteTo, 'gbk2utf').', ';
	            //echo $copyFrom.'----'.$pasteTo."=======<br />";
	            if($method=='copy'){
	                $type = '复制文件';
	                $res = $fileUtil->copyFile($copyFrom, $pasteTo, true);
	            }else if($method=='cut'){
	                //moveDir($oldDir, $aimDir, $overWrite = false)
	                $type = '剪切文件';
	                //echo $copyFrom.'------'. $pasteTo;
	                $res = $fileUtil->moveFile($copyFrom, $pasteTo, true);
	            }
	        }
	        
	        $message = $fileUtil->getMsg();
	        if(!$res){
	            error_log($message.' '.$type.'失败！'."\r\n", 3, './logs/operLog.txt');
	            echo json_encode(array('code'=>400, 'msg'=>$type.'出错！'));
	            return false;
	        }else{
	            return $msg;
	        }
	    }

	    function deleteFiles($fileName, $pathRoot, $fileUtil){
	        $fileName = rtrim($fileName, '/');
	        //echo dirname($fileName);die;
	        if(!empty($fileName) && $fileName!=$pathRoot){//echo $fileName;
	            $fileName = $fileUtil->checkiconv($fileName, 'utf2gbk');
	            if(is_dir($fileName)){//echo 1;
	                $res = $fileUtil->unlinkDir($fileName); 
	            }else{
	                $res = $fileUtil->unlinkFile($fileName); 
	            }
	            $message = $fileUtil->getMsg();
	            $fileName = $fileUtil->checkiconv($fileName, 'gbk2utf');
	            if(!$res){
	                echo json_encode(array('code'=>400, 'msg'=>'可能由于权限关系，你无法删除文件！'));
	                error_log($message.$fileName.'删除失败！'."\r\n", 3, './logs/operLog.txt');
	                return false;
	            }else{
	                return $fileName.', ';
	            }
	        }else{
	            echo json_encode(array('code'=>400, 'msg'=>'没有要删除的item'));
	            error_log($message.'删除文件失败！'."\r\n", 3, './logs/operLog.txt');
	            return false;
	        }
	    }
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
		$resourceDir = $_POST['resourceDir'];
		if(!is_dir($targetDir.'/'.$versionName)){
			//不存在同名的大版本 就创建
			////1. 自动创建该大版本的文件夹比如../Versions/VN
			mkdir($targetDir.'/'.$versionName);
			//2. 并自动copy当前的luatest文件夹到新建的VN下  名字也是luatest 之后所有的都是跟这个作比较
			//copyDir($oldDir, $aimDir, $overWrite = false)
			$fileUtil = new FileUtil();
			$fileUtil->copyDir($resourceDir, $targetDir.'/'.$versionName.'/luatest');

			if(is_dir($targetDir.'/remarks')){
				//3. 自动在目录../Versions/remarks中新建一个VN_remarks.php的文件用于保存该大版本下的所有版本的备注信息
				touch($targetDir.'/remarks/'.$versionName.'_remarks.php');//大版本 要建他的备注文件
				file_put_contents($targetDir.'/remarks/'.$versionName.'_remarks.php', '<?php $remarks=\'{}\';return $remarks;');
			}
			echo '{"code":200, "msg":"创建成功"}';
		}else{
			echo '{"code":400, "msg":"已存在同名大版本名"}';
		}
	}else if($_POST['refer']=='newVersion'){
		
		//发生变化的所有可能情况：
		//1. 新增文件（比当前大版本中luatest中多的文件 放到新建的小版本 相对应的目录中）
		//2. 修改文件（相比当前大版本中luatest中修改的文件（文件大小，二进制内容？修改时间？？） 放到新建的小版本 相对应的目录中）
		//3. 删除文件（相比当前大版本中luatest中删除的文件？？？如何知道）
		

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