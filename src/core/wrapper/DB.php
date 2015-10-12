<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\wrapper;

/**
 * 文件存贮在数据库中，通过自定义Wrapper访问
 * 
 * @package     core.wrapper
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */
class DB implements IWrapper {
	private $dirFiles = array();
	private $dirPos = 0;
	
	/**
	 * 文件指针是否指向文件结尾
	 * @var bool
	 */
	private $eof = false;
	
	/**
	 * 文件指针位置
	 * @var int
	 */
    private $position = 0;

	private $uri = null;
	private $content = null;
	
	/**
	 * 文件的信息
	 * @var array
	 */
	private $stat = array();
	
	/**
	 * 文件存贮缓冲
	 * @var blob
	 */
	private $buffer = '';
	
	/**
	 * wrapper名，不带://
	 * @var string
	 */
	public static $wrapper = 'db';
	
	public static function register() {
		stream_register_wrapper(static::$wrapper, __CLASS__);
	}
	
	/**
	 * 带://的wrapper
	 * @return string
	 */
	protected static function wrapper() {
		return static::$wrapper . '://';
	}
	
	/**
	 * 在数据库中存贮的uri
	 * @param string $path
	 * @return string
	 */
	private static function path($path) {
		$pos = strpos($path, static::wrapper());
		if(!$pos && false !== $pos) {
			$path = substr($path, strlen(static::wrapper()));
		}
		
		return $path;
	}
	
	/**
	 * 文件打开模式
	 * 'r' 只读方式打开，将文件指针指向文件头。
	 * 'r+' 读写方式打开，将文件指针指向文件头。  
	 * 'w' 写入方式打开，将文件指针指向文件头并将文件大小截为零。如果文件不存在则尝试创建之。  
	 * 'w+' 读写方式打开，将文件指针指向文件头并将文件大小截为零。如果文件不存在则尝试创建之。  
	 * 'a' 写入方式打开，将文件指针指向文件末尾。如果文件不存在则尝试创建之。  
	 * 'a+' 读写方式打开，将文件指针指向文件末尾。如果文件不存在则尝试创建之。  
	 * 'x' 创建并以写入方式打开，将文件指针指向文件头。如果文件已存在，
	 *     则 fopen()调用失败并返回 FALSE，并生成一条 E_WARNING 级别的错误信息。
	 *     如果文件不存在则尝试创建之。这和给底层的 open(2) 系统调用指定 O_EXCL|O_CREAT 标记是等价的。
	 * 'x+' 创建并以读写方式打开，将文件指针指向文件头。如果文件已存在，
	 *      则 fopen()调用失败并返回 FALSE，并生成一条 E_WARNING 级别的错误信息。
	 *      如果文件不存在则尝试创建之。这和给 底层的 open(2) 系统调用指定 O_EXCL|O_CREAT 标记是等价的。
	 * 
	 * @var string
	 */
	private $mode;
	
	/**
	 * 
	 * @return \core\adapter\db\ADB
	 */
	private static function db() {
		return \core\Factory::db();
	}
	
	function __construct() {		
	}
	
	/**
	 * 关闭打开的文件夹指针
	 *
	 * @return bool
	 */
	public function dir_closedir() {
		// free stored directory content
		if (is_array($this->dirFiles)) {
			$this->dirFiles = false;
			$this->dirPos = 0;
		}
	}

	/**
	 * 打开文件夹
	 *
	 * @param string $path
	 * @param int $options
	 * @return bool
	 */
	public function dir_opendir($path , $options) {
		$this->dirFiles = array();
		$this->dirPos = 0;
		
		$path = static::path($path);		
		$path = rtrim($path, '/') . '/%';
		
		$rows = static::db()->getAll("SELECT uri FROM wk_storage WHERE uri LIKE %s", array($path));
			
		foreach (@$rows as $row) {
	        $this->dirFiles[] = static::wrapper().$row['uri'];
	    }
	
		return true;
	}

	/**
	 * 读取文件夹中的文件列表
	 *
	 * @return string
	 */
	public function dir_readdir() {
		// bailout if directory is empty
		if (!is_array($this->dirFiles)) {
			return false;
		}
		
		// bailout if we already reached end of dir
		if ($this->dirPos >= count($this->dirFiles)) {
			return false;
		}
		
		// return an entry and move on
		return $this->dirFiles[$this->dirPos++];
	}

