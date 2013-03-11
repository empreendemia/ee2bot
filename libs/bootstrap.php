<?php
define('BASE', $_SERVER['DOCUMENT_ROOT'].substr($_SERVER['PHP_SELF'], 0, (-1)*strlen('index.php')));

if (!isset($_GET['url'])) {
    die('URL NEEDED');
}
else if (!file_exists('apps/'.$_GET['url'])) {
    die('INVALID URL');
}
else {
    define('URL', 'apps/'.$_GET['url']);
}

if (is_dir(URL) && !file_exists(BASE.URL.'index.php')) {
    die('NO PHP FILE');
}

include(BASE.'libs/functions.php')

?>
