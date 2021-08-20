<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2625f4e089056ca474b206affa4171ff
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LZCompressor\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LZCompressor\\' => 
        array (
            0 => __DIR__ . '/..' . '/nullpunkt/lz-string-php/src/LZCompressor',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2625f4e089056ca474b206affa4171ff::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2625f4e089056ca474b206affa4171ff::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2625f4e089056ca474b206affa4171ff::$classMap;

        }, null, ClassLoader::class);
    }
}
