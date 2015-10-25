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
 * 模板视图引擎
 * 模板引擎将模板“编译”成php脚本，每次调用视图的时候将包含 “编译”后的php脚本 
 * 
 * @package     core.mvc
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.mvc.template.html
 * @since       1.0.0
 */
class Template {	
	/**
	 * 是否使用手机版模板视图
	 * @var bool
	 */
	public $isMobileView = false;
	
	/**
	 * 是否每次都“编译”模板
	 * 
	 * @var bool 默认 true
	 */
    public $forceCompile = true;
    
    /**
     * “编译”模板时是否合并模板到一个文件中
     * 
     * @var bool 默认 false
     */
    public $mergeCompile = false;  // 
    
    /**
     * 存放视图的文件夹，相对于当前站点所在目录
     * 
     * @var string 
     */
    public $tplDir = 'template/default';
    
    /**
     * 模板“编译”后存放目录
     * 
     * @var string
     */
    public $compiledDir = 'data/template';
    
    /**
     * 模板编译id，用于编译特殊的需要针对不同用户或不同参数唯一的模板界面
     * 
     * @var string 
     */
    public $compileId = '';
        
    /**
     * 存贮模板中设置及调用的变量
     * 
     * @var array
     */
    protected $vars = array();
    
    /**
     * 页面布局可调用的小工具
     * @var array
     */
    protected $hooks  = array();
    
    /**
     * 未定义的属性赋值给$this->vars
     * 
     * @param string $var
     * @param mixed $val
     */
    public function __set($var, $val) {
        $this->vars[$var] = $val;    
    }
    
    /**
     * 访问未定义的属性返回$this->vars[属性名]
     * 
     * @param string $var 属性名
     * @return mixed
     */
    public function __get($var) {
        return isset($this->vars[$var])? $this->vars[$var] : null;
    }
    
    /**
     * 设置模板目录，位于站点根目录
     * 
     * @param string $path
     * @return \core\mvc\Template
     */
    public function setTplDir($path) {
        $this->tplDir = trim($path, '/');
        return $this;
    }
    
    /**
     * 设置模板是否强制每次都编译
     * 
     * @param bool $isForceCompile
     * @return \core\mvc\Template
     */
    public function setForceCompile($isForceCompile) {
        $this->forceCompile = $isForceCompile;
        return $this;
    }
      
    /**
     * 设置模板编译时是否将该页面调用的模板合并到同一个文件中
     * 如果启用，能稍微提高性能，但页面子模板修改时程序将不能检测到，修改子模板后需要通过后台清楚模板缓存
     * 建议网站正式上线时启用该功能
     * 
     * @param bool $isMergeCompile
     * @return \core\mvc\Template
     */
    public function setMergeCompile($isMergeCompile) {
        $this->mergeCompile = $isMergeCompile;
        return $this;
    }        
    
    /**
     * 设置编译后的模板引擎的地址
     * 
     * @param string $path
     * @return \core\mvc\Template
     */
    public function setCompiledDir($path) {
        $this->compiledDir = rtrim($path, '/');
        return $this;
    }
    
    /**
     * 设置模板识别id,用于区分不同的语言
     * 
     * @param string $compileId
     * @return \core\mvc\Template
     */
    public function setCompileId($compileId) {
        $this->compileId = $compileId;
         return $this;
    }

    /**
     * 模板变量赋值
     *
     * @param string $k 模板变量下标
     * @param mixed $v 模板变量值
     * @return \core\mvc\Template
     */
    public function assign($k, $v) {
        $this->vars[$k] = $v;
        return $this;
    }

    /**
     * 获取模板变量的值
     *
     * @param string $index
     * @return mixed
     */
    public function getVar($index) {
        return isset($this->vars[$index]) ? $this->vars[$index] : null;
    }
    
    /**
     * 获取模板所有变量
     *
     * @return array
     */
    public function getVars() {
        return $this->vars;
    }

