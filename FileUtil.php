<?php
    header('content-type:text/html;charset=utf-8');
/**
 * 操纵文件类
 * 
 * 例子：
 * FileUtil::createDir('a/1/2/3');                    测试建立文件夹 建一个a/1/2/3文件夹
 * FileUtil::createFile('b/1/2/3');                    测试建立文件        在b/1/2/文件夹下面建一个3文件
 * FileUtil::createFile('b/1/2/3.exe');             测试建立文件        在b/1/2/文件夹下面建一个3.exe文件
 * FileUtil::copyDir('b','d/e');                    测试复制文件夹 建立一个d/e文件夹，把b文件夹下的内容复制进去
 * FileUtil::copyFile('b/1/2/3.exe','b/b/3.exe'); 测试复制文件        建立一个b/b文件夹，并把b/1/2文件夹中的3.exe文件复制进去
 * FileUtil::moveDir('a/','b/c');                    测试移动文件夹 建立一个b/c文件夹,并把a文件夹下的内容移动进去，并删除a文件夹
 * FileUtil::moveFile('b/1/2/3.exe','b/d/3.exe'); 测试移动文件        建立一个b/d文件夹，并把b/1/2中的3.exe移动进去                   
 * FileUtil::unlinkFile('b/d/3.exe');             测试删除文件        删除b/d/3.exe文件
 * FileUtil::unlinkDir('d');                      测试删除文件夹 删除d文件夹
 */
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

