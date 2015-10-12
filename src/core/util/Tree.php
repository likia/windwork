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

/**
 * 树形结构类
 * 树的排序和深度的计算
 * 树结点的结构 array('结点编号', '父结点编号', ... )
 * 
 * 术语：
 *   tree 树
 *   subTree 子数
 *   node 结点
 *   degree 结点的度
 *   leaf 叶子，度为0的结点
 *   child 孩子，结点子树的根
 *   parent 结点的上层结点
 *   level 结点的层次
 *   depth 数的深度，树中结点最大层次数
 *   ancestor 祖先，树叶的顶层父结点
 *   descendant 树的子孙，结点的祖先和子孙不包含结点 本身
 *   path 路径，一个结点到达另一个结点经过的结点
 *   
 *   这里用不上sibling、forest
 * 
 * @package     core.util
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */
class Tree {
	/**
	 * 要处理的结点的数组
	 *
	 * @var array
	 */
	protected $inNodes = array();
	
	/**
	 * 处理后的结点的数组
	 *
	 * @var array
	 */
	protected $outNodes = array();
	
	/**
	 * 结点中表示该结点的父结点id的属性名（下标）
	 *
	 * @var string
	 */
	protected $nodeParentIdKey = 'parent_id';
	
	/**
	 * 结点编号属性名（下标）
	 *
	 * @var int
	 */
	protected $nodeIdKey = 'id';
	
	/**
	 * 树的复杂度
	 *
	 * @var int
	 */
	public $step = 0;
	
	/**
	 * 树的深度，树中结点的最大层次数
	 * 
	 * @var int
	 */
	public $depth = 0;

	//public function __construct() {}
	
	public function setParentNodeIdKey($key) {
		$this->nodeParentIdKey = $key;
	}
	
	public function setNodeIdKey($key) {
		$this->nodeIdKey = $key;
	}
	
	/**
	 * 填充要计算的数组
	 *
	 * @param array $var
	 * @param string $id
	 * @param string $parentId
	 */
	public function set($vars, $id = 'id', $parentId = 'parent_id') {
		if(!is_array($vars)) throw new \core\Exception("错误的数据类型");
		foreach ($vars as $var) {
			$this->inNodes[$var[$id]] = $var;
		}
		
		$this->nodeIdKey = $id;
		$this->nodeParentIdKey = $parentId;
	}
	
	/**
	 * 添加结点
	 *
	 * @param array $node 结点
	 * @param string $key = null 结点的下标
	 */
	public function add($node, $key = null) {
		$node = $key ? array($key => $node) : array($node);
		$this->inNodes = array_merge($this->inNodes, $node);
	}

	/**
	 * 获取子结点,子结点将按父结点id和结点id来排序
	 *
	 * @param int $id 结点的id
	 * @param bool $isReturnSelf 是否返回自己
	 * @return array
	 */
	public function get($id = 0, $isReturnSelf = true) {
		// 层次排序
		$this->levelOrder(0);		
		// 设置缩进图标/形状
		$this->setLevelIcon();
		
		$cats = $this->outNodes;
		if($id && isset($cats[$id])) {
			$cat = $cats[$id];
						
			if (!$isReturnSelf) {
				unset($cats[$id]);
			}
			
			foreach ($cats as $key => $_tmp) {
				if(!in_array($key, $cat['descendantIdArr'])){
					unset($cats[$key]);
				}
			}
		}
		
		return $cats;
	}
	