    /**
     * 显示视图
     *
     * @param string $file = "{$mod}/{$ctl}.{$act}.html" 模板文件，模板目录及文件名全部为小写
     */
    public function render($file = '') {
    	if (empty($file)) {
    		$file = "{$_GET['mod']}/{$_GET['ctl']}.{$_GET['act']}.html";
    	}

    	$file = strtolower($file);
    		
        $this->tplDir          = trim($this->tplDir, '/');
        $this->compileId       = trim($this->compileId, '/');
        $this->compiledDir     = trim($this->compiledDir, '/');

        // 确定是否启用手机模板（设为启用手机视图并且文件存在。）
        if ($this->isMobileView && is_file($this->tplDir . '/mobile/' . $file) && substr($this->tplDir, -7) != 'mobile/') {
        	$this->tplDir .= '/mobile';
        	$this->compileId .= '^mobile';
        } else if (false === strpos($this->tplDir, 'admincp')) {
        	$this->tplDir .= '/pc';
        }
        
        extract($this->vars,  EXTR_SKIP);
        
        // 包含文件        
        require $this->getTpl($file);
    }
    
    /**
     * 获取模板
     *
     * @param string $file 模板文件名
     * @return string 编译后的模板文件
     */
    protected function getTpl($file) {
        $file = trim(strtolower($file), '/ ');

        $tplFile      = "{$this->tplDir}/{$file}";
        $compiledFile = str_replace("/", '.', $file);
        $compiledFile = "{$this->compiledDir}/{$this->compileId}^{$compiledFile}.php";
      
        // 判断是否强制编译或是否过期($compiledFile不存在时 < 成立)
        if($this->forceCompile || !is_file($compiledFile) || @filemtime($compiledFile) < @filemtime($tplFile)) {
            $this->compile($tplFile, $compiledFile);
        }

        return $compiledFile;
    }

