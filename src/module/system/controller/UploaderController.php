<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\controller;

use core\Common;
use core\mvc\Message;
use core\Factory;
use core\Config;
use core\Storage;

/**
 * 系统默认页面
 * 
 * @package     module.system.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
 class UploaderController extends \core\mvc\Controller {
 	/**
 	 * 
 	 * @var \module\system\model\UploadModel
 	 */
 	private $m = null;

 	public function __construct(){
 		parent::__construct();
 		$this->m = new \module\system\model\UploadModel();
 		$this->m->imgWatermark = Config::get('watermark_enabled');
		
		$this->initView();
 	}

 	public function createAction(){
 		$type   = $this->request->getRequest('type');
 		$rid    = $this->request->getRequest('rid');
 		$from   = $this->request->getRequest('from');
 		$name   = $this->request->getRequest('name'); // 表单文件域名称
 		$reName = $this->request->getRequest('rename'); // 文件显示名称为
 		 		
 		// KindEditor 文件类型用dir参数区分（dir=image|flash|media|file）
 		if ($from == 'editor') {
 			$type = $this->request->getRequest('dir');
 		}
 			
 		if ($type) {
 			$this->m->type = $type;
 			$type == 'album' && $type = 'image';
 		}
 		
 		if ($type == 'image') {
 			$this->m->isCheckImageType = true;
 		}

 		$this->m->uid = $_SESSION['uid'];
 		$rid && $this->m->rid = $rid;
 		$name || $name = 'file';
 		
 		$r = Message::getMessages();
 		if (!empty($_POST['base64_image'])) {
 			$content = base64_decode($_POST['base64_image']);
 			$this->m->setContent($content);
 			//$this->m->setName($reName ? $reName : $file['name']);
	 		//$this->m->setTempFile();
	 		//$this->m->setTempName();
	 		$mime = '';
	 		$bytes6=substr($content, 0, 6);
			if ($bytes6) {
				if (substr($bytes6, 0 ,3) == "\xff\xd8\xff") {
					$mime = 'image/jpeg';
					$this->m->setTempName('tmp.jpg');
				} else if ($bytes6 == "\x89PNG\x0d\x0a") {
					$mime = 'image/png';
					$this->m->setTempName('tmp.png');
				} else if ($bytes6 == "GIF87a" || $bytes6 == "GIF89a") {
					$mime = 'image/gif';
					$this->m->setTempName('tmp.gif');
				}
			} 
			
			if (!$mime) {
				Message::setErr('错误的文件格式，只允许上传“jpg/jpeg/png/gif”文件');
				$this->showMessage();
				return ;
			}
			
			$this->m->setMime($mime);
	 		$this->m->setSize(strlen($content));	 		
	 		// $this->m->setErrno();	 			
	 		// $this->m->note = $this->request->getRequest('note');
	 			
	 		if($this->m->create()){
	 			$r['uploadfile_response'] = array(
	 				'id'    => $this->m->getPkv(),
	 				'rid'   => $rid,
 					'path'  => $this->m->getPath(),
	 				'url'   => $this->m->getUrl(),
	 				'name'  => $this->m->getName(),
	 			);
	 			
	 			$this->m->getIsImage() && 
	 			$r['uploadfile_response']['thumb'] = \core\Factory::storage()->getThumbUrl($this->m->getPkv(), 150, 150);
	 			$r['ok'] = '成功上传附件';
	 			$this->m->toArray();
	 		} else {
	 			$r = $this->m->getLastErr();
	 			Message::setErr($this->m->getErrs());
	 		}
	 		
	 		$this->showMessage($r);
	 		return;
 		} else if ($_FILES) {
 			$this->m->setIsUploadFile(true);
 			$count = 0;
 			$file = &$_FILES[$name];
 			if (empty($file['tmp_name'])) {
 				Message::setErr('请选择要上传的文件');
 			} elseif (is_array($file['tmp_name'])) {
 			    $r['uploadfiles_response'] = array();
 			    
 				foreach ($file['tmp_name'] as $key => $tmp) {
 					if (!$file['tmp_name'][$key]) {
 						continue;
 					}

 					$this->m->setTempFile($file['tmp_name'][$key]);
 					$this->m->setMime($file['type'][$key]);
 					$this->m->setTempName($file['name'][$key]);
 					$this->m->setSize($file['size'][$key]);
 					$this->m->setErrno($file['error'][$key]);
 					
 					if($this->m->create()){
 						$r['uploadfiles_response'][] = array(
	 						'id'    => $this->m->getPkv(),
		 					'rid'   => $rid,
 							'thumb' => \core\Factory::storage()->getThumbUrl($this->m->getPkv(), 150, 150),
	 						'path'  => $this->m->getPath(),
 							'path'  => $this->m->getUrl(), 								
	 						'name'  => $this->m->getName(),
	 					);
 						$count ++;
 					} else {
 						Message::setErr($this->m->getErrs());
 					}
 				}
 				
 				if ($count) {
 					Message::setOK('成功上传'.$count.'个文件');
 				}
 			} else {
 				$this->m->setName($reName ? $reName : $file['name']);
	 			$this->m->setTempFile($file['tmp_name']);
	 			$this->m->setTempName($file['name']);
	 			$this->m->setMime($file['type']);
	 			$this->m->setSize($file['size']);
	 			$this->m->setErrno($file['error']);
	 			
	 			$this->m->note = $this->request->getRequest('note');
	 			
	 			if($this->m->create()){
	 				if($from == 'editor') {
	 					$r = array(
	 						"error" => 0,
	 						"url" => \core\Factory::storage()->getFullUrl($this->m->getPath()),
	 					);
	 					Common::showJson($r);
	 					return true;
	 				} else {
		 				$r['uploadfile_response'] = array(
		 					'id'    => $this->m->getPkv(),
		 					'rid'   => $rid,
	 						'path'  => $this->m->getPath(),
		 					'url'   => $this->m->getUrl(),
		 					'name'  => $this->m->getName(),
		 				);
		 				
		 				$this->m->getIsImage() && 
		 				$r['uploadfile_response']['thumb'] = \core\Factory::storage()->getThumbUrl($this->m->getPkv(), 150, 150);
		 				
		 				$this->m->toArray();
		 				Message::setOK('成功上传附件');	 						
	 				}
	 			} else {
	 				$r = $this->m->getLastErr();
	 				Message::setErr($this->m->getErrs());
	 			} 					
 				
 			} 			
			
			if ($this->request->isAjaxRequest()) {
				$this->showMessage($r);
				return ;
			}
		}
		
 		$this->view->render(); 	
 	}
 	
	public function updateAction($id = 0){
	    $id = (int)$id;
    
		if(!$id || !$this->m->setPkv($id)->load()) {
			$this->err404();
			return false;
		}
		
		// 会员只允许修改自己的附件
		else if (empty($_SESSION['isadmin']) && $m->uid != $_SESSION['uid']) {
			$this->err403();
			return false;
		}
		
		if ($this->request->isPost()) {			
	 		$type   = $this->request->getRequest('type');
	 		$rid    = $this->request->getRequest('rid');
	 		$from   = $this->request->getRequest('from');
	 		$name   = $this->request->getRequest('name'); // 表单文件域名称
	 		$reName = $this->request->getRequest('rename'); // 文件显示名称为
	 		
	 		// KindEditor 文件类型用dir参数区分（dir=image|flash|media|file）
	 		if ($from == 'editor') {
	 			$type = $this->request->getRequest('dir');
	 		}
	 		 		
	 		if ($type) {
	 			$this->m->type = $type;
	 			$type == 'album' && $type = 'image';
	 		}
	 		
	 		if ($type == 'image') {
	 			$this->m->setAllowUploadTypes('gif,jpg,jpeg,png,bmp');
	 		}
	 		
	 		$rid && $this->m->rid = $rid;
	 		$name || $name = 'file';
	 		
	 		$r = array();
	 		if ($_FILES) {
	 			$this->m->setIsUploadFile(true);
	 			$count = 0;
	 			$file = &$_FILES[$name];
	 			
	 			if (is_array($file['tmp_name'])) {
	 			    $r['uploadfiles_response'] = array();
	 			    
	 				foreach ($file['tmp_name'] as $key => $tmp) {
	 					if (!$file['tmp_name'][$key]) {
	 						continue;
	 					}
	
	 					$this->m->load();
	 					$this->m->setTempFile($file['tmp_name'][$key]);
	 					$this->m->setMime($file['type'][$key]);
	 					$this->m->setTempName($file['name'][$key]);
	 					$this->m->setSize($file['size'][$key]);
	 					$this->m->setErrno($file['error'][$key]);
	 					
	 					if($this->m->update()){
	 						$r['uploadfiles_response'][] = array(
		 						'id'    => $this->m->getPkv(),
	 							'thumb' => \core\Factory::storage()->getThumbUrl($this->m->getPkv(), 150, 150),
		 						'path'  => $this->m->getPath(),
		 						'url'   => \core\Factory::storage()->getUrl($this->m->getPath()),
		 						'name'  => $this->m->getName(),
		 					);
	 						$count ++;
	 					} else {
	 						Message::setErr($this->m->getErrs());
	 					}
	 				}
	 				
	 				if ($count) {
	 					Message::setOK('成功上传'.$count.'个文件');
	 				}
	 			} else {	 				
	 				if ($type && strpos($file['type'], $type)) {
	 					Message::setErr('错误的文件格式，允许上传的文件格式为：'.Config::get('upload_allow_type'));
	 				} else {
	 					$this->m->load();
	 					if (!$file['error']) {
			 				$this->m->setTempFile($file['tmp_name']);
			 				$this->m->setTempName($file['name']);
			 				$this->m->setMime($file['type']);
			 				$this->m->setSize($file['size']);
	 					}

	 					$this->m->setErrno($file['error']);
		 				$this->m->setName($reName ? $reName : $file['name']);
		 				$this->m->note = $this->request->getRequest('note');
		 				
		 				if(false !== $this->m->update()){
		 					if($from == 'editor') {
		 						$r = array(
		 							"error" => 0,
		 							"url" => Factory::storage()->getFullUrl($this->m->getPath()),
		 						);
		 						Common::showJson($r);
		 						return true;
		 					} else {
			 					$r['uploadfile_response'] = array(
			 						'id'    => $this->m->getPkv(),
		 							'thumb' => Factory::storage()->getThumbUrl($this->m->getPkv(), 150, 150),
			 						'path'  => $this->m->getPath(),
			 						'url'   => Factory::storage()->getUrl($this->m->getPath()),
			 						'name'  => $this->m->getName(),
			 						'rid'   => $this->m->uuid,
			 					);
			 					$this->m->toArray();
			 					Message::setOK('成功修改附件');	 						
		 					}
		 				} else {
		 					$r = $this->m->getLastErr();
		 					Message::setErr($this->m->getErrs());
		 				} 					
	 				}
	 			} 
	 			
				if ($this->request->isAjaxRequest()) {
					$this->showMessage($r);
					return ;
				}
			}
		}
		
		$this->m->load();
		
		if ($this->request->isAjaxRequest()) {
			$r = array(
				'id' => $this->m->getPkv(),
				'path' => $this->m->getPath(),
				'url'  => Factory::storage()->getUrl($this->m->getPath()),
				'name' => $this->m->getName(),
				'note' => $this->m->getNote(),
				'isimage' => $this->m->getIsImage(),
			);
			
			if ($this->m->getIsImage()) {
				$r['thumb'] = thumb($this->m->getPkv(), 150, 150);
			}
			
			$this->showMessage($r);
			return;
		}
		
		$this->view->assign('item', $this->m->toArray());
		$this->view->render();	
	}

	/**
	 * 删除附件
	 * 会员只允许删除自己的附件，
	 * 商家可以删除自己和自己所管理的服务号的附件，
	 * 管理员可以删除所有附件
	 * 
	 * @param number $id
	 */
	public function deleteAction($id = 0){
	    $id = (int)$id;
	    
	    if ($_SESSION['isadmin']) {
	    	$this->m->setPkv($id);
	    	if ($this->m->delete()) {
	    		Message::setOK('成功删除文件');
	    	} else {
	    		Message::setErr($this->m->getLastErr());
	    	}
	    } else {
			// 会员只允许修改自己的附件
			if ($this->m->deleteByIdUid($id, $_SESSION['uid'])) {
				Message::setOK('成功删除文件');
			} else {
				Message::setErr($this->m->getLastErr());
			}
		} 
		
		$this->showMessage();	
	}
	
	public function loadAction($path = 0, $p1 = null, $p2 = null, $p3 = null){
		$nopic = Config::get('base_path').Config::get('ui_nopic');
		$storObj = Factory::storage();
		
		// 头像 storage/avatar/big|medium|small/{$uid}.jpg
		if ($path == 'avatar') {
			$nopic = Config::get('ui_davatar');
			$avatarUid = (int)$p2;
			if ($avatarUid <= 0) {
				$this->response->sendRedirect($nopic);
				return;
			}
			
			if(!in_array($p1, array('big', 'medium', 'small', 'tiny'))) {
				$p1 = 'small';
			}
			
			$avatarPath = "avatar/{$p1}/{$avatarUid}.jpg";
			if ($storObj->isExist($avatarPath)) {
				$storObj->load($avatarPath);
				return;
			}
			
			// 微信头像
			$wxAvatarCacheKey = "wxavatar/{$p1}/{$avatarUid}";
			if (Factory::cache()->read($wxAvatarCacheKey)) {
				$wxAvatar = Factory::cache()->read($wxAvatarCacheKey);
				header('Location: ' . $wxAvatar);
				return;
			}
			
			// 
			$userObj = new \module\user\model\UserModel();
			
			if (!$userObj->setPkv($avatarUid)->load() || !$userObj->avatar) {
				$this->response->sendRedirect($nopic);
				return;
			}
			
			// 微信头像缓存设置
			if (false !== strpos($userObj->avatar, 'http://wx.qlogo.cn/')) {
				$wxAvatar = trim($userObj->avatar, '0');
				switch ($p1) {
					case 'tiny':
						$wxAvatar .= '46';
						break;
					case 'small':
						$wxAvatar .= '96';
						break;
					case 'medium':
						$wxAvatar .= '132';
						break;
					default:
						$wxAvatar .= '0';
						break;
				}
				
				Factory::cache()->write($wxAvatarCacheKey, $wxAvatar);
				header('Location: ' . $wxAvatar);
				return;
			}
			
			if(!$storObj->isExist($userObj->avatar)) {
				$this->response->sendRedirect($nopic);
				return;				
			}
			
			if($p1 == 'big') {
				$thumbWidth = $thumbHeight = 400;
			} else if ($p1 == 'medium') {
				$thumbWidth = $thumbHeight = 160;
			} else if ($p1 == 'tiny') {
				$thumbWidth = $thumbHeight = 50;
			} else {
				$thumbWidth = $thumbHeight = 100;
			}
			
			try {
				// 读取图片path
				$thumbObj = Factory::image();
				$thumbSrc = $storObj->getRealPath($userObj->avatar);
				
				$thumbObj->setImage($thumbSrc);
				if(false !== $thumbObj->thumb($thumbWidth, $thumbHeight, $storObj->getRealPath($avatarPath))) {
					$storObj->load($avatarPath);
				} else {
					$this->response->sendRedirect($nopic);
				}
			} catch (\core\Exception $e) {
				Message::setErr($e->getMessage(), 404);
				$this->showMessage();
				return false;
			}
		} elseif ($path == 'thumb') {
			$params = $this->request->getGet('...');
			$path = implode('/', $params);  // 
			$path = str_replace('../', '', $path);
			
			// 取得图片id和尺寸
			if(preg_match("/^(.+?)\\$(.+?)\\.jpg$/", basename($path), $m)) {
				$imgId = \core\util\Encoder::decode($m[1]);
				$size  = \core\util\Encoder::decode($m[2]);
				if (preg_match("/^([0-9]+)x([0-9]+)$/", $size, $m2)) {
					$width = $m2[1];
					$height = $m2[2];
				} else {
					$width = 100;
					$height = 100;
				}
				
				// 尺寸限制
				$width && $width < 50 && $width = 50;
				$width > 1000 && $width = 1000;
				$height < 0 && $height = 0;
				$height && $height < 50 && $height = 50;
				$height > 1600 && $height = 1600;
				// TODO 限制尺寸在规定的设置里面				
			} else {
				$this->response->sendRedirect($nopic);
				return;
			}
			
			if (!$storObj->isExist($path)) {
				$source = $storObj->getPathFromUrl($imgId);
				if(!$storObj->isExist($source)) {
					$uploadObj = new \module\system\model\UploadModel();
					if(!$uploadObj->setPkv($imgId)->load()) {
						$this->response->sendRedirect($nopic);
						return;
					}

					$source = $uploadObj->getPath();
				}
				
				$imageObj = Factory::image();
				$source = $storObj->getRealPath($source);
				try{
					$imageObj->setImage($source);
					if(!$imageObj->thumb($width, $height, $storObj->getRealPath($path))) {
						$this->response->sendRedirect($nopic);
						return;
					}
				} catch (\core\Exception $e) {
					$this->response->sendRedirect($nopic);
					return;
				}
			} 
			
			$storObj->load($path);
		} else if (is_numeric($path)) {
			$uploadObj = new \module\system\model\UploadModel();
			if(!$uploadObj->setPkv($path)->load()) {
				$this->response->sendRedirect($nopic);
				return;
			}
			
			$storObj->load($uploadObj->getPath());
		}		
	}
	
	/**
	 * 我的附件列表
	 * @return boolean
	 */
	public function listAction() {
		$from = $this->request->getRequest('from');
		$type = $this->request->getRequest('type');
		$dir  = $this->request->getRequest('dir');
		$dir && $type = $dir;
		
		if($this->request->isPost() && $batchecked = $this->request->getRequest('batchecked')) {
			foreach ($batchecked as $UploadId) {
				$this->m->setPkv($UploadId);
				if(false === $this->m->delete()) {
					Message::setErr($this->m->getErrs());
					break;
				}
			}
		}
		
		$whArr = array();
		if($type) {
			$whArr[] = array("is{$type}", 1);
		}
		
		// 费管理员只列出自己上传的图片
		empty($_SESSION['isadmin']) && $whArr[] = array('uid', $_SESSION['uid']);
		
		$cdt = array(
			'where' => $whArr,
			'order' => 'displayorder ASC, id DESC',
		);
		
		$total = $this->m->count($cdt);
		$paging = new \core\util\Pagination();
		$paging->setVar($total, 15);
		
		$list  = $this->m->select($cdt, $paging->offset, $paging->rows);
		
		if ($from == 'editor') {
			$fileList = array();
		
			foreach ($list as $file) {
				$fileList[] = array(
					'datetime' => date('Y-m-d H:i:s', $file['dateline']),
					'is_photo' => $file['isimage'],
					'icon_url' => $file['isimage'] ? thumb($file['id'], 100, 100) : '',
					'dir_path' => dirname(Factory::storage()->getUrl($file['path'])),
					'is_dir'   => false,
					'has_file' => false,
					'filesize' => $file['size'],
					'filename' => Factory::storage()->getUrl($file['path']),
					'filetype' => strtolower(pathinfo($file['path'], PATHINFO_EXTENSION)),
				);
			}
		
			$result = array();
			//相对于根目录的上一级目录
			$result['moveup_dir_path'] = '';
			//相对于根目录的当前目录
			$result['current_dir_path'] = '';
			//当前目录的URL
			$result['current_url'] = Config::get('storage_site_url') ? '' : Config::get('base_path');
			//文件数
			$result['total_count'] = $total;
			//文件列表数组
			$result['file_list'] = $fileList;
		
			Common::showJson($result);
		
			return true;
		} else {
		
			$this->initView();
			foreach ($list as $li => $lv) {
				$list[$li]['url'] = Factory::storage()->getFullUrl($lv['path']);
			}
		
			$this->view->assign('pager', $paging->getPager());
			$this->view->assign('list', $list);
			$this->view->render();
		}
	}
}
