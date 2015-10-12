<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\ad\model;

use core\mvc\Exception;

use core\Factory;

class AdPlaceModel extends \core\mvc\Model {
	protected $table = 'wk_ad_place';

	public function create() {		
		if (empty($this->name)) {
			$this->setErr('请输入广告位名称。');
			return false;
		}
		
		$this->dateline = time();
		
		$do = parent::create();
		$do && static::clearCache();
		
		return $do;
	}

	public function update() {
		if (empty($this->name)) {
			$this->setErr('请输入广告位名称。');
			return false;
		}
		
		$do = parent::update();
		$do && static::clearCache();
		
		return $do;
	}
	
	/**
	 * 获取广告位列表
	 * @return array
	 */
	public function getList() {
		$rs = $this->select(array('order' => 'id'), 0, 999);
		$list = array();
		foreach ($rs as $r) {
			$list[$r['id']] = $r;
		}
		
		return $list;
	}
	
	public function delete() {
		$do = parent::delete();
		if ($do) {
			$r = new AdPlaceRModel();
			$r->deleteByAdId($this->id);
			static::clearCache();
		}
		
		return $do;
	}

	/**
	 * 获取广告位的HTML
	 *
	 * @param array $id 广告位id
	 */
	public function getPlaceHtml(){
		if (!$this->loaded && !$this->load()) {
			throw new Exception('广告位不存在');
		}
		
		$adObj = new AdModel();
		$ads = $adObj->getAdsByPlaceId($this->id, 99);
	
		if ($this->mode == 'rand'){
			$ads = array($ads[array_rand($ads)]);
		}
	
		$html = '';
		foreach ($ads as $ad) {
			$adContent = $ad['content'];
			$itemHtml = '';
			switch ($ad['type']){
				case 'text':
					$itemHtml = "<a class=\"ad-text-item\" target=\"_blank\" href=\"{$adContent['url']}\" style=\"font-size:{$adContent['size']}\">{$adContent['text']}</a>";
					break;
				case 'html':
					$itemHtml = "<div class=\"ad-html-item\">{$adContent['code']}</div>";
					break;
				case 'flash':
					$itemHtml = "<embed class=\"ad-flash-item\" height=\"{$this->height}\" width=\"{$this->width}\" wmode=\"transparent\" type=\"application/x-shockwave-flash\" quality=\"high\" src=\"{$adContent['flash']}\" />";
					break;
				case 'image':
					empty($adContent['alt']) && $adContent['alt'] = $ad['name'];
					$itemHtml = "<a class=\"ad-image-item\" target=\"_blank\" href=\"{$adContent['url']}\"><img src=\"{$adContent['src']}\" width=\"{$this->width}\" height=\"{$this->height}\" alt=\"{$adContent['alt']}\" title=\"{$ad['name']}\" align=\"middle\" /></a>";
					break;
				default:
					break;
			}
			
			if ($itemHtml && $this->mode == 'all') {
				$itemHtml = "<li>{$itemHtml}</li>";
			}
			
			$html .= $itemHtml;
		}
		
		if ($this->mode == 'all') {
			$html = "<ul>{$html}</ul>";
		}
	
		return $html;
	}
	
	public static function clearCache() {
		Factory::cache()->clear('ad');
	}
}