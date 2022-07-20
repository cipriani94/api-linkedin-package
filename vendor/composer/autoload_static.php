<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfd85c3a118d15dbc2de3ce6e6a48b0fe
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'Neurohub\\Apilinkedin\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Neurohub\\Apilinkedin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfd85c3a118d15dbc2de3ce6e6a48b0fe::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfd85c3a118d15dbc2de3ce6e6a48b0fe::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfd85c3a118d15dbc2de3ce6e6a48b0fe::$classMap;

        }, null, ClassLoader::class);
    }
}