    /**
     * 编译模板
     * 
     * @param string $tplFile
     * @param string $compiledFile
     * @return bool
     * @throws \core\mvc\Exception 如果模板文件不存在则抛出异常
     */
    protected function compile($tplFile, $compiledFile) {
        if(!$template = @file_get_contents($tplFile)) {
            throw new Exception("'{$tplFile}' does not exists!");
        }
        
        $template = preg_replace("/\\<\\!\\-\\-\\s*?\\{(.+?)\\}\\s*?\\-\\-\\>/s", "{\\1}", $template); // 去掉<!--{}-->的<!-- -->
        $template = preg_replace_callback("/\\{tpl\\s+['\\\"]?(.*?)['\\\"]?\\}/is", array($this, 'subTpl'), $template); // {tpl xx} 包含另一个模板
        $template = preg_replace("/(<\\?xml.*?\\?>)/is", "<?php echo '\\1';?>\r\n", $template); // 允许模板中使用 <?xml 标签
        $template = preg_replace("/\\{hook\\s+(.+?)\\}/is", "<?php \$this->hooks('\\1');?>\r\n", $template);  // hooks, 用于布局中
        $template = preg_replace_callback("/\\{lang\\s+['\\\"]?(.+?)['\\\"]?\\}/is", array($this, 'lang'), $template); // 处理语言 {lang key}        
        $template = preg_replace_callback("/\\{#(.+?)#\\}/is", function($m){return "{#".base64_encode($m[1]).'#}';}, $template);  
        $template = preg_replace_callback("/\\{static\\}(.+?)\\{\\/static\\}/s", function($m){return "^static[" . base64_encode($m[1]). "]static$";}, $template);
        $template = preg_replace_callback("/\\{if\\s+(.+?)\\}/is", function($m){ return \core\mvc\Template::quote("<?php if({$m[1]}) : ?>");}, $template); // {if 表达式}        
        $template = preg_replace_callback("/\\{elseif\\s+(.+?)\\}/is", function($m){ return \core\mvc\Template::quote("<?php elseif({$m[1]}) : ?>");}, $template); // {elseif 表达式}
        $template = preg_replace_callback("/\\{else\\s+if\\s+(.+?)\\}/is", function($m){ return \core\mvc\Template::quote("<?php elseif({$m[1]}) : ?>");}, $template); // {else if 表达式}
        $template = preg_replace("/\\{else\\}/is", "<?php else : ?>", $template);  // {else}
        $template = preg_replace_callback("/\\{for\\s+(.*?)\\}/is", function($m){ return \core\mvc\Template::quote("<?php for({$m[1]}) :?>");}, $template);  // {for 表达式1; 表达式2; 表达式3}
        $template = preg_replace("/\\{\\/if\\}/is", "<?php endif; ?>", $template);  // {/if} -> endif
        $template = preg_replace("/\\{\\/for\\}/is", "<?php endfor; ?>", $template);  // {/for} -> endfor
        $template = preg_replace("/\\{\\}/", "{!^_^!}", $template);  // {}

        // endforeach
        $template = preg_replace("/\\{\\/loop\\}/", "<?php endforeach; endif; ?>", $template );
        
        // foreach($a as $v)
        if(preg_match_all("/\\{loop\\s+?(\\S+?)\\s+?(\\S+?)\\}/s", $template, $matches)) {
        	$search = array();
        	$replace = array();
        	foreach ($matches[0] as $k => $mat) {
        		$search[$k] = $mat;
        		$replace[$k] = $this->quote("<?php \$__loop__tmp__{$k} = @{$matches[1][$k]}; if(!empty(\$__loop__tmp__{$k}) && !is_scalar(\$__loop__tmp__{$k})): foreach(\$__loop__tmp__{$k} as {$matches[2][$k]}) : ?>");
        	}
        	$template = str_replace($search, $replace, $template);
        } 
        
        // foreach($a as $k => $v)
        if(preg_match_all("/\\{loop\\s+?(\\S+?)\\s+?(\\S+)?\\s+?(\\S+?)\\}/s", $template, $matches)) {
        	$search = array();
        	$replace = array();
        	foreach ($matches[0] as $k => $mat) {
        		$search[$k] = $mat;
        		$replace[$k] = $this->quote("<?php \$__loop__tmp__x_{$k} = @{$matches[1][$k]}; if(!empty(\$__loop__tmp__x_{$k}) && !is_scalar(\$__loop__tmp__x_{$k})): foreach(\$__loop__tmp__x_{$k} as {$matches[2][$k]} => {$matches[3][$k]}) : ?>");
        	}
        	$template = str_replace($search, $replace, $template);
        } 
                
        // 输出
        $template = preg_replace("/\\{(\\$\\_(GET|POST|REQUEST|COOKIE)\\[.*?\\])\\}/", "{htmlspecialchars(@$1)}", $template); // 外部变量输出xss过滤
        $template = preg_replace_callback("/\\{([a-zA-Z_\\x7f-\\xff\\\\][a-zA-Z0-9\\\\_\\x7f-\\xff\\:]*\\(([^{}]*)\\))\\}/s", function($m){return \core\mvc\Template::quote("<?php echo {$m[1]};?>");}, $template);  // 函数//*/
        $template = preg_replace_callback("/\\{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9\\\\_\\-\\>\\x7f-\\xff\\:]*\\(([^{}]*)\\))\\}/s", function($m){return \core\mvc\Template::quote("<?php echo {$m[1]};?>");}, $template );  // 对象方法调用//*/
        $template = preg_replace_callback("/\\{(\\$[a-zA-Z0-9_\\[\\]\\-\\>\\(\\)\\'\"\\.\\$\\x7f-\\xff]+)?\\}/s", function($m){return \core\mvc\Template::quote("<?php echo @{$m[1]};?>");}, $template); //数组/变量/对象属性
        $template = preg_replace("/\\{([A-Z_\\x7f-\\xff][A-Z0-9_\\x7f-\\xff]+?)\\}/s", "<?php defined('\\1') && print \\1;?>", $template );  // 常量

        $template = preg_replace("/\\{url\\s+['\"]?(.*?)['\"]?\\}/is", "<?php echo url(\"\\1\");?>", $template);
        $template = preg_replace("/\\{thumb\\s+?(\\S+?)\\s+?(\\S+)?\\s+?(\\S+?)\\}/", "<?php echo thumb(\\1, \\2, \\3)?>", $template ); // thumb             
        $template = preg_replace_callback("/\\{#(.+?)#\\}/s", function($m){return '<?php '.\core\mvc\Template::scriptDecode($m[1]).'?>';}, $template);
        $template = preg_replace_callback("/\\^static\\[(.+?)\\]static\\$/s", function($m){return \core\mvc\Template::scriptDecode($m[1]);}, $template);
        $template = preg_replace("/{\\!\\^_\\^\\!}/", "{}", $template);  // {}
        
        // 添加在模板顶部的文件说明信息
        $thisTplMsg = "<?php\n/**\n"
                    . " * Windwork Template View (Don't edit this file)\n"
                    . " *\n"
                    . " * File: {$compiledFile}\n"
                    . " * From: {$tplFile}\n"
                    . " * Time: ". microtime(1) . "\n"
                    . " * Make: by Windwork template engine at "  . date('Y-m-d H:i:s') . "\n"
                    . " */\n"
                    . "defined('IS_IN') || die('Access Denied');\n"
                    . "use core\\Factory;\n"
                    . "use core\\App;\n"
                    . "use core\\Config;\n"
                    . "use core\\Lang;\n"
                    . "use core\\Common;\n"
                    . "use core\\mvc\\Router;\n"
                    . "use core\\mvc\\Message;\n"
                    . "\$request = App::getInstance()->getRequest();\n"
                    . "\$response = App::getInstance()->getResponse();\n"
                    . "?>";

        
        // 保存“编译”后模板文件
        @file_put_contents($compiledFile, $thisTplMsg . $template);
        
        return true;
    }
    
