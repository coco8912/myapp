<?php
namespace Think;
class Think{
	private static $_map = array();
	private static $_instance = array();
	static public function start(){
		spl_autoload_register('Think\Think::autoload');
		register_shutdown_function('Think\Think::fatalError');
		set_error_handler('Think\THink::appError');
		set_exception_handler('THink\THink::appException');
		Storage::connect(STORAGE_TYPE);
		$runtimefile = RUNTIME_PATH.APP_MODE.'~runtime.php';
	}
}
?>