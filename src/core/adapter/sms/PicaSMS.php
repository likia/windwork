<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\sms;

/**
 * sms.pica.com 短信接口
 * 
 * @package     core.adapter.sms
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.sms.html
 * @since       1.0.0
 */
class PicaSMS extends ASMS implements ISMS, \core\adapter\IFactoryAble {
	/**
	 * 发送的基本链接
	 * @var string
	 */
	const SendAPI = 'http://sms.pica.com/zqhdServer/sendSMS.jsp?';

	/**
	 * @var array
	 */
	private $config;
	
	/**
	 * 
	 * @param array $cfg
	 */
	public function __construct(array $cfg){
		$this->config = $cfg;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\sms\ISMS::send()
	 */
	public function send($phoneNum, $content) {
		if (empty($this->config) && !is_array($this->config)) {
			$this->setErr('没有配置基本信息');
			return false;
		}
		// 转码
		$content = \core\util\Utf8::gbk2Utf8($content);
		// 构造链接
		$this->config['phone'] = $phoneNum;
		$this->config['content'] = urlencode($content);
		$link = self::SendAPI;
		
		// 顺序不能错
		$config = array(
			'regcode', 'pwd', 'phone', 'content', 'extnum', 'level', 
			'schtime', 'reportflag', 'url', 'smstype', 'key',
		);
		
		foreach ($config as $key){
			$link .= $key . '=' . $this->config[$key] . '&';
		}
		$link = rtrim($link, '&');
		// 请求并获取返回信息
		$ret = file_get_contents($link);
		if ($ret === FALSE || strlen($ret) < 1) {
			$this->setErr('获取返回内容失败！');
			return false;
		}
		$ret = trim(strip_tags($ret));
		// 发送成功
		if ($ret == '0'){
			return true;
		}
		// 发送失败
		$errContent = $this->getCodeContent($ret);
		$this->setErr("ERR CODE：{$ret}，ERROR：{$errContent}");
		return false;
	}
	
	/**
	 * @param string $codeNum
	 */
	protected function getCodeContent($codeNum){
		$errCodeArr = array(
			'-99'	=> '其它故障',
			'0' => '成功',
			'5' => '具体的禁发词关键词的原因失败返回的值',
			'-1' => '用户名或密码不正确',
			'-2' => '余额不够',
			'-3' => '帐号没有注册',
			'-4' => '内容超长',
			'-5' => '账号路由为空',
			'-6' => '手机号码多余1000个',
			'-8' => '扩展号超长',
			'-13' => '定时时间错误或者小于当前系统时间',
			'-17' => '手机号码为空',
			'-18' => '号码不是数字或者逗号不是英文逗号',
			'-19' => '短信内容为空',
		);
		if (isset($errCodeArr[$codeNum])) {
			return $errCodeArr[$codeNum];
		}
		return $codeNum;
	}
}
