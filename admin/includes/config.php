<?php
if (!defined('BASE_URL')) {
    $path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    define('BASE_URL', preg_replace('#/admin(/|$)#', '$1', $path));
}
