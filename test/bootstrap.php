<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 
                        dirname(__FILE__) . "/../lib");

function loader($class)
{
    $file = $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('loader');
?>
