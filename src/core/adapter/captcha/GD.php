<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\captcha;

/**
 * 验证码(GD库实现)
 * 
 * 安全的验证码要：验证码文字旋转，使用不同字体，可加干扰码、可加干扰线、可使用中文、可使用背景图片
 * 可配置的属性都是一些简单直观的变量，我就不用弄一堆的setter/getter了
 * 
 * useage:
 * $start = microtime(1);
 * $capt = \core\Factory::captcha();
 * $capt->useNoise = 0;
 * $capt->useCurve = 0;
 * $capt->useImgBg = 0;
 * $capt->fontSize = 15;
 * $capt->entry();
 * 
 *  验证码对比校验
 *  if (!\core\Factory::captcha()->check(@$_POST['secode'])) {
 *  	print 'error secode';
 *  }
 * 
 * @package     core.adapter.captcha
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.captcha.html
 * @since       1.0.0
 */
class GD implements ICaptcha, \core\adapter\IFactoryAble {	
	/**
	 * 是否使用背景图片 
	 * 
	 * @var bool
	 */
	public $useImgBg  = false;     // 
	/**
	 * 是否画混淆曲线
	 * 
	 * @var bool
	 */
	public $useCurve  = true;
	
	/**
	 * 是否添加杂点	
	 * 
	 * @var bool
	 */
	public $useNoise  = true;
	
	/**
	 * 文字倾斜度范围
	 * 
	 * @var bool
	 */
	public $gradient  = 22; 
	
	/**
	 * 验证码的session的下标
	 * 
	 * @var string
	 */
	const SESS_KEY    = 'sid.windwork.org';
	
	public static $expire    = 3000;     // 验证码过期时间（s）
	
	public $fontSize  = 25;     // 验证码字体大小(px)
	
	public $height    = 0;        // 验证码图片宽
	public $width     = 0;        // 验证码图片长
	public $length    = 4;        // 验证码位数
	public $bg        = array(243, 251, 254);  // 背景
	
	/**
	 * 是否扭曲验证码
	 *
	 * @var bool
	 */
	public $isSkew    = false;  
		
	
	/**
	 * 验证码中使用的字符，01IO容易混淆，建议不用
	 *
	 * @var string
	 */
	private $codeSet = '3456789AbcDEFHKLMNPQRSTUVWXY';
	private $image   = null;     // 验证码图片实例
	private $color   = null;     // 验证码字体颜色
	
	/**
	 * 
	 * @param array $cfg
	 */
	public function __construct($cfg) {
		
	}
	
	/**
	 * 验证验证码是否正确
	 *
	 * @param string $code 用户验证码
	 * @param string $id 下标
	 * @return bool 用户验证码是否正确
	 */
	public function check($code, $id = 'sec') {		
		isset($_SESSION) || session_start();
		// 验证码不能为空
		if(empty($code) || empty($_SESSION[self::SESS_KEY])) {
			return false;
		}
		
		$secode =  @$_SESSION[self::SESS_KEY][$id];
		// session 过期
		if(time() - $secode['time'] > self::$expire) {
			return false;
		}

		if(strtoupper($code) == strtoupper($secode['code'])) {
			return true;
		}

		return false;
	}

