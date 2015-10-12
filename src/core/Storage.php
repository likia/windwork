<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core;

/**
 * 存贮操作
 * 长久保存可以从网络访问的数据 
 * 
 * 附件存贮规范：
 * 如果附件服务器使用和网站不一样的域名，在配置文件中设置附件网址(storage_site_url)，如新浪云的Storage存贮
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.storage.html
 * @since       1.0.0
 */
class Storage {
	
	/**
	 * 存贮站点根目录
	 * 
	 * @var string
	 */
	private $storageDir = '';
	
	/**
	 * 附件网站根目录
	 * 
	 * @var string
	 */
	private $siteUrl = '';
	
	/**
	 * instances
	 * @var array
	 */
	private static $instances = array();
	
	/**
	 * constructor
	 */
	private function __construct() {
	}
	
	/**
	 * 获取存贮类实例
	 * @param string $namespace
	 * @return \core\Storage
	 */
	public static function getInstance($namespace = '') {
		$namespace || $namespace = '*';
		
		if (!isset(static::$instances[$namespace])) {
			static::$instances[$namespace] = new static();

			// 从配置注入存贮目录和存贮站点URL
			static::$instances[$namespace]
			  ->setStorageDir(\core\Config::get('storage_dir'))
			  ->setSiteUrl(\core\Config::get('storage_site_url'));
		}
		
		return static::$instances[$namespace];
	}

	/**
	 * 设置存贮目录（支持wrapper）
	 * @param string $dir
	 * @return string
	 */
	public function setStorageDir($dir) {
		$this->storageDir = rtrim($dir, '/');
		
		return $this;
	}
	
	/**
	 * 获取存贮目录
	 * @return string
	 */
	public function getStorageDir() {
		return $this->storageDir;
	}
	
	/**
	 * 设置附件存贮站点网址
	 * @param string $url
	 */
	public function setSiteUrl($url) {
		$this->siteUrl = rtrim($url, '/');

		return $this;
	}

	/**
	 * 获取附件存贮站点网址
	 */
	public function getSiteUrl() {
		return $this->siteUrl;
	}
	
	/**
	 * 文件上传的附件是否是保存为静态文件
	 * 
	 * @return boolean
	 */
	public function isStatic() {
		$isStatic = false;
		
		if(!preg_match("/^(\\w+)\\:\\/\\//", $this->storageDir, $match) || 
		  in_array($match[1], array('ftp', 'http', 'file', 'https', 'saestor'))) {
			$isStatic = true;
		}
		
		return $isStatic;
	}
			
	/**
	 * 获取附件的路径
	 * 使用两种路径：相对路径和完整路径
	 * 当程序设置使用完整路径的时候就全部使用完整路径，否则在PHP脚本中用header跳转的，一律用完整路径，前端页面用相对路径
	 * 
	 * @todo 兼容各种地址（重写、不重写、有无子目录）
	 * @param string $path
	 * @return string
	 */
	public function getUrl($path) {
		$path = $this->getPathFromUrl($path);

		if ($this->siteUrl) {
			$url = trim($this->siteUrl, '/') . '/' . $path;
		} else {
			$url = "storage/" . trim($path, '/');
		}
		
		// 附件为动态内容并且没有使用url rewrite
		if (!preg_match("/^(\\w+)\\:\\/\\//", $this->siteUrl) && !$this->isStatic()) {
			 $url = Config::get('base_url') . Common::ltrimStr($url, Config::get('base_url'));
		}
		
		return $url;
	}
	
	/**
	 * 从URL获取文件存贮的path
	 * @param string $url
	 * @return string
	 */
	public function getPathFromUrl($url) {
		return preg_replace("/(.*storage\/)/", '', $url);
	}
	
	/**
	 * 获取附件完整网址
	 * 
	 * @param string $path
	 * @return string
	 */
	public function getFullUrl($path) {
		$url = $this->getUrl($path);
						
		if(!preg_match("/^(\\w+)\\:\\/\\//", $url)) {		
			$domain = Config::get('host_info');
			$domain = rtrim($domain, '/');
			$basePath = Config::get('base_path');			
			$url = "{$domain}{$basePath}{$url}";
		}
		
		return $url;		
	}
	
