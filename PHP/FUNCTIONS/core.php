<?php
	
	if(session_id()=='') { session_start(); }
	
	/* DISABLE: Session resurection */
	if(!isset($_SESSION['SESSID']) || $_SESSION['SESSID']!=session_id()) {
		if(isset($_COOKIE['PHPSESSID'])) {
			// client can't choose its session id name
			session_regenerate_id();
			$_SESSION['SESSID'] = session_id();
		} else {
			$_SESSION['SESSID'] = session_id();
		}
	}

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
	
	define('_is_utf8_split',5000); 

	function is_utf8($string) { // v1.01 
		if (strlen($string) > _is_utf8_split) { 
			// Based on: http://mobile-website.mobi/php-utf8-vs-iso-8859-1-59 
			for ($i=0,$s=_is_utf8_split,$j=ceil(strlen($string)/_is_utf8_split);$i < $j;$i++,$s+=_is_utf8_split) { 
				if (is_utf8(substr($string,$s,_is_utf8_split))) 
					return true; 
			} 
			return false; 
		} else { 
			// From http://w3.org/International/questions/qa-forms-utf-8.html 
			return preg_match('%^(?: 
					[x09x0Ax0Dx20-x7E]            # ASCII 
				| [xC2-xDF][x80-xBF]             # non-overlong 2-byte 
				|  xE0[xA0-xBF][x80-xBF]        # excluding overlongs 
				| [xE1-xECxEExEF][x80-xBF]{2}  # straight 3-byte 
				|  xED[x80-x9F][x80-xBF]        # excluding surrogates 
				|  xF0[x90-xBF][x80-xBF]{2}     # planes 1-3 
				| [xF1-xF3][x80-xBF]{3}          # planes 4-15 
				|  xF4[x80-x8F][x80-xBF]{2}     # plane 16 
			)*$%xs', $string); 
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
        } else if (is_string($val) && !is_utf8($val)) { 
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