	/**
	 * 文件夹指针指向开头
	 *
	 * @return bool
	 */
	public function dir_rewinddir() {
		// bailout if directory content info has already
		// been freed
		if (!is_array($this->dirFiles)) {
			return false;
		}
		
		// rewind to first entry
		$this->dirPos = 0;
	}

	/**
	 * 
	 *
	 * @param string $path
	 * @param int $mode
	 * @param int $options
	 * @return bool
	 */
	public function mkdir($path , $mode , $options) {
		return true;
	}

	/**
	 * 修改文件名
	 *
	 * @param string $path_from
	 * @param string $path_to
	 * @return bool
	 */
	public function rename($path_from , $path_to) {
		if($path_from == $path_to) {
			return false;
		}
		
		$path_from = static::path($path_from);
		$path_to   = static::path($path_to);
		
		// 检查源文件是否存在
		$isSrcExists = static::db()->getOne("SELECT COUNT(*) FROM wk_storage WHERE uri = %s LIMIT 1", array($path_from));
		if (!$isSrcExists) {
			return false;
		}
		
		// 检查目标文件是否存在
		$isDistExists = static::db()->getOne("SELECT COUNT(*) FROM wk_storage WHERE uri = %s LIMIT 1", array($path_to));
		if ($isDistExists) {
			return false;
		}
		
		return false !== static::db()->exec("UPDATE wk_storage SET uri = %s WHERE uri = %s", array($path_to, $path_from));
	}

	/**
	 * 删除文件夹
	 *
	 * @param string $path
	 * @param int $options
	 * @return bool
	 */
	public function rmdir($path , $options) {
		$path = static::path($path);
		$path = rtrim($path, '/') . '/%';
		
		return false !== static::db()->exec("DELETE FROM wk_storage WHERE uri LIKE %s", array($path));
	}

	/**
	 * 
	 *
	 * @param int $cast_as
	 * @return resource
	 */
	public function stream_cast($cast_as) {
		
	}

	/**
	 * 
	 */
	public function stream_close() {
		$this->uri = null;
		$this->eof = true;		
	}

	/**
	 * 
	 * @return bool
	 */
	public function stream_eof() {
		return $this->eof;
	}

	/**
	 * 将缓冲内容持久化
	 *
	 * @return bool
	 */
	public function stream_flush() {
        $r = true;
		if($this->buffer) {
	        $buffer = $this->buffer;
	        $path   = static::path($this->uri);
	        
	        $this->buffer = '';
	        
	        $sql = "REPLACE INTO wk_storage (uri, content, size, ctime, atime, mtime) VALUE (%s, %s, %i, %i, %i, %i)";
	        $r = static::db()->exec($sql, array(
	                	$path,
	                	$buffer,
	                	$this->stat['size'],
	                	$this->stat['ctime'],
	                	$this->stat['atime'],
	                	$this->stat['mtime'],
	                ));
	        return $r;
		}
		
		return $r;
	}

	/**
	 * 读写锁定（数据库有锁，无需实现）
	 *
	 * @param mode $operation
	 * @return bool
	 */
	public function stream_lock($operation) {
		return false;
	}

	/**
	 * 打开文件流
	 *
	 * @param string $path
	 * @param string $mode
	 * @param int $options
	 * @param string &$opened_path
	 * @return bool
	 */
	public function stream_open($path , $mode , $options , &$opened_path) {
		$path = static::path($path);
		$row = static::db()->getRow("SELECT * FROM wk_storage WHERE uri = %s", array($path));
		
		if($row) {
			// x || x+
			if (strpos($mode, "x") !== false) {
				trigger_error($path. ' existed!', E_WARNING);
				return false;
			}
						
            $this->content = $row['content'];
            unset($row['content']);
            unset($row['uri']);

            $this->stat = $row;
		} else if (!preg_match('|[aw\+]|', $mode)) {
			// 是否是文件夹
			$path = rtrim($path, '/') . '/%';
			$sql = "SELECT * FROM wk_storage WHERE uri LIKE %s ORDER BY atime DESC LIMIT 1";
			$row = static::db()->getRow($sql, array($path));
			
			if($row) {
	            unset($row['content'], $row['uri']);
	            $row['mode'] = 040000;
	
	            $this->stat = $row;
			} else {
				$this->eof = true;
				return false;
			}
        }
        
        //追加写模式
		if (strpos($mode, "a") !== false) {
			$this->position = (int)$this->stat['size'];
			$this->eof = true;
		}
		
		$this->mode = $mode;
		$this->uri = $path;
		
		return true;
	}

