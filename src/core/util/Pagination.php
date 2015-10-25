<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\util;

use \core\App;
use \core\Exception;
use core\Common;

/**
 * 分页类
 * 属性为public类型以便可以灵活使用,
 * 比如把实现$this->setStyle()的功能放在调用该类的页面中(不用extends)完成
 * 
 * @package     core.util
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */
class Pagination {
	/**
	 * 当前页数
	 * 
	 * @var int
	 */
	public $page = 1; 

	/**
	 * 总记录数
	 *
	 * @var int
	 */
	public $totals = 100;
	
	/**
	 * 每页显示记录数
	 *
	 * @var int
	 */
	public $rows = 10;

	
	public $firstPageUrl;
	/**
	 * 最后一页页码，总页数
	 *
	 * @var int
	 */
	public $lastPage;
	public $lastPageUrl;

	/**
	 * 上一页的页码
	 *
	 * @var int
	 */
	public $prePage;
	public $prePageUrl;

	/**
	 * 下一页的页码
	 *
	 * @var int
	 */
	public $nextPage;
	public $nextPageUrl;

	/**
	 * 分页的url查询变量
	 *
	 * @var string
	 */
	public $pageVar = 'page';

	/**
	 * 分页页的uri
	 *
	 * @var string
	 */
	public $uri;
	
	public $maxRows = 100;

	/**
	 * 查询的起始项
	 *
	 * @var int
	 */
	public $offset;

	public $rowsVar = 'rows';
	
	public $req;
	
	public $style = 'simple';
	
	/**
	 * url参数分隔符
	 *
	 * @var string(char)
	 */
	public $argSeparator = '/';
	
	/**
	 * url参数等号符号
	 *
	 * @var string(char)
	 */
	public $argEQSign = '=';
	
	public $rowsAllowCustom = true;
	
	/**
	 * 
	 * @var \core\mvc\Router
	 */
	protected $router = null;
	
	public function __construct() {
		$this->router = new \core\mvc\Router();
	}
	
	public function getPagerComplex() {
		/* 首页 */
		$paginationHtml = "
		  <div class='page_nav'>
		    <a class=paging_total href=\"javascript:;\" class='button' title='总主题数'>共{$this->totals}条</a><a href=\"javascript:;\" class=paging_page_no title='当前页/总页数' class='button'>{$this->page}/{$this->lastPage}</a>
		    <a class=paging_first href=\"{$this->firstPageUrl}\"  class='button'>头页</a>";
		
		/* 前页 */
		if($this->prePage) {
		    $paginationHtml.="<a class=paging_last href=\"{$this->prePageUrl}\" class='button'>上一页</a>";
		} else {
		    $paginationHtml.="<a href=\"javascript:;\" class='button' type='button'>上一页</a>";
		}
		
		/* 后页 */
		if($this->nextPage) {
		    $paginationHtml .= "<a class=paging_next href=\"{$this->nextPageUrl}\"  class='button'>下一页</a>";
		} else {
		    $paginationHtml .= "<a href=\"javascript:;\" class='button'>下一页</a>";
		}
		
		/* 尾页 */
		$paginationHtml .= "<a class=paging_final href=\"{$this->lastPageUrl}\" class='button'>尾页</a>";
		
		/* 每页显示条数 */
		$paginationHtml .= "<span class='paging-rows'>每页显示 <select size='1' onchange='window.location=this.value'>\n";
		
		for($i=10; $i<=100; $i+=10){
			$url = $this->getPageUrl(1, $i);
			
			$paginationHtml .= ($i == $this->rows) ? 
			    "<option value='$url' selected=\"selected\">$i</option>\n" : 
			    "<option value='$url'>$i</option>\n";
		}
		
		$paginationHtml .= "</select> 条</span> ";
		
		/* 下拉跳转列表，循环列出所有页码 */
		$paginationHtml .= " <span class='paging-goto'>跳到第 <select size='1' onchange='window.location=this.value'>\n";
		
		for($i=1; $i<=$this->lastPage; $i++){
			if($i > 1000000) {
				$i += 49999;
			} elseif($i > 100000) {
				$i += 4999;
			} elseif($i > 10000) {
				$i += 499;
			} elseif($i > 1000) {
				$i += 99;
			} elseif($i > 100) {
				$i += 49;
			} elseif($i > 50) {
				$i += 9;
			}
			
			$url = $this->getPageUrl($i);
			
			$paginationHtml .= ($i == $this->page) ?
				 "<option value='$url' selected=\"selected\">$i</option>\n" :
				 "<option value='$url'>$i</option>\n";
		}
				
		$paginationHtml .= "</select> 页</span></div>";
		
		return $paginationHtml;
	}