	/**
	 * 获取上传文件（在存贮介质上）的真实路径 {$wrapper}$path
	 * 
	 * @param string $path
	 * @param string $type 
	 * @return string
	 */
	public function getRealPath($path) {
		$path = $this->safePath($path);
		$path = "{$this->storageDir}/{$path}";
		
		return $path;
	}
	
	/**
	 * 获取缩略图相对URL，在显示图片时使用
	 *
	 * @param int $imgId
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public function getThumbUrl($path, $width, $height){
		if (!$path) {
			$url = Config::get('ui_nopic');
			if(!preg_match("/^(\\w+)\\:\\/\\//", $url)) {				
				$domain = Config::get('static_site_url');
				$domain || $domain = Config::get('host_info');
				$domain = rtrim($domain, '/');
				$basePath = Config::get('base_path');
				$url = "{$domain}{$basePath}{$url}";
			}
		} else {
			$path = $this->getThumbPath($path, $width, $height);
			$url = $this->getFullUrl($path);
		}
		
		return $url;
	}
	
	/**
	 * 删除附件
	 * @param string $path
	 */
	public function remove($path) {
		return @unlink($this->getRealPath($path));
	}
	
	/**
	 * 删除缩略图
	 * 
	 * @param string $path 缩略图路径
	 */
	public function removeThumb($path) {		
		// 缩略图路径
		$thumbDir = dirname($this->getThumbPath($path, 1, 1));
		$thumbDir = $this->getRealPath(trim($thumbDir, '/')) . '/';

		if(!is_dir($thumbDir)) {
			return false;
		}
		
		$baseId = \core\util\Encoder::encode($path);
		$d = dir($thumbDir);
		
		while (false !== ($entry = $d->read())) {
			if ($entry[0] == '.') {
				continue;
			}
			
			if(false !== $pos = strpos($entry, $baseId.'$')){
				@unlink($thumbDir.'/'.$entry);
			}
		}
		
		$d->close();
	}
	
	/**
	 * 删除所有缩略图
	 *
	 */
	public function clearThumb() {
		File::clearDir($this->getRealPath('thumb'));
	}
	
	/**
	 * 获取缩略图路径
	 * 
	 * @param string $path
	 * @param int $width
	 * @param int $height
	 */
	public function getThumbPath($path, $width, $height) {
		$path = $this->getPathFromUrl($path);
		
		$id = \core\util\Encoder::encode($path) . '$' . \core\util\Encoder::encode("{$width}x{$height}");
		$sub = sprintf("%x", crc32($path));
		$path = "thumb/{$sub[0]}{$sub[1]}/{$sub[2]}{$sub[3]}/{$id}.jpg";
		
		return $path;
	}
		
