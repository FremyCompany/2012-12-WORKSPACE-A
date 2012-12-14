<?php
	if (isset($jsonService)) { 
		if(isset($_REQUEST['func'])) {
			
			if(!isset($_REQUEST['args'])) { $_REQUEST['args'] = '[]'; }
			$_ENV['json']=true; 
			//addLog($_REQUEST['args']);
			$result = call_user_func_array($jsonService.'::'.$_REQUEST['func'], json_decode($_REQUEST['args']));
					
			echo js_encode(array(true,$result));
			
			exit;
			
		} else {
			
            check_cache(fileLastMod());
			echo "window.$jsonService={";
			
				$ms = get_class_methods($jsonService);
				foreach($ms as $index => $name) {
					echo "$name: websFunc('$name'),";
				}
				echo "serviceUrl: ".js_encode($_SERVER['REQUEST_URI']);
				
			echo "};";
			
		}
	}
?>