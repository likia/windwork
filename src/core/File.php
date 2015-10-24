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
 * 文件及目录
 *
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.file.html
 * @since       1.0.0
 */
class File {
	/**
	 * 文件安全路径
	 * 过滤掉文件路径中非法的字符
	 * @param string $path
	 * @return string
	 */
	public static function safePath($path) {
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('/(\.+\\/)/', './', $path);
		$path = preg_replace('/(\\/+)/', '/', $path);
		return $path;
	}
	
	/**
	 * 文件安全目录
	 * 过滤掉目录名称中非法的字符
	 * @param string $dir
	 * @return string
	 */
	public static function safeDir($dir) {
		$dir = static::safePath($dir);
		$dir = rtrim($dir, '/') . '/';
		return $dir;
	}
	
	/**
	 * 删除文件夹里的所有文件和文件夹，但保留该文件夹。
	 * @param string $dir
	 */
	public static function clearDir($dir) {
		static::removeDirs($dir, false);
	}
	
	/**
	 * 写入数据
	 * @param string $file
	 * @param string $content
	 * @param int $flag 0|LOCK_EX|FILE_APPEND 
	 *   0：覆盖写入
	 *   LOCK_EX：锁定后写入再解锁
	 *   FILE_APPEND：追加写入
	 *   LOCK_EX|FILE_APPEND：加锁追加写入
	 * @return bool
	 */
	public static function write($file, $content, $flag = 0) {
		$file = static::safePath($file);
		if (!is_dir(dirname($file))) {
			@mkdir(dirname($file), 0755, true);
		}
		
		if (!is_scalar($content)) {
			throw new \core\Exception('$content的数据类型错误！只能写入标量数据。');
		}
		
		return file_put_contents($file, $content, $flag);
	}
	
	/**
	 * 读取文件内容
	 * @param string $file
	 * @return string|null 如果读取不出文件内容则返回null
	 */
	public static function read($file) {
		$file = static::safePath($file);
		if(is_readable($file)) {
			return file_get_contents($file);
		}
		
		return null;
	}
	
	/**
	 * 删除文件
	 * @param string $file
	 * @return bool
	 */
	public static function delete($file) {
		$file = static::safePath($file);
		if(is_readable($file)) {
			return @unlink($file);
		} elseif (!is_file($file)) {
			return true;
		}
		
		return false;
	}

	/**
	 * 删除文件夹（包括有子目录或有文件）
	 *
	 * @param string $dir 目录
	 * @return bool
	 */
	public static function removeDirs($dir, $rmSelf = true) {
		// 不处理非法路径
		$dir = File::safeDir($dir);
	
		if(!$dir || !$d = dir($dir)) {
			return;
		}

		$do = true;
		while (false !== ($entry = @$d->read())) {
			if($entry[0] == '.') {
				continue;
			}
			if (is_dir($dir.'/'.$entry)) {
				$do = $do && static::removeDirs($dir.'/'.$entry, true);
			} else {
				$do = $do && false !== @unlink($dir.'/'.$entry);
			}
		}
			
		@$d->close();
		$rmSelf && @rmdir($dir);
		
		return $do;
	}
}

