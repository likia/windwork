<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\model;

use core\Common;
use core\Factory;
use core\Config;
use core\Storage;
use core\Lang;

Lang::add('upload');

/**
 * 系统选项模型
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class UploadModel extends \core\mvc\Model {
	public $imgWatermark = false;
	public $isCheckImageType = false;

	protected $allowSize = 2048000;
	
	/**
	 * 允许上传的文件类型
	 * @var array
	 */
	protected $allowUploadTypes = array();
	
	protected $isUploadFile = true;

	protected $path = null;

	protected $tempFile = null;
	protected $tempName = '';
	protected $content = null;
	
	protected $mime = null;
	protected $name = null;
	protected $size = 0;
	protected $errno = 0;

	protected $isImage = 0;
	protected $isAudio = 0;
	protected $isVideo = 0;
	protected $isFlash = 0;
	protected $isFile  = 0;

	/**
	 */
	protected $table = 'wk_upload';
	
	protected $fieldMap = array(
		'isimage' => 'isImage',
		'isaile'  => 'isAudio',
		'isvideo' => 'isVideo',
		'isflash' => 'isFlash',
		'isfile'  => 'isFile', 
		'path'    => 'path', 
		'mime'    => 'mime', 
		'name'    => 'name',
		'size'    => 'size'
	);
		
	public function __construct($id = null) {
		$this->setAllowUploadTypes(Config::get('upload_allow_type'));
		$this->setAllowSize(Config::get('upload_max_size'));
		
		parent::__construct($id);
	}
	
	/**
	 * 设置图片类型
	 * @param int $id
	 * @param string $type
	 * @return boolean
	 */
	public function setImageType($id, $type) {
		if(!$id || !$type) {
			$this->setErr('错误的参数');
			return false;
		} else {
			$obj = $this;
			$obj->setObjId($id);
			$obj->alterField(array('type' => $type));
		}
		
		static::clearCache();
		
		return true;
	}
	
	public function create() {
		$this->dateline  = time();
		
		if(!($this->upload())) {
			return false;
		}
		
		$this->name = Common::stripTags($this->name);
		$this->note = Common::stripTags($this->note);
		
		static::clearCache();
		
		return parent::create();
	}
	
	public function update() {
		$oldObj = new self();
		$oldObj->setObjId($this->id);
		
		if(!$this->errno && $this->upload()) {			
			$oldObj->load();
			Storage::getInstance()->removeThumb($oldObj->getObjId());
			Storage::getInstance()->remove($oldObj->path);
		}

		$this->name = Common::stripTags($this->name);
		$this->note = Common::stripTags($this->note);
		
		$do = parent::update();
		
		if($do && $this->isImage) {
			Storage::getInstance()->removeThumb($this->id);
			static::clearCache();
		}
		
		return $do;
	}
	
	protected function upload() {
		$msgs[UPLOAD_ERR_OK]           = Lang::get('upload_err_ok');
		$msgs[UPLOAD_ERR_INI_SIZE]     = Lang::get('upload_err_ini_size');
		$msgs[UPLOAD_ERR_FORM_SIZE]    = Lang::get('upload_err_form_size');
		$msgs[UPLOAD_ERR_PARTIAL]      = Lang::get('upload_err_partial');
		$msgs[UPLOAD_ERR_NO_FILE]      = Lang::get('upload_err_no_file');
		$msgs[UPLOAD_ERR_NO_TMP_DIR]   = Lang::get('upload_err_no_tmp_dir');
		$msgs[UPLOAD_ERR_CANT_WRITE]   = Lang::get('upload_err_cant_write');

		if ($this->errno != UPLOAD_ERR_OK) {
			$this->setErr($msgs[$this->errno]);
			return false;
		}
		
		if (!$this->tempFile && !$this->content) {
			throw new \LogicException('请设置要上传的文件');
		}
		
		empty($this->uid) && $this->uid = $_SESSION['uid'];
		
		// 上传文件后缀
		$suffix = pathinfo($this->tempName, PATHINFO_EXTENSION);
		$suffix = strtolower($suffix);
				
		// 不允许上传没有后缀的文件
		if (empty($suffix)) {
			$this->setErr(Lang::get('upload_noext_denied'));
			return false;
		}		
				
		// 大小
		if(/*filesize($this->tempFile)*/ $this->size > $this->allowSize) {
			$this->setErr('文件大小不允许超过'.$this->allowSize.'K');
			return false;
		}
		
		// 如果是图片则加载允许上传的图片格式
		if ($this->isCheckImageType) {
			$this->setAllowUploadTypes(Config::get('upload_allow_image'));
		}
		
		//检查上传文件的类型
		if ($this->tempName == $suffix || !$this->isAllowType($suffix)) {
			$this->setErr(Lang::get('upload_denied_type').', '.Lang::get('upload_allow_type_is'). implode(',', $this->allowUploadTypes));
			return false;
		}
		
		if ($this->isUploadFile && $this->tempFile && !is_uploaded_file($this->tempFile)) {
			$this->setErr(Lang::get('upload_file_has_err'));
			return false;
		}

		$uploadPath = Storage::getInstance()->generatePath($suffix);
		// 保存文件
		if ($this->content) {
			if(!Storage::getInstance()->save($uploadPath, $this->content)) {
				$this->setErr(Lang::get('upload_file_save_err'));
				return false;				
			}
		} elseif($this->isUploadFile && !Storage::getInstance()->upload($this->tempFile, $uploadPath)) {
			$this->setErr(Lang::get('upload_file_move_err'));
			return false;
		} elseif (!$this->isUploadFile && Storage::getInstance()->copy($this->tempFile, $uploadPath)) {
			$this->setErr(Lang::get('upload_file_copy_err'));
			return false;
		}
		
		// 打水印
		if (in_array(strtolower($suffix), array('jpg', 'gif', 'png')) && $this->imgWatermark) {
			$img = Config::get('watermark_img'); // 水印图
			$pos = Config::get('watermark_pos'); // 水印位置
			$qlt = Config::get('watermark_quality'); // 水印质量
			if(!$img) {
				$img = 'static/images/logo_water.png';
			}
		
			$distFile = Storage::getInstance()->getRealPath($uploadPath);
			$image = Factory::image();
			$image->setImage($distFile);
			$image->watermark($img, $pos, $qlt);
		}
		
		return ($this->path = $uploadPath);
	}
	
	public function isAllowType($ext) {
		return in_array($ext, $this->allowUploadTypes);
	}
	
	/**
	 * @return string $mime
	 */
	public function getMime() {
		return $this->mime;
	}

	/**
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string 
	 */
	public function getTempFile() {
		return $this->tempFile;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getTempName() {
		return $this->tempName;
	}

	/**
	 * @return int $size bit
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return int $errno
	 */
	public function getErrno() {
		return $this->errno;
	}

	/**
	 * @return bool $isUploadFile
	 */
	public function getIsUploadFile() {
		return $this->isUploadFile;
	}

	/**
	 * @return int $allowSize bit
	 */
	public function getAllowSize() {
		return $this->allowSize;
	}

	/**
	 * @return string $allowUploadTypes
	 */
	public function getAllowUploadTypes() {
		return $this->allowUploadTypes;
	}

	/**
	 * @param bool $isUploadFile
	 */
	public function setIsUploadFile($isUploadFile) {
		$this->isUploadFile = $isUploadFile;
	}

	/**
	 * @param number $allowSize
	 */
	public function setAllowSize($allowSize) {
	    if(stripos($allowSize, 'M')) {
			$allowSize = floatval($allowSize)*1024*1024;
		}
		if(stripos($allowSize, 'k')) {
			$allowSize = floatval($allowSize)*1024;
		}
		
		$allowSize = (int)$allowSize;
		
		$this->allowSize = $allowSize;
	}

	/**
	 * @param multitype: $allowUploadTypes
	 */
	public function setAllowUploadTypes($allowUploadTypes) {
		if (is_string($allowUploadTypes)) {
			$allowUploadTypes = explode(',', trim(strtolower($allowUploadTypes)));
		}
		
		$this->allowUploadTypes = $allowUploadTypes;
	}

	protected function setMimeFromFile() {
		// 图片
		if(false !== ($size = @getimagesize($this->tempFile))) {
			$this->mime = $size['mime'];
			$this->isImage = 1;
		} elseif (!$this->tempName){
			throw new \core\mvc\Exception('请先设置$this->tempName');
		} else {
			$ext = pathinfo($this->tempName, PATHINFO_EXTENSION);
			if(false !== stripos('|flv|avi|rm|mov|ram|mpeg|mpg|asf|mp4|rmvb|wmv|dat|qt|', "|$ext|")) {
				// 视频
				$this->isVideo = 1;
			} elseif(false !== stripos('|mp3|wma|wav|mid|midi|', "|$ext|")) {
				// 音频
			} elseif('flash' == $ext) {
				// Flash
				$this->isFlash = 1;
			} else {
				// 文件
				$this->isFile = 1;
			}
		}
	}
	
	/**
	 * @param string $mime
	 * @todo flash 上传的mime就一个样
	 */
	public function setMime($mime) {
		if ($mime == 'application/octet-stream') {
			if($this->tempFile) {
				$this->setMimeFromFile();
			} else {
				$this->mime = $mime;
			}
		} else {
			$this->mime = $mime;
	
			$this->isImage  = false !== strpos($this->mime, 'image') ? 1 : 0;
			$this->isAudio  = false !== strpos($this->mime, 'audio') ? 1 : 0;
			$this->isVideo  = false !== strpos($this->mime, 'video') ? 1 : 0;
			$this->isFlash  = false !== strpos($this->mime, 'flash') ? 1 : 0;
			$this->isFile   = !($this->isImage || $this->isAudio || $this->isVideo || $this->isFlash);
		}
		return $this;
	}

	/**
	 * 设置文件名称（basename()）
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * 
	 * @param string $tempName
	 * @return \module\system\model\UploadModel
	 */
	public function setTempName($tempName) {
		$this->tempName = $tempName;
		return $this;
	}
	
	/**
	 * @param string $tempFile
	 */
	public function setTempFile($tempFile) {
		$this->tempFile = $tempFile;
		$this->size = filesize($tempFile);
		$this->md5 = md5_file($tempFile);
		
		if ($this->mime == 'application/octet-stream') {
			$this->setMimeFromFile();
		}
		
		return $this;
	}

	/**
	 * @param number $size  bit
	 */
	public function setSize($size) {
		$this->size = $size;
		return $this;
	}

	/**
	 * @param number $errno
	 */
	public function setErrno($errno) {
		$this->errno = $errno;
		return $this;
	}
	
	/**
	 * @return the $path
	 */
	public function getPath() {
		return $this->path;
	}
	
	public function getUrl() {
		return \core\Storage::getInstance()->getUrl($this->getPath());
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = trim($path, '/');
	}

	/**
	 * @return the $content
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @return the $isImage
	 */
	public function getIsImage() {
		return $this->isImage;
	}

	/**
	 * @return the $isAudio
	 */
	public function getIsAudio() {
		return $this->isAudio;
	}

	/**
	 * @return the $isVideo
	 */
	public function getIsVideo() {
		return $this->isVideo;
	}

	/**
	 * @return the $isFlash
	 */
	public function getIsFlash() {
		return $this->isFlash;
	}

	/**
	 * @return the $isFile
	 */
	public function getIsFile() {
		return $this->isFile;
	}

	/**
	 * @param bool $isImage
	 */
	public function setIsImage($isImage) {
		$this->isImage = $isImage;
	}

	/**
	 * @param bool $isAudio
	 */
	public function setIsAudio($isAudio) {
		$this->isAudio = $isAudio;
	}

	/**
	 * @param bool $isVideo
	 */
	public function setIsVideo($isVideo) {
		$this->isVideo = $isVideo;
	}

	/**
	 * @param bool $isFlash
	 */
	public function setIsFlash($isFlash) {
		$this->isFlash = $isFlash;
	}

	/**
	 * @param bool $isFile
	 */
	public function setIsFile($isFile) {
		$this->isFile = $isFile;
	}

	/**
	 * 设置要上传的附件的内容
	 * @param string $content
	 */
	public function setContent($content) {
		$this->size = strlen($content);
		$this->md5 = md5($content);
		$this->content = $content;
	}

	/**
	 * 根据关联id获取图册信息
	 * @param string $rid
	 * @param int $limit
	 * @return array
	 */
	public function getAlbumByRid($rid, $limit = 10) {
		$cacheKey = "upload/getAlbumByRid/{$rid}-{$limit}";
		if(null === $album = Factory::cache()->read($cacheKey)) {
			$whArr = array(
				array('rid', $rid),
				array('isimage', 1),
				array('type', 'album')
			);
			
			$cdt = array(
				'fields' => 'id, name, path, dateline',
				'where' => $whArr,
				'order' => 'displayorder, id'
			);
		
			$album = $this->select($cdt, 0, $limit);
		
			if ($album) {		
				foreach ($album as $key => $val) {
					$album[$key]['url'] = Storage::getInstance()->getUrl($val['path']);
					$album[$key]['fullUrl'] = Storage::getInstance()->getFullUrl($val['path']);
					$album[$key]['thumb'] = thumb($val['path'], 100, 100);
				}
			}
			Factory::cache()->write($cacheKey, $album);
		}
		
		return $album;
	}
	
	/**
	 * 根据相册尺寸计算相册高
	 * @param array $album 
	 * @param int $albumWidth
	 * @return int
	 */
	public static function getAlbumImgHeight($album, $albumWidth) {
		$albumHeight = 0;
		if ($album && is_array($album)) {
			foreach ($album as $albumImg) {
				if(!Storage::getInstance()->isExist($albumImg['path'])) {
					continue;
				}
		
				list($width, $height) = getimagesize(Storage::getInstance()->getRealPath($albumImg['path']));
				$height = $height*$albumWidth/$width;
				$albumHeight = $albumHeight > 0 ? min(array($height, $albumHeight)) : $height;
			}
		}
		
		return (int)$albumHeight;
	}

	/**
	 * 根据关联id获取图片
	 * @param string $rid
	 * @param int $limit
	 * @return array
	 */
	public function getImgByRid($rid, $limit = 10) {
		$cacheKey = "upload/getImgByRid-{$rid}-{$limit}";
		if(null === $images = Factory::cache()->read($cacheKey)) {
			$whArr = array(
				array('rid', $rid),
				array('isimage', 1),
			);
			$cdt = array(
				'fields' => 'id, name, path, dateline, type',
				'where' => $whArr,
				'order' => 'displayorder, id'
			);
		
			$images = $this->select($cdt, $whArr, 0, $limit);
		
			if ($images) {
				foreach ($images as $key => $val) {
					$images[$key]          = $val;
					$images[$key]['url']   = Storage::getInstance()->getUrl($val['path']);
					$images[$key]['thumb'] = thumb($val['id'], 100, 100);
					$images[$key]['isAlbum'] = $val['type'] == 'album';
				}			
			}
			
			Factory::cache()->write($cacheKey, $images);
		}
	
		return $images;
	}
	
	public function delete() {		
		// 从数据库删除
		$do = parent::delete();
		
		if (false !== $do) {			
			$storage = Storage::getInstance();			
			// 删除缩略图
			if($this->isImage) {
				$storage->removeThumb($this->id);	
				$storage->removeThumb($this->getPath());	
				Factory::cache()->clear('upload/getAlbumByRid');		
			}
			// 删除文件
			$storage->remove($this->getPath());
		}
		
		return $do;
	}
	
	/**
	 * 根据附件id和用户ID删除附件，供会员删除自己的附件使用
	 * @param int $id
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteByIdUid($id, $uid) {
		$whArr = array(
			array('id', $id),
			array('uid', $uid),
		);

		if (!$this->loadBy($whArr)) {
			$this->setErr('文件不存在或已经被删除！');
			return false;
		}

		// 从数据库删除
		$do = parent::deleteBy($whArr);
		
		if (false !== $do) {
			$storage = Storage::getInstance();
			// 删除缩略图
			$this->isImage && $storage->removeThumb($this->id);
			// 删除文件
			$storage->remove($this->getPath());
		}
		
		return $do;
	}
		
	/**
	 * 将内容中的外部网站图片下载到服务器上并更换为站内图片地址
	 * 
	 * @param string $content
	 * @param string $rid      关联uuid
	 * @return mixed
	 */
	public static function fetchContentImage($content, $rid = '') {
		$storageSiteUrl = Config::get('storage_site_url');
		if(!empty($content) && preg_match_all("/<img.*?src=[\"'](http:\\/\\/.*?)[\"']/i", $content, $m)) {
			set_time_limit(0);
			$imgArr = $m[1];
			$imgArr = array_unique($imgArr);
			foreach($imgArr as $imgUrl) {
				if($storageSiteUrl && false !== strpos($imgUrl, $storageSiteUrl)) {
					continue;
				}
				
				$opts = array('http' =>
					array(
						'method'  => 'GET',
						'header'  => "User-Agent:Mozilla/5.0 (Windows NT 6.2; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0",
						'timeout' => 30
					)
				);
				
				$http_response_header = array(); // wrapper自动赋值
				if(false != ($imgContent = @file_get_contents($imgUrl, false, stream_context_create($opts)))) {				
					$obj = new static();
					
					// 图片是否已经存在
					if ($obj->loadBy(array(array('isimage', 1), array('md5', md5($imgContent))))) {
						$url = Storage::getInstance()->getUrl($obj->getPath());											
						$content = str_replace($imgUrl, $url, $content);
						continue;
					}
					
					$obj->rid = $rid;
					$obj->isimage = 1;
					$obj->type = 'content';
					$obj->setContent($imgContent);
					$obj->setIsUploadFile(false);
					$obj->imgWatermark = true;
					
					// 补全后缀
					$name = basename($imgUrl);
					$ext  = pathinfo($name, PATHINFO_EXTENSION);
					
					foreach ($http_response_header as $headerLine) {
						if (preg_match("/Content-Type:.*?(image\\/jpeg|image\\/png|image\\/gif)$/i", $headerLine, $m)) {
							$mime = trim($m[1]);
							$obj->setMime($mime);
							if (!$ext) {
								switch ($mime) {
									case 'image/jpeg':
										$name .= '.jpg';
										break;
									case 'image/png':
										$name .= '.png';
										break;
									case 'image/gif':
										$name .= '.gif';
										break;
								}
								
								break;
							}
						}
					} 
					
					$obj->setTempName($name);
					
					if(false !== $obj->create()) {
						$url = Storage::getInstance()->getUrl($obj->getPath());											
						$content = str_replace($imgUrl, $url, $content);
					}
				}
			}
		}
		
		return $content;
	}
	
	/**
	 * 上传一个文件
	 * @param string $file
	 * @param string $type
	 * @return \module\system\model\UploadModel|NULL
	 */
	public static function uploadFile($file, $type = '') {
		if (!empty($file)&& !$file['error']) {
			$uploadObj = new static();
			$uploadObj->setMime($file['type']);
			$uploadObj->setTempName($file['name']);
			$uploadObj->setSize($file['size']);
			$uploadObj->setErrno($file['error']);
			$uploadObj->setTempFile($file['tmp_name']);
			$uploadObj->type = $type;
		
			if($uploadObj->create()) {
				return $uploadObj;
			}
		}
		
		return false;
	}
	
	public static function clearCache() {
		Factory::cache()->clear('upload');
	}
}



