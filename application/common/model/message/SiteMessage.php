<?php
namespace app\common\model\message ;

use app\common\model\BaseModel;
use app\common\model\UserModel;
use think\Db;

class SiteMessage extends BaseModel {
	
	/**
	 * 消息类型：系统消息
	 * @var integer
	 */
	const MESSAGE_TYPE_SYSTEM = 1 ;
	/**
	 * 消息类型：交易提醒
	 * @var integer
	 */
	const MESSAGE_TYPE_TRADE = 2 ;
	
	private $messageConfig, $userModel, $pageItemsCount;
	
	protected $table = "sm_site_message" ;
	
	public function __construct() {
		parent::__construct() ;
		$this->messageConfig = new MessageConfig() ;
		$this->userModel = new UserModel() ;
		$this->pageItemsCount = config('message')['page_items'] ;
	}
	
	public function getMessageList ($uid, $page=1, $type=0, $unreadOnly=false) {
		$where['I_userID'] = $uid ;
		if ($unreadOnly) {
			$where['I_read'] = 0 ;
		}
		if (0 != $type) {
			$where['I_type'] = $type ;
		}
		$data = Db::table($this->table)->where($where)->order('I_read,id desc')
					->page($page, $this->pageItemsCount)->select() ;
		$count = Db::table($this->table)->where($where)->value('count(*)') ;
		return [
				'data'		=>		$data,
				'count'		=>		$count,
				'current'	=>		$page,
				'size'		=>		$this->pageItemsCount,
		] ;
	}
	
	public function setRead ($mids = []) {
		$ids = implode(',', $mids) ;
		$this->update(['I_read'=>1],"id in ($ids)") ;
	}
	
	public function getContent ($uid, $mid) {
		$message = Db::table($this->table)
				->where(['I_userID'=>$uid, 'id'=>$mid, 'state'=>parent::DEFAULT_STATUS_NORMAL])->find();
		if ($message) {
			$this->update(['I_read'=>1],['id'=>$mid]) ;
			return $message ;
		}
	}
	
	public function doSend ($uid, $tpl, $param, $urlParam=[]) {
		$tplConfig = $this->messageConfig->getSiteMessageTplByName($tpl) ;
		$content = vsprintf($tplConfig['content'],$param) ;
		$siteUrl = config('site_url') ;
		$domain = substr($siteUrl, strpos($siteUrl, '//')+2) ;
		$url = url($tplConfig['url'], $urlParam, true, $domain) ;
		return $this->create([
				'I_userID'		=>		$uid,
				'Vc_template'	=>		$tpl,
				'Vc_title'		=>		$tplConfig['title'] ,
				'Vc_content'	=>		$content,
				'Vc_url'		=>		$url,
				'I_read'		=>		0 ,
				'I_type'		=>		$tplConfig['type'] ,
		]) ;
	}
	
	public static function setReaded ($ids) {
		if (is_array($ids) && count($ids) > 0) {
			$ids = implode(',', $ids) ;
			$sql = "update sm_site_message set I_read=1 where id in ($ids)" ;
			Db::query($sql) ;
		}
	}
	
}