	/**
	 * 载入文件以显示
	 * 
	 * @param string $path
	 */
	public function load($path) {
		$path = $this->safePath($path);
		
		if(!$this->isExist($path)) {
			throw new Exception('附件不存在。', Exception::ERROR_HTTP_404);
		}
		
		if ($this->isStatic()) {
			header('Location: '.$this->getFullUrl($path));
			return true;
		} else {
				
			$pos = strrpos($path, '.');
			
			if(false !== $pos) {
				$mimes = array(
					'css'  => 'text/css',
					'js'   => 'text/javascript',
					'htm'  => 'text/html',
					'html' => 'text/html',
					'jpg'  => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'gif'  => 'image/gif',
					'png'  => 'image/png',
					'tiff' => 'image/tiff',
					'tif'  => 'image/tif',
					'ico'  => 'image/ico',
					'svg'  => 'image/svg+xml',
					'pdf'  => 'application/pdf',
					'doc'  => 'application/msword',
					'rtf'  => 'application/rtf',
					'xls'  => 'application/vnd.ms-excel',
					'ppt'  => 'application/vnd.ms-powerpoint',
					'rar'  => 'application/x-rar-compressed',
					'swf'  => 'application/x-shockwave-flash',
					'zip'  => 'application/zip',
					'msi'  => 'application/octet-stream',
					'exe'  => 'application/octet-stream',
					'mid'  => 'application/midi',
					'midi' => 'application/midi',
					'kar'  => 'application/midi',
					'mp3'  => 'application/mpeg',
					'3gp'  => 'application/3gpp',
					'3gpp' => 'application/3gpp',
					'mpg'  => 'application/mpeg',
					'mpeg' => 'application/mpeg',
					'mov'  => 'application/quicktime',
					'flv'  => 'application/x-flv',
					'mng'  => 'application/x-mng',
					'asx'  => 'application/x-ms-asx',
					'asf'  => 'application/x-ms-asf',
					'wmv'  => 'application/x-ms-wmv',
					'avi'  => 'application/x-ms-avi',
				);
			
				$ext = substr($path, $pos+1);
			
				if(isset($mimes[$ext])) {
					$mime = $mimes[$ext];
				} else {
					$mime = 'application/octet-stream';
				}
				
				header("Content-Type: {$mime}");
			}
			
			header('Cache-Control: public, max-age=2592000');
			header('Pragma: public');
			header('Expires: '.date('D, d M Y H:i:s', time()+2592000). ' GMT');
			header('Last-Modified: '.date('D, d M Y H:i:s', mktime(0, 0, 1, 1, 1, 2000)). ' GMT');
			
			print $this->getContent($path);
			
			return true;
		}
	}
	
	/**
	 * 获取安全文件路径名
	 * @param string $path
	 * @return string
	 */
	public function safePath($path) {
		return File::safePath($path);
	}

	/**
	 * 读取内容
	 *
	 * @param string $path
	 * @return string
	 */
	public function getContent($path) {
		return file_get_contents($this->getRealPath($path));
	}
	
	/**
	 * 存贮附件
	 * 
	 * @param string $path
	 * @param string $content
	 * @return bool
	 */
	public function save($path, $content) {
		$path = $this->getRealPath($path);
		if (!is_dir(dirname($path))) {
			@mkdir(dirname($path), 0755, true);
		}
		
		return file_put_contents($path, $content);
	}
	
	/**
	 * 生成附件路径
	 * @param string $suffix
	 * @throws Exception
	 * @return string
	 */
	public function generatePath($suffix) {
		if (!$suffix) {
			throw new Exception('请设置后缀名参数');
		}
		
		$path = date(\core\Config::get('upload_subdir_format'));
		$path = $path . '/' . \core\Common::guid(16) . '.' . ltrim($suffix, '.');
		
		if($this->isExist($path)) {
			$path = $this->generatePath($suffix);
		}
		
		return $path;
	}

	/**
	 * 上传文件
	 * @param string $tempFile
	 * @param string $uploadPath
	 */
	public function upload($tempFile, $uploadPath) {
		$uploadPath = $this->getRealPath($uploadPath);
		if (!is_dir(dirname($uploadPath))) {
			@mkdir(dirname($uploadPath), 0755, true);
		}
		return move_uploaded_file($tempFile, $uploadPath);
	}
	
	/**
	 * 复制文件到附件目录
	 * @param string $pathFrom 来源文件完整的路径（注意该文件路径的安全）
	 * @param string $pathTo
	 * @return boolean
	 */
	public function copy($pathFrom, $pathTo) {
		$pathTo = $this->getRealPath($pathTo);
		
		if (!is_dir(dirname($pathTo))) {
			@mkdir(dirname($pathTo), 0755, true);
		}
	
		return copy($pathFrom, $pathTo);
	}
	
	/**
	 * 附件是否存在
	 * @param string $path
	 * @return boolean
	 */
	public function isExist($path) {
		return is_file($this->getRealPath($path));
	}
}


