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
 * 图像处理接口
 *
 * @package     core.adapter.image
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.image.html
 * @since       1.0.0
 */
interface IImage {
	
	/**
	 * 设置图片的完整真实路径
	 * 
	 * @param string $src
	 * @return \core\adapter\image\Image
	 */
	public function setImage($src);
	
	/**
	 * 生成缩略图
	 * 宽度不小于$thumbWidth或高度不小于$thumbHeight的图片生成缩略图
	 * 缩略图和被提取缩略图的文件放于同一目录，文件名为“被提取缩略图文件.thumb.jpg”
	 *
	 * @param int $thumbWidth
	 * @param int $thumbHeight
	 * @param string $thumbPath 缩略图完整路径 
	 */
	public function thumb($thumbWidth, $thumbHeight, $thumbPath);
	
	/**
	 * 给图片打水印
	 * 建议用gif或png图片做水印，jpg不能设置透明，故不用
	 *
	 * @param string $watermarkFile 水印图片
	 * @param int $watermarkPlace 水印放置位置 1:左上, 2：中上， 3右上, 4：左中， 5：中中， 6：右中，7：左下， 8：中下，9右下
	 * @param int $watermarkQuality  被打水印后的新图片(相对于打水印前)质量百分比
	 */
	public function watermark($watermarkFile = 'static/images/watermark.png', $watermarkPlace = 9, $watermarkQuality = 75);
}
