<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\image;

/**
 * 图片处理类，使用GD2生成缩略图和打水印 
 *
 * @package     core.adapter.image
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.image.html
 * @since       1.0.0
 */
class GD implements IImage, \core\adapter\IFactoryAble {
	/**
	 * 图片相关信息
	 * 
	 * @var array
	 */
	protected $imgInfo = '';
	
	/**
	 * 原始图片路径
	 * 
	 * @var string
	 */
	protected $srcFile = '';
	
	/**
	 * 创建图片的函数
	 * 
	 * @var string
	 */
	protected $imageCreateFromFunc = '';
	
	/**
	 * 图片处理使用的库函数
	 * 
	 * @var string
	 */
	protected $imageFunc = '';
	
	/**
	 * 是否是动态gif
	 * 
	 * @var bool
	 */
	protected $animatedGif = 0;
	
	/**
	 * 构造函数中设置内存限制多一点以能处理较大图片
	 */
	public function __construct() {
		@ini_set("memory_limit", "128M");  // 处理大图片的时候要较很大的内存
	}
	
	/**
	 * 初始化图片信息
	 *
	 * @param string $srcFile 原始图片文件真实路径路径
	 * @return \core\adapter\image\ImageAbstract
	 */
	public function setImage($srcFile) {
		if (!function_exists('gd_info')) {
			throw new Exception('你的php没有使用gd2扩展,不能处理图片');
		}
		
		if(!is_file($srcFile) || false == ($this->imgInfo = @getimagesize($srcFile))) {
			throw new Exception('错误的图片文件！');;
		}
		
		$this->imgInfo['size'] = @filesize($srcFile);
		$this->srcFile = $srcFile;
		
		switch(@$this->imgInfo['mime']) {
			case 'image/jpeg':
				$this->imageCreateFromFunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
				$this->imageFunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
				break;
			case 'image/gif':
				$this->imageCreateFromFunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
				$this->imageFunc = function_exists('imagegif') ? 'imagegif' : '';
				break;
			case 'image/png':
				$this->imageCreateFromFunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
				$this->imageFunc = function_exists('imagepng') ? 'imagepng' : '';
				break;
		}
		
		/*
		// 动态gif
		if($this->imgInfo['mime'] == 'image/gif') {
			$fp = fopen($srcFile, 'rb');
			$srcFileContent = fread($fp, $this->imgInfo['size']);
			fclose($fp);
			$this->animatedGif = strpos($srcFileContent, 'NETSCAPE2.0') === false ? 0 : 1;
		}
		//*/
		return $this;
	}

	/**
	 * 生成缩略图
	 * 宽度不小于$thumbWidth或高度不小于$thumbHeight的图片生成缩略图
	 * 缩略图和被提取缩略图的文件放于同一目录，文件名为“被提取缩略图文件.thumb.jpg”
	 *
	 * @param int $thumbWidth
	 * @param int $thumbHeight
	 * @param string $thumbPath 缩略图完整路径
	 */
	public function thumb($thumbWidth, $thumbHeight, $thumbPath) {
		if (!$this->imageFunc) {
			return false;
		}	
		
		$imageCreateFromFunc = $this->imageCreateFromFunc;
		$imageFunc = $this->imageFunc;	

		list($srcW, $srcH) = $this->imgInfo;
		$imgH = $srcH;
		$imgW = $srcW;

		// 如果不是动态gif
		//if(!$this->animatedGif) {
			$attachImage = $imageCreateFromFunc($this->srcFile);
			
			$thumbWidth > 0 || $thumbWidth = $srcW * ($thumbHeight/$srcH);
			$xRatio = $thumbWidth / $imgW;  // 宽比率
			$thumbHeight || $thumbHeight = $imgH*$xRatio;
			
			if ($imgW >= $thumbWidth || $imgH >= $thumbHeight || 
			  ($srcW < $thumbWidth || $srcH < $thumbHeight && ($thumbWidth && $thumbHeight))) {				
				// 高需要截掉
				if(($xRatio * $imgH) > $thumbHeight) {
					$imgH = ($imgW / $thumbWidth) * $thumbHeight;
				} else {
					// 宽需要截掉
					$imgW = ($imgH / $thumbHeight) * $thumbWidth;
				}
			}
			
			// 缩略图一律用jpg格式文件，如果不设置缩略图保存路径则保存到原始文件所在目录
			$thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
			if($this->imgInfo['mime'] == 'image/gif') {
			    imagecolortransparent($attachImage, imagecolorallocate($attachImage, 255, 255, 255));
			} else if($this->imgInfo['mime'] == 'image/png') {			
			    imagealphablending($thumbImage , false);//关闭混合模式，以便透明颜色能覆盖原画布
			    imagesavealpha($thumbImage, true);
			}
			
			// 重采样拷贝部分图像并调整大小到$thumbImage

			$srcX = floor(($srcW - $imgW)/2);
			$srcY = floor(($srcH - $imgH)/2);
			imagecopyresampled($thumbImage, $attachImage ,0, 0, $srcX, $srcY, $thumbWidth, $thumbHeight, $imgW, $imgH);
			
			$thumb = null;
			
			
			ob_start();
			if($this->imgInfo['mime'] == 'image/jpeg') {
				$imageFunc($thumbImage, null, 90);  // 为兼容云存贮设备，这里不直接把缩略图写入文件系统
			} else {
				$imageFunc($thumbImage);
			}
			
			$thumb = ob_get_clean();
			
			if(!$thumb) {
				throw new Exception('无法生成缩略图');
			}
			
			if (!is_dir(dirname($thumbPath))) {
				@mkdir(dirname($thumbPath), 0755, true);
			}
			
			return file_put_contents($thumbPath, $thumb);
		//} else {
		//	return false;
		//}
	}

