<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\storage;

/**
 * 存贮文件（用户上传的附件）操作
 * 长久保存可以从网络访问的数据 
 * 
 * 附件存贮规范：
 * 如果附件服务器使用和网站不一样的域名，在配置文件中设置附件网址(storage_site_url)，如新浪云的Storage存贮
 * 
 * @package     core.adapter.storage
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.storage.html
 * @since       1.0.0
 */
class File extends AStorage {

	/**
	 * 支持通过wrapper访问存贮
	 * @param array $cfg
	 */
	public function __construct(array $cfg) {
		parent::__construct($cfg);
		
		// 支持wrapper
		if (false != ($wrapper = \core\Wrapper::getWrapperFromPath($cfg['storage_dir']))) {
			\core\Wrapper::registerWrapper($wrapper);
		}
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
		\core\File::clearDir($this->getRealPath('thumb'));
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


