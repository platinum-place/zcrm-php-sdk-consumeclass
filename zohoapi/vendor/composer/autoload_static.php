<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit37336093b2b3c9d1a172ef7f3f622da5
{
    public static $prefixLengthsPsr4 = array (
        'z' => 
        array (
            'zcrmsdk\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'zcrmsdk\\' => 
        array (
            0 => __DIR__ . '/..' . '/zohocrm/php-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit37336093b2b3c9d1a172ef7f3f622da5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit37336093b2b3c9d1a172ef7f3f622da5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