	/**
	 * 分页赋值
	 *
	 * @param int $total
	 * @param int $rows
	 * @param string $style simple|complex simple：简单导航；complex：复杂导航，
	 * @param string $uri
	 * @param string $pageVar
	 * @param string $rowsVar
	 */
	public function setVar($total, $rows=10, $style = null, $uri='', $pageVar = 'page', $rowsVar = 'rows') {		
		if($uri) {
			$this->router->parseUrl($uri);
		} else {
			$this->router->params = App::getInstance()->getRequest()->getGet();
		}
				
		$this->totals    = $total;
		$this->pageVar   = $pageVar;
		$this->rowsVar   = $rowsVar;
		$this->page      = (int)App::getInstance()->getRequest()->getGet($pageVar);
		$this->rows      = empty($this->router->params[$rowsVar]) ? $rows : $this->router->params[$rowsVar];
		
		// 最多记录数限制
		$this->rows > $this->maxRows && $this->rows = $this->maxRows;

		$style && $this->style = $style;
		$this->page <= 0 && $this->page = 1;
		$this->rowsAllowCustom || $this->rows = $rows;		
		
		/* 页码计算 */
		$this->lastPage  = ceil($this->totals / $this->rows);         // 最后页，也是总页数
		$this->page      = min($this->lastPage, $this->page);              // page值超过最大值时取最大值做page值
		$this->prePage   = ($this->page - 1 > 0) ? ($this->page - 1) : 1;                                // 上一页
		$this->nextPage  = ($this->page == $this->lastPage) ? $this->lastPage : $this->page + 1; // 下一页
		$this->offset    = $this->page ? ($this->page - 1) * $this->rows : 0;              // 查询的起始项
		
		$this->firstPageUrl = $this->getPageUrl(1);
		$this->lastPageUrl  = $this->getPageUrl($this->lastPage);
		$this->thisPageUrl  = $this->getPageUrl($this->page);
		$this->prePageUrl   = $this->getPageUrl($this->prePage);
		$this->nextPageUrl  = $this->getPageUrl($this->nextPage);		
	}
	
	/**
	 * 根据参数生成URL
	 * @param int $page
	 * @param int $rows
	 * @return string
	 */
	private function getPageUrl($page, $rows = null) {
		$router = clone $this->router;
		if ($rows) {
			$router->params[$this->rowsVar] = $rows;			
		}
		
		if($page > 1) {
			$router->params[$this->pageVar] = $page;
		} else {
			unset($router->params[$this->pageVar]);
		}
		
		$url = $router->toUrl(true);
		
		return $url;
	}
	
	/**
	 * 获取导航条html
	 * @throws Exception
	 * @return string
	 */
	public function getPager() {
		if (Common::checkMobile()) {
			$fnc = 'getMobilePager';
		} else {
		    $fnc = "getPager{$this->style}";
		}
		if (!method_exists($this, $fnc)) {
			throw new Exception('错误的参数');
		}
		
		return $this->$fnc();
	}