    /**
     * 模板内容解码
     * @param string $code
     * @return string
     */
    public static function scriptDecode($code) {
    	return str_replace("\\\"", "\"", base64_decode($code));  
    }
    
    /**
     * 转义双引号
     * 
     * @param string $var
     * @return string
     */
    public static function quote($var) {
        return str_replace ("\\\"", "\"", preg_replace("/\\[([a-zA-Z0-9_\\-\\.\\x7f-\\xff]+)\\]/s", "['\\1']", $var));
    }

    /**
     * 语言包处理
     *
     * @param string $m 匹配到的数组
     * @return string 语言包中该数组变量的值
     */
    protected function lang($m) {
    	$k1 = $m[1];
        $lang = \core\Lang::get(trim($k1));
        return $lang !== null ? $lang : "undefined lang.$k1";
    }
    
    /**
     * 视图中的钩子
     *
     * @todo 
     * @param array $m 
     */
    protected function hooks($m) {
    	$key = $m[1]; // Hook ID
        if(!$this->hooks) {
			$this->hooks = \core\Hook::$hooks;
            $tplHooksFile = SRC_PATH. "data/hooks.tlp.php";
            if(is_file($tplHooksFile)) {
                $tplHooks = require_once $tplHooksFile;
				$tplHooks && $this->hooks = array_merge($this->hooks, $tplHooks);
            }
        }
        
        \core\Hook::call($key);
    }

    /**
     * 解析子模板标签
     *
     * @param array $m
     * @return string
     */
    protected function subTpl($m) {
    	$subTpl = $m[1]; // 匹配的子模板
        if ($this->mergeCompile) {
        	$subTpl = "{$this->tplDir}/{$subTpl}.html";
            $content = file_get_contents($subTpl);
            $content = preg_replace("/\\<\\!\\-\\-\\s*?\\{(.+?)\\}\\s*?\\-\\-\\>/s", "{\\1}", $content); // 去掉<!--{}-->的<!-- -->

            $tplDir = $this->tplDir;
            // 多级包含，尽管支持多级包含，但应该少用多级包含
            for ($i=0; $i<5; $i++) {
            	$content = preg_replace_callback("/\\{tpl\\s+['\"]?(.*?)['\"]?\\}/is", function($m) use ($tplDir){
            		return file_get_contents("{$tplDir}/{$m[1]}.html");
            	}, $content); // {tpl xx}
            }
            // 去掉<!--{}-->的<!-- -->
            $content = preg_replace("/\\<\\!\\-\\-\\s?\\{(.+?)\\}\\s?\\-\\-\\>/s", "{\\1}", $content);
            return $content;
        } else {
            return "<?php require \$this->getTpl('{$subTpl}.html');?>";
        }
    } 
}

