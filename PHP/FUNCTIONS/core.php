<?php
	session_start();

	/* DISABLE: Magic quotes */
    if(get_magic_quotes_gpc() == 1){
        # Définition de la fonction récursive.
        function remove_magic_quotes(&$array){
            foreach($array as $key => $val){
                # Si c'est un array, recurssion de la fonction, sinon suppression des slashes
                if(is_array($val)){
                    remove_magic_quotes($array[$key]);
                } else if(is_string($val)){
                    $array[$key] = stripslashes($val);
                }
            }
        }
        # Appel de la fonction pour chaque variables.
        remove_magic_quotes($_POST);
        remove_magic_quotes($_GET);
        remove_magic_quotes($_REQUEST);
        remove_magic_quotes($_SERVER);
        remove_magic_quotes($_COOKIE);
    }

	function pathto($abs) {
		if($abs && $abs[0]=="/") {
			return $_SERVER['DOCUMENT_ROOT'].$abs;
		} else {
			return $abs;
		}
	}
	
	function utf8_obj_encode($val) {
		if(is_array($val)) { 
            // recurse on array elements
			$newval = array();
			foreach($val as $key => $value) {
				$newval[$key] = utf8_obj_encode($value); 
			}
			return $newval;
        } else if (is_string($val)) { 
            // encode string values
            return utf8_encode($val); 
        } else {
			// do nothing on other objects
			return $val;
		}

	}
	
	function js_encode($obj) {
		//return json_encode($obj);
		return json_encode(utf8_obj_encode($obj));
	}
	
	function hex2str($hexstr) {
		$hexstr = str_replace(' ', '', $hexstr);
		$hexstr = str_replace('\x', '', $hexstr);
		$retstr = pack('H*', $hexstr);
		return $retstr;
	}

	function str2hex($string) {
		$hexstr = unpack('H*', $string);
		return array_shift($hexstr);
	}
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/PHP/FUNCTIONS/CORE/throw.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/PHP/FUNCTIONS/CORE/cache.php');
?>