	/**
	 * 输出验证码并把验证码的值保存的session中
	 * 验证码保存到session的格式为： $_SESSION[self::SESS_KEY] = array('code' => '验证码值', 'time' => '验证码创建时间');
	 */
	public function render($id = 'sec') {
		//$this->bg = array(mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
		// 图片宽(px)
		$this->width || $this->width = $this->length * $this->fontSize * 1.25; 
		// 图片高(px)
		$this->height || $this->height = $this->fontSize * 1.5;
		// 建立一幅 $this->width x $this->height 的图像
		$this->image = imagecreate($this->width, $this->height); 
		// 设置背景      
		imagecolorallocate($this->image, $this->bg[0], $this->bg[1], $this->bg[2]); 
		
		// 验证码字体随机颜色
		$this->color = imagecolorallocate($this->image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
		// 验证码使用随机字体
		$ttfPath = SRC_PATH . 'core/res/captcha/ttfs/';

		$dir = dir($ttfPath);
		$ttfs = array();		
		while (false !== ($file = $dir->read())) {
		    if($file[0] != '.' && strtolower(substr($file, -4)) == '.ttf') {
				$ttfs[] = $ttfPath . $file;
			}
		}
		$dir->close();
		
		if(empty($ttfs)) {
			throw new Exception('no ttf');
		}

		$ttf = $ttfs[array_rand($ttfs)];	
				
		$this->useImgBg && self::background(); // 添加背景图片
		$this->useNoise && self::writeNoise(); // 绘杂点
		$this->useCurve && self::writeCurve(); // 绘干扰线
		
		// 绘验证码
		$code = array(); // 验证码
		$codeNX = - mt_rand($this->fontSize*0.3, $this->fontSize*0.6); // 验证码第N个字符的左边距		
		for ($i = 0; $i<$this->length; $i++) {
			$code[$i] = $this->codeSet[mt_rand(0, strlen($this->codeSet)-1)];
			$codeNX += mt_rand($this->fontSize*0.95, $this->fontSize*1.1);
			$gradient = mt_rand(-$this->gradient, $this->gradient);	
			
			// 写一个验证码字符
			imagettftext($this->image, $this->fontSize, $gradient, $codeNX, mt_rand($this->fontSize*1.25, $this->fontSize*1.36), $this->color/*imagecolorallocate($this->image, mt_rand(1,130), mt_rand(1,130), mt_rand(1,130))*/, $ttf, $code[$i]);
		}
		
		// 保存验证码
		isset($_SESSION) || session_start();
		if($id) {
			$_SESSION[self::SESS_KEY][$id]['code'] = join('', $code); // 把校验码保存到session
			$_SESSION[self::SESS_KEY][$id]['time'] = time();  // 验证码创建时间
		} else {
			$_SESSION[self::SESS_KEY]['code'] = join('', $code); // 把校验码保存到session
			$_SESSION[self::SESS_KEY]['time'] = time();  // 验证码创建时间
		}

		$this->isSkew && self::skew();
		
		header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);		
		header('Pragma: no-cache');
		header("content-type: image/png");
	
		// 输出图像
		imagepng($this->image); 
		imagedestroy($this->image);
	}
	
	/** 
	 * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数) 
     *      
     *      高中的数学公式咋都忘了涅，写出来
	 *		正弦型函数解析式：y=Asin(ωx+φ)+b
	 *      各常数值对函数图像的影响：
	 *        A：决定峰值（即纵向拉伸压缩的倍数）
	 *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
	 *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
	 *        ω：决定周期（最小正周期T=2π/∣ω∣）
	 *
	 */
    protected function writeCurve() {
    	$px = $py = 0;
		// 曲线前部分
		$A = mt_rand(1, $this->height/2);                  // 振幅
		$b = mt_rand(-$this->height/4, $this->height/4);   // Y轴方向偏移量
		$f = mt_rand(-$this->height/4, $this->height/4);   // X轴方向偏移量
		$T = mt_rand($this->height, $this->width*2);      // 周期
		$w = (2* M_PI)/$T;
						
		$px1 = 0;  // 曲线横坐标起始位置
		$px2 = mt_rand($this->width/2, $this->width * 0.8);  // 曲线横坐标结束位置

		for ($px=$px1; $px<=$px2; $px=$px+ 0.5) {
			if ($w!=0) {
				$py = $A * sin($w*$px + $f)+ $b + $this->height/2;  // y = Asin(ωx+φ) + b
				$i = (int) ($this->fontSize/6);
				while ($i > 0) {	
				    imagesetpixel($this->image, $px , $py + $i, $this->color);  // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多				    
				    $i--;
				}
			}
		}
		
		// 曲线后部分
		$A = mt_rand(1, $this->height/2);                  // 振幅		
		$f = mt_rand(-$this->height/4, $this->height/4);   // X轴方向偏移量
		$T = mt_rand($this->height, $this->width*2);      // 周期
		$w = (2* M_PI)/$T;		
		$b = $py - $A * sin($w*$px + $f) - $this->height/2;
		$px1 = $px2;
		$px2 = $this->width;

		for ($px=$px1; $px<=$px2; $px=$px+ 0.5) {
			if ($w!=0) {
				$py = $A * sin($w*$px + $f)+ $b + $this->height/2;  // y = Asin(ωx+φ) + b
				$i = (int) ($this->fontSize/6);
				while ($i > 0) {			
				    imagesetpixel($this->image, $px, $py + $i, $this->color);
				    $i--;
				}
			}
		}
	}
	