if($_POST['logout']=='logout'){
    file_put_contents('./logs/access.log', '');//echo 89;
    session_start();
    $_SESSION['uName']=null;
    echo json_encode(array('code'=>200));
}else if($_POST['ajax'] == 'ajaxCopy'){
    //1复制文件
    $fileUtil = new FileUtil();
    $fromDir = $_POST['fromDir'];
    $toDir =$_POST['toDir'];
    $toDir = rtrim($toDir, '/');
    $method = $_POST['method'];//cut 或 copy

    $fromDir = trim($fromDir, ',');
    //var_dump(strpos($fromDir, ','));die;
    if($method=='copy'){
        $copyMsg = '复制文件';
    }else if($method=='cut'){
        $copyMsg = '剪切文件';
    }
    
    if(strpos($fromDir, ',') != false && strpos($fromDir, ',') != 0){
        $fDir = explode(',', $fromDir);
        //var_dump($fDir);die;
        foreach($fDir as $k=>$copyFrom){//echo $copyFrom.'<br />';
            $copyFrom = rtrim($copyFrom, '/');
            //echo $copyFrom.',,,'.$toDir.'----';
            $copyMsg .= $fileUtil->pasteFiles($fileUtil, $copyFrom, $toDir, $method);
        }
    }else{
        $copyFrom = rtrim($fromDir, '/');
        $posL = strrpos($copyFrom, '/');//最后一个斜杠位置  后面就是完整的名字了
        //echo $copyFrom.',,,'.$toDir.'----';
        //$copyFrom = $fileUtil->checkiconv($copyFrom, 'utf2gbk');//UTF-8转成GBK 才能操作文件名    
        //$toDir=$fileUtil->checkiconv($toDir, 'utf2gbk');
        $copyMsg .= $fileUtil->pasteFiles($fileUtil, $copyFrom, $toDir, $method);
    }

    if($copyMsg == '剪切文件' || $copyMsg == '复制文件'){
        return false;//并未删除成功
    }
    $message = $fileUtil->getMsg();
    $copyFrom = $fileUtil->checkiconv($copyFrom, 'gbk2utf');
    $pasteTo = $fileUtil->checkiconv($toDir, 'gbk2utf');
    echo json_encode(array('code'=>200, 'msg'=>$copyMsg.'成功'));
    //$info = '目录从 '.$copyFrom.' 复制到 '.$pasteTo.' 复制成功!'."\r\n";
    $info = $copyMsg .' 成功!'."\r\n";
    error_log($message.$info, 3, './logs/operLog.txt');

    
}else if($_POST['ajax'] == 'ajaxRename'){
    //2重命名文件
    $fileUtil = new FileUtil();
    $newName = trim($_POST['newName'], '/');
    $newName = $fileUtil->checkiconv($newName, 'utf2gbk');
    $source = trim($_POST['origDir'], '/');
    $source = $fileUtil->checkiconv($source, 'utf2gbk');
    $oriDir = dirname($source);
    $newName = $oriDir . '/' . $newName;
	$res = @rename($source, $newName);
    $message = $fileUtil->getMsg();
    if($res){
        echo json_encode(array('code'=>200, 'msg'=>'修改成功'));
        $source = $fileUtil->checkiconv($source, 'gbk2utf');
        $newName = $fileUtil->checkiconv($newName, 'gbk2utf');
        
        $info = $source.' 重命名为 '.$newName.' 成功!'."\r\n";
        error_log($message.$info, 3, './logs/operLog.txt');
    }else{
        echo json_encode(array('code'=>400, 'msg'=>'重命名文件失败！'."\r\n"));
        error_log($message.' 重命名文件失败,可能已有同名文件！'."\r\n", 3, './logs/operLog.txt');
    }
    
}else if($_POST['ajax'] == 'ajaxNewFile'){
    //3新建文件夹
    $fileUtil = new FileUtil();
    $fileName = $_POST['fileName'];
    $destDir = $_POST['origDir'];

    $newItem =  rtrim($destDir, '/').'/'.$fileName;
    $newItem = $fileUtil->checkiconv($newItem, 'utf2gbk');
    $res = $fileUtil->createDir($newItem);   

    $message = $fileUtil->getMsg();
    $newItem = $fileUtil->checkiconv($newItem, 'gbk2utf');
        
    if($res){
        echo json_encode(array('code'=>200, 'msg'=>'新建成功'));
        
        $info = $newItem.' 新建成功!'."\r\n";
        error_log($message.$info, 3, './logs/operLog.txt');
    }else{
        echo json_encode(array('code'=>400, 'msg'=>'新建文件夹出错！'));
        $info = $newItem.' 新建失败!'."\r\n";
        error_log($message.$info, 3, './logs/operLog.txt');
    }
}else if($_POST['ajax'] == 'ajaxDelete'){
    //4删除文件或文件夹
    $fileUtil = new FileUtil();
    $pathRoot = $_POST['pathRoot'];
    $fileName = $_POST['fileName'];
    $fileName = trim($fileName, ',');
    if(empty($fileName) || $fileName==$pathRoot){
        echo json_encode(array('code'=>400, 'msg'=>'你需要选中一个要删除的目录'));
        return false;
    }

    $fNames = '';
    if(strpos($fileName, ',') != false && strpos($fileName, ',') != 0){
        $fNames = explode(',', $fileName);
        foreach($fNames as $k=>$fileName){//echo $fileName;
            $fName .= $fileUtil->deleteFiles($fileName, $pathRoot, $fileUtil);
        }
    }else{
        $fName .= $fileUtil->deleteFiles($fileName, $pathRoot, $fileUtil);
    }

    $message = $fileUtil->getMsg();
    echo json_encode(array('code'=>200, 'msg'=>'删除成功'));
    $info = $fName .' 删除成功!'."\r\n";//utf
    error_log($message.$info, 3, './logs/operLog.txt');
    
}else if($_POST['ajax'] == 'ajaxEdit'){
    //5修改json文件
    $fileUtil = new FileUtil();
    $contents = $_POST['contents'];
    
    $contents=trim($contents, "\xEF\xBB\xBF");//去除BOM头
    $path = $_POST['path'];
    $path = rtrim($path, ',/');
    json_decode($contents);
    
    if(json_last_error() !== JSON_ERROR_NONE){//echo json_last_error();echo JSON_ERROR_NONE;
        echo json_encode(array('code'=>400, 'msg'=>'不是json格式的字符串'));
        exit();
    }
    
    $path = $fileUtil->checkiconv($path, 'utf2gbk');
    //echo $path;die;
    $res = file_put_contents($path, $contents);
    $message = $fileUtil->getMsg();
    $fileName = $fileUtil->checkiconv($path, 'gbk2utf');
    if($res !== FALSE){
        echo json_encode(array('code'=>200, 'msg'=>'保存成功'));
        $info=$fileName.'修改成功'."\r\n";
        error_log($message.$info, 3, './logs/operLog.txt');
    }else{
        echo json_encode(array('code'=>400, 'msg'=>'保存文件出错！'));
        $info=$fileName.'修改失败！'."\r\n";
        error_log($message.$info, 3, './logs/operLog.txt');
    }
}

$fileUtil = new FileUtil();
?>