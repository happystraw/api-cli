<?php
define('APP_NAME', 'Console Tool For RainBow');
define('APP_VERSION', '1.0.0');
define('APP_PHP_VERSION', '5.6');
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);