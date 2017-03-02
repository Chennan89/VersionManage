<?php
	
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

	function getFiles($path, $contat='', $layer='', $prelayer='', $class='', $parent){
		$IP = $_SERVER['REMOTE_ADDR'];
		/*if($IP!='192.168.15.105'){
			include_once('LogUtil.php');//有操作 就更新操作日志
			$flag = modifyLog();
			if($flag === false){
				echo '<meta http-equiv="refresh" content="0.01;url=resM.php"> ';
				return false;
			}
		}*/
		$path = checkiconv($path, 'utf2gbk');
		if(!is_dir($path)){
			return false;
		}
		date_default_timezone_set('Asia/Shanghai');
		$createTime = date('Y-m-d H:i:s', filectime($path));
		$file = new FilesystemIterator($path);
		if(!empty($class)){
			$editRemark="";
		}else{
			$editRemark="<a href='javascript:;' class='edit btn purple'><i class='icon-pencil'></i> Edit</a><a href='javascript:;' class='save btn green'><i class='icon-save'></i>Save</a>";
		}
		$path = checkiconv($path, 'gbk2utf');//为保证下次传过来的参数是UTF-8的
		foreach ($file as $fileinfo) {
	    	$filename = $fileinfo->getFilename();
	    	$filename = checkiconv($filename, 'gbk2utf');
	    	$isDir = $fileinfo->isDir();
	    	if($contat!=''){
	    		$liL='40px';
	    	}else{
	    		$liL=0;
	    	}
	        $bgPos = 20*($layer-1).'px';//echo $class;

	        echo "<li class='timeline-yellow ".$class."' data-dir='$path' style='margin-left:{$liL};'><div class='timeline-time'><span class='time' style='font-size:16px;'>".$createTime."</span></div><div class='timeline-icon'><i class='icon-tag'></i></div><div class='timeline-body'><h2 style='display:inline-block;margin:0;border:none;width:75%;overflow:hidden;height:30px;line-height:34px;'>".$filename."</h2><div class='portlet-title' style='display:inline-block; float:right; border:none;'><div class='actions'><a href='javascript:;' class='collapse arrow' style='display:inline-block; width:14px; height:16px;' data-id='{$filename}' data-parent='$parent'></a>{$editRemark}</div></div></div></li>";
		    //echo "<div data-layer='$layer' data-unique='{$path}{$filename}' data-dir='$path' data-clicked=0 id='$filename' class='{$class}' style='background-position:$bgPos 0px;'>".$contat. "<div class='$addClass'>&nbsp;</div>$filename </div>";
	    }
	}

	function getContents($path){
		$path = rtrim($path, '/');
		$path = checkiconv($path, 'utf2gbk');
		if($_POST['type']=='json'){
			$con = file_get_contents($path);
			$con = checkiconv($con, 'gbk2utf');
			$content=trim($con, "\xEF\xBB\xBF");
			$contents =  '<pre>' .$content.'</pre>';
			//$contents=trim($contents, "\xEF\xBB\xBF");//去除BOM头
		}else{
			$contents = '';
		}
	
		//得到文件信息
		$data = array();
		date_default_timezone_set('Asia/Shanghai');
		if(is_dir($path)){
			$size=getDirSize($path);
			$realSize=getRealSize($size);
		}else{
			$size=filesize($path);
			$realSize=getRealSize($size);
		}
		$data['timeinfo'] = '创建时间'.date('Y-m-d H:i:s', filectime($path))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".'上次修改时间'.date('Y-m-d H:i:s', filemtime($path))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;文件大小".$realSize;
		$data['contents'] = $contents;
		$path = checkiconv($path, 'gbk2utf');
		$data['path'] = '路径'.$path;
		
		echo json_encode($data);
	}
	
	// 获取文件夹大小 
    function getDirSize($dir){ 
        $handle = opendir($dir); 
        while (false!==($FolderOrFile = readdir($handle))){ 
            if($FolderOrFile != "." && $FolderOrFile != "..") { 
                if(is_dir("$dir/$FolderOrFile")){ 
                    $sizeResult += getDirSize("$dir/$FolderOrFile"); 
                }else{
                    $sizeResult += filesize("$dir/$FolderOrFile"); 
                } 
            }    
        } 
        closedir($handle); 
        return $sizeResult; 
    } 
    // 单位自动转换函数 
    function getRealSize($size) { 
        $kb = 1024;         // Kilobyte 
        $mb = 1024 * $kb;   // Megabyte 
        $gb = 1024 * $mb;   // Gigabyte 
        $tb = 1024 * $gb;   // Terabyte 
        
        if($size < $kb) 
        { 
            return $size." B"; 
        } 
        else if($size < $mb) 
        { 
            return round($size/$kb,2)." KB"; 
        } 
        else if($size < $gb) 
        { 
            return round($size/$mb,2)." MB"; 
        } 
        else if($size < $tb) 
        { 
            return round($size/$gb,2)." GB"; 
        } 
        else 
        { 
            return round($size/$tb,2)." TB"; 
        } 
    } 
    //echo  getRealSize(getDirSize('目录')); 

	if(!empty($_POST['refer']) && $_POST['refer'] == 'ajax'){
		$path = $_POST['preDir'];
		$parent = $_POST['parent'];
		//echo $path;
		$class = $_POST['class'];
		$prelayer = $_POST['prelayer'];
		$contat = $_POST['contat'];
		getFiles($path, $contat, $prelayer+1, $prelayer, $class, $parent);
	}
?> 