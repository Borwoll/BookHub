<?php

declare(strict_types=1);

if (false === isset($frameworkPath)) {
    $searchPaths = [
        __DIR__ . '/vendor/yiisoft/yii2',
        __DIR__ . '/../vendor/yiisoft/yii2',
    ];

    foreach ($searchPaths as $path) {
        if (true === is_dir($path)) {
            $frameworkPath = $path;

            break;
        }
    }
}

if (false === isset($frameworkPath) || false === is_dir($frameworkPath)) {
    $message = "<h1>Error</h1>\n\n"
        . "<p><strong>The path to yii framework seems to be incorrect.</strong></p>\n"
        . '<p>You need to install Yii framework via composer or adjust the framework path in file <abbr title="' . __FILE__ . '">' . basename(__FILE__) . "</abbr>.</p>\n"
        . '<p>Please refer to the <abbr title="' . __DIR__ . "/README.md\">README</abbr> on how to install Yii.</p>\n";

    if (false === empty($_SERVER['argv'])) {
        echo strip_tags($message);
    } else {
        echo $message;
    }
    exit(1);
}

require_once $frameworkPath . '/requirements/YiiRequirementChecker.php';
$requirementsChecker = new YiiRequirementChecker();

$gdMemo = $imagickMemo = 'Either GD PHP extension with FreeType support or ImageMagick PHP extension with PNG support is required for image CAPTCHA.';
$gdOK = $imagickOK = false;

if (true === extension_loaded('imagick')) {
    $imagick = new Imagick();
    $imagickFormats = $imagick->queryFormats('PNG');
    if (true === in_array('PNG', $imagickFormats)) {
        $imagickOK = true;
    } else {
        $imagickMemo = 'Imagick extension should be installed with PNG support in order to be used for image CAPTCHA.';
    }
}

if (true === extension_loaded('gd')) {
    $gdInfo = gd_info();
    if (false === empty($gdInfo['FreeType Support'])) {
        $gdOK = true;
    } else {
        $gdMemo = 'GD extension should be installed with FreeType support in order to be used for image CAPTCHA.';
    }
}

$requirements = [
    [
        'name' => 'PDO extension',
        'mandatory' => true,
        'condition' => extension_loaded('pdo'),
        'by' => 'All DB-related classes',
    ],
    [
        'name' => 'PDO SQLite extension',
        'mandatory' => false,
        'condition' => extension_loaded('pdo_sqlite'),
        'by' => 'All DB-related classes',
        'memo' => 'Required for SQLite database.',
    ],
    [
        'name' => 'PDO MySQL extension',
        'mandatory' => false,
        'condition' => extension_loaded('pdo_mysql'),
        'by' => 'All DB-related classes',
        'memo' => 'Required for MySQL database.',
    ],
    [
        'name' => 'PDO PostgreSQL extension',
        'mandatory' => false,
        'condition' => extension_loaded('pdo_pgsql'),
        'by' => 'All DB-related classes',
        'memo' => 'Required for PostgreSQL database.',
    ],
    [
        'name' => 'Memcache extension',
        'mandatory' => false,
        'condition' => extension_loaded('memcache') || extension_loaded('memcached'),
        'by' => '<a href="https://www.yiiframework.com/doc-2.0/yii-caching-memcache.html">MemCache</a>',
        'memo' => extension_loaded('memcached') ? 'To use memcached set <a href="https://www.yiiframework.com/doc-2.0/yii-caching-memcache.html#$useMemcached-detail">MemCache::useMemcached</a> to <code>true</code>.' : '',
    ],
    [
        'name' => 'GD PHP extension with FreeType support',
        'mandatory' => false,
        'condition' => $gdOK,
        'by' => '<a href="https://www.yiiframework.com/doc-2.0/yii-captcha-captcha.html">Captcha</a>',
        'memo' => $gdMemo,
    ],
    [
        'name' => 'ImageMagick PHP extension with PNG support',
        'mandatory' => false,
        'condition' => $imagickOK,
        'by' => '<a href="https://www.yiiframework.com/doc-2.0/yii-captcha-captcha.html">Captcha</a>',
        'memo' => $imagickMemo,
    ],
    'phpExposePhp' => [
        'name' => 'Expose PHP',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff('expose_php'),
        'by' => 'Security reasons',
        'memo' => '"expose_php" should be disabled at php.ini',
    ],
    'phpAllowUrlInclude' => [
        'name' => 'PHP allow url include',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff('allow_url_include'),
        'by' => 'Security reasons',
        'memo' => '"allow_url_include" should be disabled at php.ini',
    ],
    'phpSmtp' => [
        'name' => 'PHP mail SMTP',
        'mandatory' => false,
        'condition' => mb_strlen(ini_get('SMTP')) > 0,
        'by' => 'Email sending',
        'memo' => 'PHP mail SMTP server required',
    ],
];

if (false === version_compare(PHP_VERSION, '5.5', '>=')) {
    $requirements[] = [
        'name' => 'APC extension',
        'mandatory' => false,
        'condition' => extension_loaded('apc'),
        'by' => '<a href="https://www.yiiframework.com/doc-2.0/yii-caching-apccache.html">ApcCache</a>',
    ];
}

$result = $requirementsChecker->checkYii()->check($requirements)->getResult();
$requirementsChecker->render();
exit(0 === $result['summary']['errors'] ? 0 : 1);
