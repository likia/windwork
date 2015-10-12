<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\controller\admin;

use core\Common;
use core\mvc\Message;
use core\Config;
use core\Storage;

/**
 * 附件管理
 * 
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class UploadsController extends \module\system\controller\admin\AdminBase {
	/**
	 *
	 * @var \module\system\model\UploadModel
	 */
	private $m = null;
	
	public function __construct(){
		parent::__construct();
		parent::initView();
		
		$this->m = new \module\system\model\UploadModel();
		$this->m->imgWatermark = Config::get('watermark_enabled');
	}
	
	public function listAction(){
		$from = $this->request->getRequest('from');
		$type = $this->request->getRequest('type');
		$dir  = $this->request->getRequest('dir');
		$dir && $type = $dir;
	
		if($this->request->isPost() && $batchecked = $this->request->getRequest('batchecked')) {
			foreach ($batchecked as $UploadId) {
				$this->m->setObjId($UploadId);
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
					'dir_path' => dirname(Storage::getInstance()->getUrl($file['path'])),
					'is_dir'   => false,
					'has_file' => false,
					'filesize' => $file['size'],
					'filename' => Storage::getInstance()->getUrl($file['path']),
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
				$list[$li]['url'] = Storage::getInstance()->getFullUrl($lv['path']);
			}
	
			$this->view->assign('pager', $paging->getPager());
			$this->view->assign('list', $list);
			$this->view->render();
		}
	}

	/**
	 *
	 * @param string $rid
	 */
	public function getAlbumByRidAction($rid = '') {
		$r = array(
			'album' => array(),
		);
	
		if (!$rid) {
			Message::setErr('错误的参数');
		} else {
			$r['album'] = $this->m->getAlbumByRid($rid, 100);
		}
	
		$this->showMessage();
	}
	
	/**
	 * 设置图片类型
	 * @param int $id
	 * @param string $type
	 */
	public function setImageTypeAction($id = 0, $type = '') {
		if (!$id || !$type) {
			die('Error Params');
		}
	
		if(!in_array($type, array('album', 'image', 'content'))) {
			$type = 'content';
		}
	
		if($this->m->setImageType($id, $type)) {
			Message::setOK('修改图片类型成功');
		} else {
			Message::setErr($this->m->getLastErr());
		}
	
		$this->showMessage();
	}
	
	/**
	 * @todo 设置相册封面
	 */
	public function setAlbumCoverAction() {
		
	}
}