	/**
	 * 画杂点
	 * 往图片上写不同颜色的字母或数字
	 */
	protected function writeNoise() {
		$num = (int)$this->fontSize*2;
		for($i = 0; $i < $num; $i++){
			//杂点颜色
		    $noiseColor = imagecolorallocate(
                      $this->image, 
                      mt_rand(150, 255), 
                      mt_rand(150, 255), 
                      mt_rand(150, 255)
                  );
			// 绘杂点
		    imagestring(
		        $this->image,
		        5, 
		        mt_rand(-5, $this->width), 
		        mt_rand(-5, $this->height), 
		        $this->codeSet[mt_rand(0, 25)], // 杂点文本为随机的字母或数字
		        $noiseColor
		    );
		}
	}
	
	/**
	 * 绘制背景图片
	 * 注：如果验证码输出图片比较大，将占用比较多的系统资源
	 */
	private function background() {
		$path = SRC_PATH . 'core/res/captcha/bgs/';
		$dir = dir($path);

		$bgs = array();		
		while (false !== ($file = $dir->read())) {
		    if($file[0] != '.' && substr($file, -4) == '.jpg') {
				$bgs[] = $path . $file;
			}
		}
		$dir->close();

		$gb = $bgs[array_rand($bgs)];

		list($width, $height) = @getimagesize($gb);
		// Resample
		$bgImage = @imagecreatefromjpeg($gb);
		@imagecopyresampled($this->image, $bgImage, 0, 0, 0, 0, $this->width, $this->height, $width, $height);
		@imagedestroy($bgImage);
	}
	
	/**
	 * TODO
	 * 扭曲图片
	 * 按正弦曲线分别对x和y方向复制图片并且并粘贴
	 */
	private function skew() {		
		//imageline($this->image,0,100,$this->width,100, imagecolorallocate($this->image,0,0,0));//画一条横线 
		//imageline($this->image,50,0,50,$this->height, imagecolorallocate($this->image,0,0,0));//画一条竖线 
		
    	$A = $this->fontSize/mt_rand(8, 12);
        $w = mt_rand(2, 4)/$this->fontSize;   
    	$f = mt_rand(50, 100);
    	// 纵向扭曲
        for ($x = 0; $x < $this->width; $x+=0.2) {
        	// y = Asin(ωx+φ) + k         	
        	// k = 0
        	$y = $A*sin($w*$x + $f);
            imagecopy($this->image, $this->image,
                $x-1, $y,
                $x, 0,
                1, $this->height-$y);
        }
        // 横向扭曲
        $A = $this->fontSize/mt_rand(4, 6);
        $w = mt_rand(2, 4)/$this->fontSize;
        $f = mt_rand(50, 100);
        for ($y = 0; $y < $this->height; $y+=0.2) {
        	// y = Asin(ωx+φ) + k 
        	// k = 0
        	$x = $A*sin($w*$y + $f);
        	//$y = $A*sin($w*$x);
            imagecopy($this->image, $this->image,
                $x, $y,
                0, $y+1,
                $this->width-$x, 1);
        }
    }
}