	/**
	 * 给图片打水印
	 * 建议用gif或png图片做水印，jpg不能设置透明，故不用
	 *
	 * @param string $watermarkFile 水印图片
	 * @param int $watermarkPlace 水印放置位置 1:左上, 2：中上， 3右上, 4：左中， 5：中中， 6：右中，7：左下， 8：中下，9右下
	 * @param int $watermarkQuality  被打水印后的新图片(相对于打水印前)质量百分比
	 */
	public function watermark($watermarkFile = 'static/images/watermark.png', $watermarkPlace = 9, $watermarkQuality = 75) {
		$imageCreateFromFunc = $this->imageCreateFromFunc;
		$imageFunc = $this->imageFunc;
		@list($imgW, $imgH) = $this->imgInfo;
		
		$watermarkInfo	= @getimagesize($watermarkFile);
		$watermarkLogo	= ('image/png' == $watermarkInfo['mime']) ? @imageCreateFromPNG($watermarkFile) : @imageCreateFromGIF($watermarkFile);

		if(!$watermarkLogo) {
			return;
		}

		list($logoW, $logoH) = $watermarkInfo;
		$wmwidth = $imgW - $logoW;
		$wmheight = $imgH - $logoH;

		if(is_readable($watermarkFile) && $wmwidth > 10 && $wmheight > 10 && !$this->animatedGif) {
			switch($watermarkPlace) {
				case 1:
					$x = +5;
					$y = +5;
					break;
				case 2:
					$x = ($imgW - $logoW) / 2;
					$y = +5;
					break;
				case 3:
					$x = $imgW - $logoW - 5;
					$y = +5;
					break;
				case 4:
					$x = +5;
					$y = ($imgH - $logoH) / 2;
					break;
				case 5:
					$x = ($imgW - $logoW) / 2;
					$y = ($imgH - $logoH) / 2;
					break;
				case 6:
					$x = $imgW - $logoW;
					$y = ($imgH - $logoH) / 2;
					break;
				case 7:
					$x = +5;
					$y = $imgH - $logoH - 5;
					break;
				case 8:
					$x = ($imgW - $logoW) / 2;
					$y = $imgH - $logoH - 5;
					break;
				case 9:
					$x = $imgW - $logoW - 5;
					$y = $imgH - $logoH - 5;
					break;
			}

			$dstImage = imagecreatetruecolor($imgW, $imgH);
			imagefill($dstImage, 0, 0, imagecolorallocate($dstImage, 0xFF, 0xFF, 0xFF));			
			$targetImage = @$imageCreateFromFunc($this->srcFile);
			imageCopy($dstImage, $targetImage, 0, 0, 0, 0, $imgW, $imgH);
			imageCopy($dstImage, $watermarkLogo, $x, $y, 0, 0, $logoW, $logoH);

			if($this->imgInfo['mime'] == 'image/jpeg') {
				$imageFunc($dstImage, $this->srcFile, $watermarkQuality);
			} else {
				$imageFunc($dstImage, $this->srcFile);
			}

			$this->imgInfo['size'] = filesize($this->srcFile);
			
			return true;
		}
	}
}