<?php
namespace Think;
class Think{
	private static $_map = array();
	private static $_instance = array();
	static public function start(){
		spl_autoload_register('Think\Think::autoload');
		register_shutdown_function('Think\Think::fatalError');
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
			foreach($mode['core'] as $file){
				if(is_file($file))
					include $file;
				if(!APP_DEBUG) $content.=compile($file);
			}
			foreach($mode['config'] as $key=>$file){
				is_numeric($key)?C(load_config($file)):C($key,load_config($file));
			}
			if('common'!=APP_MODE && is_file(CONF_PATH.'config_'.APP_MODE.CONF_EXT))
				C(load_config(CONF_PATH.'config_'.APP_MODE.CONF_EXT));
			
		}
	}
}
?>