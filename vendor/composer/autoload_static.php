<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf7afcef6e1072451af8f08c8ccb5c787
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf7afcef6e1072451af8f08c8ccb5c787::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf7afcef6e1072451af8f08c8ccb5c787::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf7afcef6e1072451af8f08c8ccb5c787::$classMap;

        }, null, ClassLoader::class);
    }
}
