<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita1e47fe88e5e6c2ed9ef27262a4e19a0
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Curl\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita1e47fe88e5e6c2ed9ef27262a4e19a0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita1e47fe88e5e6c2ed9ef27262a4e19a0::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}