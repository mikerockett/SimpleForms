<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit04bc9ad8ae5fc5c587e27e05bae944ba
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'Violin\\' => 7,
        ),
        'S' => 
        array (
            'Symfony\\Component\\Yaml\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Violin\\' => 
        array (
            0 => __DIR__ . '/..' . '/alexgarrett/violin/src',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'StringTemplate\\Test' => 
            array (
                0 => __DIR__ . '/..' . '/nicmart/string-template/tests',
            ),
            'StringTemplate' => 
            array (
                0 => __DIR__ . '/..' . '/nicmart/string-template/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit04bc9ad8ae5fc5c587e27e05bae944ba::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit04bc9ad8ae5fc5c587e27e05bae944ba::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit04bc9ad8ae5fc5c587e27e05bae944ba::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
