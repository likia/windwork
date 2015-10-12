<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\mvc;

/**
 * Abstract RESTful controller
 *
 * @package     core.mvc
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.mvc.restful.html
 * @since       1.0.0
 */
abstract class Restful extends Controller {
	
	public function execute($params) {
		if($this->request->getRequest('act') == 'error') {
			parent::errorAction();
			return;
		}
		
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if (!in_array($method, array('get', 'put', 'post', 'delete'))) {
			$method = 'get';
		}
		
		$id = empty($params) ? null : $params[0];

		switch ($method) {
			case 'get':
				if ($id) {
					$this->get($id);
				} else {
					$this->getList();
				}
				break;
				
			case 'post':
				$data = $GLOBALS['HTTP_RAW_POST_DATA'];
				$this->create($data);
				break;
				
			case 'put':
				$data = $GLOBALS['HTTP_RAW_POST_DATA'];
			    $this->update($id, $data);
				break;
				
			case 'delete':
				if (!$id) {
					$this->response->setStatus(405, 'Method Not Allowed');
				}
			    $this->delete($id);
				break;
			default:
				$this->response->setStatus(405, 'Method Not Allowed');
				break;			
		}		
	}
	
	/**
	 * 返回资源列表
	 *
	 * @return mixed
	 */
	public function getList() {
		$this->response->setStatus(405, 'Method Not Allowed');
	}
	
	/**
	 * 返回一个资源
	 *
	 * @param mixed $id
	 * @return mixed
	*/
	public function get($id) {
		$this->response->setStatus(405, 'Method Not Allowed');
	}
	
	/**
	 * Create a new resource
	 *
	 * @param mixed $data
	 * @return mixed
	*/
	public function create($data) {
		$this->response->setStatus(405, 'Method Not Allowed');
	}
	
	/**
	 * 更新一个资源
	 *
	 * @param mixed $id
	 * @param mixed $data
	 * @return mixed
	*/
	public function update($id, $data) {
		$this->response->setStatus(405, 'Method Not Allowed');
	}
	
	/**
	 * 删除一个资源
	 *
	 * @param mixed $id
	 * @return mixed
	*/
	public function delete($id) {
		$this->response->setStatus(405, 'Method Not Allowed');
	}
		
}