<?php

// Load environment variables
require __DIR__ . '/../vendor/autoload.php';

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad(); // Use safeLoad() to avoid errors if .env doesn't exist

require __DIR__ . '/../config/bootstrap.php';

// Set debug and environment from .env
defined('YII_DEBUG') or define('YII_DEBUG', env('APP_DEBUG', 'true') === 'true');
defined('YII_ENV') or define('YII_ENV', env('APP_ENV', 'dev'));

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