	/**
	 * 设置树的层次缩进图标/形状
	 * 
	 */
	private function setLevelIcon() {
	    $nodes  = $this->outNodes;
	    $fidName = $this->nodeParentIdKey;
	    
		// 设置结点在该层是否是最后一个结点
		foreach ($nodes as $k=>$v) {						
			// 修改同父分结点的结点为非最后结点
			foreach ($nodes as $k2 => $v2) {
				// 把和当前遍历到的结点同parent的结点设为非最后结点
				if (isset($nodes[$k2]['isLastNode']) && $nodes[$k2]['isLastNode'] == true && $v2[$fidName] == $v[$fidName]) {					
					$nodes[$k2]['isLastNode'] = false;					
				}
			}
			
			// 设置当前结点为最后结点
			$nodes[$k]['isLastNode'] = true;			
		}
		//print "\n------------$i-----------------\n";
		// 设置icon
		foreach ($nodes as $kIcon => $vIcon) {
			if (!$vIcon['level']) {
				continue;
			}
			
			//$icon = $vIcon['isLastNode'] ? '　\--' : '　|--';
			$icon = '&nbsp;|-';
			
			if ($vIcon['level'] == 1) {
				$picon = '';
			} else {
				if ($nodes[$vIcon[$fidName]]['isLastNode']) {
					$picon = substr($nodes[$vIcon[$fidName]]['icon'], 0, -3) . '&nbsp;';
				} else {
					$picon = substr($nodes[$vIcon[$fidName]]['icon'], 0, -3) . '|&nbsp;';
				}				
			}
			
			$nodes[$kIcon]['icon'] = $picon . $icon;
		}
		
		$this->outNodes = $nodes;
	}

	/**
	 * 预排序遍历树
	 *
	 * @param int|string $id 结点id
	 */
	protected function levelOrder($id) {
		foreach($this->inNodes as $node) {
			$nodeId   = $node[$this->nodeIdKey];  // 结点ID
			$nodeFid  = $node[$this->nodeParentIdKey];  // 结点的父结点的id 		
			$this->step ++ ;
			
			// 防止死循环（开发的时候需要检查是否把子分类作为分类的父分类）
			if (!empty($this->outNodes[$nodeId])) {
				continue;
			}
			
			if($nodeFid == $id) {
				$this->outNodes[$nodeId] = $node;
				$this->outNodes[$nodeId]['isLeaf'] = true;
				
				// 如果是顶级节点
				if (!$nodeFid) {	
					$this->outNodes[$nodeId]['isTop']             = true;
					$this->outNodes[$nodeId]['level']             = 1;  // 结点的层次，顶层结点层次为1	
					$this->outNodes[$nodeId]['ancestorIdArr'][]   = $nodeId;  // 从结点到根的所有结点id
					$this->outNodes[$nodeId]['descendantIdArr'][] = $nodeId;
					$this->outNodes[$nodeId]['descendantId']      = $nodeId;
					$this->outNodes[$nodeId]['topLevelId']        = $nodeId;  // 结点的顶层祖先结点的id	
					$this->outNodes[$nodeId]['chileArr']          = array();  // 子结点id
				} else {
					$this->outNodes[$nodeId]['isTop']             = false;
					$this->outNodes[$nodeId]['level']             = @$this->outNodes[$nodeFid]['level'] + 1;					
					$this->outNodes[$nodeId]['ancestorIdArr']     = array_merge($this->outNodes[$nodeFid]['ancestorIdArr'], array($nodeId));
					$this->outNodes[$nodeId]['topLevelId']        = $this->outNodes[$nodeFid]['topLevelId'];
					$this->outNodes[$nodeId]['chileArr']          = array();
					$this->outNodes[$nodeFid]['chileArr'][]   = $nodeId;
					$this->outNodes[$nodeFid]['isLeaf']       = false; // 设置父结点为非叶子（子树）	
						
					// 将结点id添加到该结点祖先结点的子孙id列表
					foreach ($this->outNodes[$nodeId]['ancestorIdArr'] as $_nid) {
						$this->outNodes[$_nid]['descendantIdArr'][] = $nodeId;
						$this->outNodes[$_nid]['descendantIdArr'] = array_unique($this->outNodes[$_nid]['descendantIdArr']);
						$this->outNodes[$_nid]['descendantId'] = join(',', $this->outNodes[$_nid]['descendantIdArr']);
					}					
				}
				
				// 把当前分类的id添加到父分类的child
				//$this->addChildMark($nodeId, $nodeFid);
				$this->depth = ($this->depth > $this->outNodes[$nodeId]['level']) ? $this->depth : $this->outNodes[$nodeId]['level'];
				if($nodeId) {
					$this->levelOrder($nodeId);				
				}
				//unset($this->inNodes[$nodeId]);
			}
		}
	}
	
	public function __destruct() {
		unset($this->inNodes);
		unset($this->outNodes);
	}	
}

