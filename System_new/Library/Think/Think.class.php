<?php
namespace Think;
class Think{
	private static $_map = array();
	private static $_instance = array();
	static public function start(){
		spl_autoload_register('Think\Think::autoload');
		register_shutdown_function("Think\Think::fatalError");
		set_error_handler('Think\Think::appError');
		set_exception_handler('Think\Think::appException');
		Storage::connect(STORAGE_TYPE);
		$runtimefile = RUNTIME_PATH.APP_MODE.'~runtime.php';
		if(!APP_DEBUG && Storage::has($runtimefile)){
			Storage::load($runtimefile);
		}else{
			if(Storage::has($runtimefile))
				Storage::unlink($runtimefile);
			$content = '';
			$mode = include is_file(CONF_PATH.'core.php')?CONF_PATH.'core.php':MODE_PATH.APP_MODE.'.php';
			foreach($mode['alias'] as $file){
				if(is_file($file))
					include $file;
				if(!APP_DEBUG) $content.=compile($file);
			}
			foreach($mode['config'] as $key=>$file){
				is_numeric($key)?C(load_config($file)):C($key,load_config($file));
			}
			if('common'!==APP_MODE && is_file(CONF_PATH.'config_'.APP_MODE.CONF_EXT))
				C(include CONF_PATH.'config_'.APP_MODE.CONF_EXT);
			if(isset($mode['alias'])){
				self::addMap(is_array($mode['alias'])?$mode['alias']:include $mode['alisa']);
			}
			if(is_file(CONF_PATH.'alias.php'))
				self::addMap(include CONF_PATH.'alias.php');
			if(isset($mode['tags'])){
				Hook::import(is_array($mode['tags'])?$mode['tags']:include $mode['tags']);
			}
			if(is_file(CONF_PATH.'tags.php'))
				Hook::import(include CONF_PATH.'tags.php');
			L(include THINK_PATH.'Lang/'.strtolower(C('DEFAULT_LANG')).'.php');
			if(!APP_DEBUG){
				$content .= "\nnamespace { Think\\Think::addMap(".var_export(self::$_map,true).");";
				$content .="\nL(".var_export(L(),true).");";
				$content .="\nC(".var_export(C(),true).");";
				$content .="Think\Hook::import(".var_export(Hook::get(),true).");}";
				Storage::put($runtimefile,strip_whitespace('<?php'.$content));
			}else{
				C(include THINK_PATH.'Conf/debug.php');
				if(is_file(CONF_PATH.'debug'.CONF_EXT))
					C(include CONF_PATH.'debug'.CONF_EXT);
			}
		}
		if(APP_STATUS && is_file(CONF_PATH.APP_STATUS.CONF_EXT))
			C(include CONF_PATH.APP_STATUS.CONF_EXT);
		date_default_timezone_set(C('DEFAULT_TIMEZONE'));
		if(C('CHECK_APP_DIR')){
			$module = defined('BIND_MODULE')?BIND_MODULE:C('DEFAULT_MODULE');
			if(!is_dir(APP_PATH.$module) ||!is_dir(LOG_PATH)){
				Build::checkDir($module);
			}
		}
		G('loadTime');
		App::run();
	}
	static public function addMap($class,$map=''){
		if(is_array($class)){
			self::$_map = array_merge(self::$_map,$class);
		}else{
			self::$_map[$class] = $map;
		}
	}
	static public function getMap($class=''){
		if(''===$class){
			return self::$_map;
		}elseif(isset(self::$_map[$class])){
			return self::$_map[$class];
		}else{
			return null;
		}
	}
	public static function autoload($class){
		if(isset(self::$_map[$class])){
			include self::$_map[$class];
		}elseif(false !== strpos($class,'\\')){
			$name = strstr($class,'\\',true);
			if(in_array($name,array('Think','Org',"Behavior",'Com','Vendor'))||is_dir(LIB_PATH.$name)){
				$path = LIB_PATH;
			}else{
				$namespace = C('AUTOLOAD_NAMESPACE');
				$path = isset($namespace[$name])?dirname($namespace[$name]).'/': APP_PATH;
			}
			$filename = $path.str_replace('\\','/',$class).EXT;
			if(is_file($filename)){
				if(IS_WIN && false ===strpos(str_replace('/','\\',$filename),$class.EXT)){
					return ;
				}
				include $filename;
			}
		}elseif(!C('APP_USE_NAMESPACE')){
			foreach(explode(',',C('APP_AUTOLOAD_LAYER')) as $layer){
				if(substr($class,-strlen($layer)) == $layer){
					if(require_cache(MODULE_PATH.$layer.'/'.$class.EXT)){
						return ;
					}
				}
			}
			foreach(explode(',',C('APP_AUTOLOAD_PATH')) as $path){
				if(import($path.'.'.$class)){
					return ;
				}
			}
		}
	}
	public static function instance($class,$method=''){
		$identify = $class.$method;
		if(!isset(self::$_instance[$identify])){
			if(class_exists($class)){
				$o = new $class();
				if(!empty($method) && method_exists($o,$method)){
					self::$_instance[$identify] = call_user_func(array(&$o,$method));
				}else{
					self::$_instance[$identify] = $o;
				}
			}else{
				self::halt(L('_CLASS_NOT_EXIST').':'.$class);
			}
			
		}
		return self::$_instance[$identify];
	}
	static public function appException($e){
		$error = array();
		$error['message'] = $e->getMessage();
		$trace = $e->getTrace();
		if('E' == $trace[0]['function']){
			$error['file'] = $trace[0]['file'];
			$error['line'] = $trace[0]['line'];
		}else{
			$error['file'] = $e->getFile();
			$error['line'] = $e->getLine();
		}
		$error['trace'] = $e->getTraceAsString();
		Log::record($error['message'],Log::ERR);
		header(HTTP/1.1 404 NOT FOUND);
		header(Status:404 NOT FOUND);
		self::halt($error);
	}
	
}
?>