<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2da6b3f709547075d546b8867b7eb5e1
{
    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'markfullmer\\porter2\\' => 20,
        ),
        'P' => 
        array (
            'Phpml\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'markfullmer\\porter2\\' => 
        array (
            0 => __DIR__ . '/..' . '/markfullmer/porter2/src',
            1 => __DIR__ . '/..' . '/markfullmer/porter2/test',
        ),
        'Phpml\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-ai/php-ml/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Sastrawi\\' => 
            array (
                0 => __DIR__ . '/..' . '/sastrawi/sastrawi/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2da6b3f709547075d546b8867b7eb5e1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2da6b3f709547075d546b8867b7eb5e1::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit2da6b3f709547075d546b8867b7eb5e1::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit2da6b3f709547075d546b8867b7eb5e1::$classMap;

        }, null, ClassLoader::class);
    }
}
