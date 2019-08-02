<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3a75a1a21a795be748375c1558b3c4ce
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Frontend\\' => 9,
        ),
        'B' => 
        array (
            'Backend\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Frontend\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Frontend',
        ),
        'Backend\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Backend',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3a75a1a21a795be748375c1558b3c4ce::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3a75a1a21a795be748375c1558b3c4ce::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
