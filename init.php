<?php
define('__CONTROLLERS__', 'controllers\\');
define('__CLASSES__', 'classes\\');
define('__MODELS__', 'models\\');

define('__CONTROLLER__', 'controllers/');
define('__CLASS__', 'classes/');
define('__VIEW__', 'views/');
define('__MODEL__', 'models/');
define('__PROCESS__', 'process/');
define('__CALCULATE__', 'calc/');

define('HEAD', __VIEW__."head.php");
define('HEADER', __VIEW__.'header.php');
define('FOOTER', __VIEW__.'footer.php');
define('FOOT', __VIEW__.'foot.php');
define('_404', __VIEW__.'404.php');

session_start();
$GLOBALS['conf'] = [
    'db_data' => false, 
    'tables' => false,
    'siteName' => "www.6thsense.co",
    'siteNameStyled' => "6thSense",
    'metaKeywords' => [],
    'title' => "6th Sense Company",
];

require_once 'functions/debug.php';
require_once 'functions/sanitize.php';
spl_autoload_extensions(".class.php");
spl_autoload_register(function ($clss) {
    $parsedClss = explode("\\", $clss);
    $namespace = $parsedClss[0];
    set_include_path(".:/opt/php56/lib/php".PATH_SEPARATOR.$namespace.PATH_SEPARATOR."utils");
    require_once(end($parsedClss).".php");
});
?>
