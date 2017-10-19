<?php
define('APP_NAME', 'Happystraw Console');
define('APP_VERSION', 'v0.0.1');
define('APP_PHP_VERSION', '5.6');
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);