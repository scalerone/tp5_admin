<?php
namespace app\home\controller ;

use app\common\controller\HomeController;
class FileUpload extends HomeController {
	
     public function _initialize() {
         $uid=$this->getSessionUid();
         if(empty($uid)){
             exit("非法上传！");
         }
     }
    
	public function doUpload() {
		//获取参数
		$path = config('img_path') ;
		//获取文件
		$files = request()->file() ;
		//设置保存路径
		$result = [] ; $savePath = date('Ymd') ;
		//多文件
		if ($files && is_array($files)) {
			foreach ($files as $file) {
				//文件上传
				$info = $file->move($path) ;
				$result[] = [
					'name'		=>		$info->getFilename(),
					'extension'	=>		$info->getExtension(),
					'path'		=>		$info->getPath(),
					'realPath'	=>		$info->getRealPath(),
					'size'		=>		$info->getSize(),
					'savePath'	=>		$savePath,
					'saveName'	=>		$savePath . '/' . $info->getFilename() ,
				] ;
			}
		}
		return getJsonStrSucNoMsg($result) ;
	}
	
}