	protected function getPagerSimple() {		
		/* 首页 */
		$paginationHtml = "<div class='page_nav'>"
				        . "  <a href=\"javascript:;\" class=\"paging_totals button\">共{$this->totals}条</a>"
				        . "  <a href=\"javascript:;\" class=\"paging_page_no button\" title='当前页/总页数'>{$this->page}/{$this->lastPage}页</a>"
		                . "  <a href=\"{$this->firstPageUrl}\" class=\"button paging_first\">头页</a>";

		/* 前页 */
		if($this->prePage) {
			$paginationHtml .= "<a class='paging_last button' href=\"{$this->prePageUrl}\">上一页</a>";
		} else {
			$paginationHtml .= "<a href=\"javascript:;\" class='button' type='button'>上一页</a>";
		}
		
		$numFirst = $this->page - 5;
		if ($numFirst < 1) {
			$numFirst = 1;
		}
		
		$numLast = $numFirst + 9;
		if ($numLast > $this->lastPage) {
			$numLast = $this->lastPage;
		}
		if ($numLast < 1) {
			$numLast = 1;
		}
		
		if ($numLast - $numFirst < 9) {
			$numFirst = $numLast - 9;
			if ($numFirst < 1) {
				$numFirst = 1;
			}
		}
		
		if ($this->lastPage > 1) {
			for ($i = $numFirst; $i <= $numLast; $i++) {
				$current = $i == $this->page ? ' current' : '';
				$url = $this->getPageUrl($i);;
				$paginationHtml .= "<a href='{$url}' class='{$current} button'>{$i}</a>";
			}
		}

		/* 后页 */
		if($this->nextPage) {
			$paginationHtml .= "<a class=\"paging_next button\" href=\"{$this->nextPageUrl}\">下一页</a>";
		} else {
			$paginationHtml.="<a href=\"javascript:;\" class='button'>下一页</a>";
		}

		/* 尾页 */
		$paginationHtml .= "<a class=\"paging_final button\" href=\"{$this->lastPageUrl}\">尾页</a>";

		/* 每页显示条数 */
		$paginationHtml .= "</div>";		
		
		return $paginationHtml;
	}
	
	/**
	 * 提供给js调用的分页信息，需使用json_encode() 编码返回的对象实例
	 * 返回 (object) array(
	 *      'totals' => '',
	 *      'pages'  => '',
	 *      'page'   => '',
	 *      'rows'   => '',
	 *      'offset' => ''
	 *  );
	 *  
	 * @return object
	 */
	public function getObj4Json() {
		$r = array(
		    'totals' => $this->totals,
	        'pages'  => $this->lastPage,
	        'page'   => $this->page,
	        'rows'   => $this->rows,
	        'offset' => $this->offset
		);
		
		return (object)$r;
	}
	
	/**
	 * 获取手机版导航分类
	 */
	public function getMobilePager() {
		/* 首页 */
		$paginationHtml = "<div class='page_nav'>\n<a href=\"{$this->firstPageUrl}\" class=\"button paging_first\">头页</a>\n";
		
		/* 前页 */
		if($this->prePage) {
		    $paginationHtml .= "<a class='paging_last button' href=\"{$this->prePageUrl}\">上一页</a>\n";
		} else {
		    $paginationHtml .= "<a href=\"javascript:;\" class='button' type='button'>上一页</a>\n";
		}
		
		$paginationHtml .= "  <a href=\"javascript:;\" class=\"paging_page_no button\" title='当前页/总页数'>{$this->page}/{$this->lastPage}</a>\n";
		
		/* 后页 */
		if($this->nextPage) {
			$paginationHtml .= "<a class=\"paging_next button\" href=\"{$this->nextPageUrl}\">下一页</a>\n";
		} else {
			$paginationHtml.="<a href=\"javascript:;\" class='button'>下一页</a>\n";
		}
		
		/* 尾页 */
		$paginationHtml .= "<a class=\"paging_final button\" href=\"{$this->lastPageUrl}\">尾页</a>\n";
		
		/* 每页显示条数 */
		$paginationHtml .= "</div>\n";
		
	    return $paginationHtml;
	}
}