	/**
	 * 读取文件流内容
	 *
	 * @param int $count
	 * @return string
	*/
	public function stream_read($count) {
		if($this->eof) {
			return false;
		}
		
		// 以写入方式打开，不允许读
		if($this->mode == 'a' || $this->mode == 'w' || $this->mode == 'x') {
			return false;
		}
		
        $start = $this->position;
        $end   = $start + $count - 1;

        $data = substr($this->content, $start, $count);

        //是否已到结尾
        if($end >= $this->stat['size'] -1) {
            $this->eof = true;
            //指针位置移动到结尾
            $this->position = $this->stat['size'] - 1;
        } else {
            //指针位置移动到下一个位置
            $this->position = $end + 1;
        }

        return $data;
    }

	/**
	 * 
	 *
	 * @param int $offset
	 * @param int $whence = SEEK_SET
	 * @return bool
	*/
	public function stream_seek($offset , $whence = SEEK_SET) {
        switch ($whence) {
            case SEEK_SET:
                // absolute position
                $this->position = $offset;
                break;
            case SEEK_CUR:
                // relative position
                $this->position += $offset;
                break;
            case SEEK_END:
                // relative position form end
                $this->position = $this->stat['size'] + $offset;
                break;
            default:
                return false;
                break;
        }

        $this->eof = false;

        return true;
    }

	/**
	 * Enter description here...
	 *
	 * @param int $option
	 * @param int $arg1
	 * @param int $arg2
	 * @return bool
	*/
	public function stream_set_option($option , $arg1 , $arg2) {
        // none
    }

	/**
	 * Enter description here...
	 *
	 * @return array
	*/
	public function stream_stat() {
        return $this->stat;
    }

	/**
	 * 获取当前文件指针所在位置
	 *
	 * @return int
	 */
	public function stream_tell() {
        return $this->position;
    }

	/**
	 * 写入文件
	 *
	 * @param string $data
	 * @return int
	 */
	public function stream_write($data) {
		// 只读
		if($this->mode == 'r') {
			return false;
		}
		
        if(!$this->buffer && strpos($this->mode, "a") !== false) {
        	// 追加写
        	$this->buffer = $this->content . $data;
        } else {
        	$this->buffer .= $data;
        }
        
        $this->content = $this->buffer;
        
        $time   = time();
        
        $this->stat = array(
        	'dev'     => isset($this->stat['dev'])   ? $this->stat['dev']   : 3,
        	'ino'     => isset($this->stat['ino'])   ? $this->stat['ino']   : 0,
        	'mode'    => isset($this->stat['mode'])  ? $this->stat['mode']  : 33206,
        	'nlink'   => isset($this->stat['nlink']) ? $this->stat['nlink'] : 1,
        	'uid'     => isset($this->stat['uid'])   ? $this->stat['uid']   : 0,
        	'gid'     => isset($this->stat['gid'])   ? $this->stat['gid']   : 0,
        	'rdev'    => isset($this->stat['rdev'])  ? $this->stat['rdev']  : 3,
	        'size'    => strlen($this->buffer),
	        'ctime'   => empty($this->stat['ctime']) ? $time : $this->stat['ctime'],
	        'atime'   => $time,
	        'mtime'   => $time,
        	'blksize' => isset($this->stat['blksize']) ? $this->stat['blksize'] : -1,
        	'blocks'  => isset($this->stat['blocks'])  ? $this->stat['blocks']  : -1,
        );
        
        //$this->position = $this->stat['size'] - 1;
        
        return strlen($data);
    }

	/**
	 * 删除文件
	 *
	 * @param string $path
	 * @return bool
	 */
	public function unlink($path) {
        $path = static::path($path);
        return false !== static::db()->exec("DELETE FROM wk_storage WHERE uri = %s", array($path));
    }

	/**
	 * 文件或文件夹信息
	 *
	 * @param string $path
	 * @param int $flags
	 * @return array
	*/
	public function url_stat($path , $flags) {
        // we map this one to open()/stat()/close()
        // there won't be much gain in inlining this
		$dummy = null;
        if (!$this->stream_open($path, "r", array(), $dummy)) {
            return false;
        }

        $stat =  $this->stream_stat();
        $this->stream_close();

        return $stat;
    }
}
