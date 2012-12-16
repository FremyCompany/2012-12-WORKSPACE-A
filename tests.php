<?php
    error_reporting(E_ALL);
    
    function myErrorHandler($errno, $errstr, $errfile, $errline) { 
        
        echo "<div style='margin: 7px; border-left: 3px solid orange; padding-left: 5px; font-family: Consolas, monospace; font-size: 10pt;'>";
        
        $errfile=substr(strrchr($errfile, '\\'), 1);
        if ($errfile=="debugger.php(56) : eval()'d code") {
            $errfile="eval code";
        }
        
        switch ($errno) {
            case E_USER_ERROR:
                echo "Error: $errstr (line: $errline; type: $errno; source: $errfile)<br />\n";
                echo "  Fatal error on line $errline in file $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            break;

            case E_USER_WARNING:
                echo "Warning: $errstr (line: $errline; type: $errno; source: $errfile)<br />\n";
            break;

            case E_USER_NOTICE:
                echo "Notice: $errstr (line: $errline; type: $errno; source: $errfile)<br />\n";
            break;

            default:
                echo "Error: $errstr (line: $errline; type: $errno; source: $errfile)<br />\n";
            break;
        }
        
        echo "</div>";

        /* Don't execute PHP internal error handler */
        return true;

    }
    set_error_handler("myErrorHandler");

    function exception_handler($exception) {}
    set_exception_handler('exception_handler');
    
    session_start();
    
    $code = "";
    if (isset($_REQUEST['code'])) { $code = $_REQUEST['code']; }
    
    echo ('<form action="debugger.php" method="POST"><textarea name="code" style="width: 450px; height: 200px;">'.htmlspecialchars($code).'</textarea><br/><input type="submit" /></form><br/>');
    echo ('<b>Starting code</b><br/>');
    if (isset($code) && $code != "") {
        $code=str_replace('\\\'','\'', str_replace("''","'",$code));
    }
    echo ('<pre>'.htmlspecialchars($code).'</pre><div style="color: green; padding-left: 15px; border-left: 3px green solid;">');
    if (isset($code) && $code != "") {
        @eval($code);
    }
    echo ('</div><br/><b>Ending code</b><br/>');
?>