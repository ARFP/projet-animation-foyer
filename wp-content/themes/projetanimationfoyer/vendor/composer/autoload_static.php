<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit71fa54f8ead869b9d6b89befb86db61f
{
    public static $files = array (
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '89efb1254ef2d1c5d80096acd12c4098' => __DIR__ . '/..' . '/twig/twig/src/Resources/core.php',
        'ffecb95d45175fd40f75be8a23b34f90' => __DIR__ . '/..' . '/twig/twig/src/Resources/debug.php',
        'c7baa00073ee9c61edf148c51917cfb4' => __DIR__ . '/..' . '/twig/twig/src/Resources/escaper.php',
        'f844ccf1d25df8663951193c3fc307c8' => __DIR__ . '/..' . '/twig/twig/src/Resources/string_loader.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twig\\' => 5,
            'Timber\\' => 7,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'P' => 
        array (
            'Projet\\' => 7,
        ),
        'F' => 
        array (
            'Fantassin\\Core\\WordPress\\' => 25,
            'FantassinCoreWordPressVendor\\Symfony\\Contracts\\Service\\' => 55,
            'FantassinCoreWordPressVendor\\Symfony\\Component\\Filesystem\\' => 58,
            'FantassinCoreWordPressVendor\\Symfony\\Component\\DependencyInjection\\' => 67,
            'FantassinCoreWordPressVendor\\Symfony\\Component\\Config\\' => 54,
            'FantassinCoreWordPressVendor\\Psr\\Container\\' => 43,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Timber\\' => 
        array (
            0 => __DIR__ . '/..' . '/timber/timber/src',
        ),
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Projet\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Fantassin\\Core\\WordPress\\' => 
        array (
            0 => __DIR__ . '/..' . '/fantassin/core/src',
        ),
        'FantassinCoreWordPressVendor\\Symfony\\Contracts\\Service\\' => 
        array (
            0 => __DIR__ . '/..' . '/fantassin/core/vendor-scoped/symfony/service-contracts',
        ),
        'FantassinCoreWordPressVendor\\Symfony\\Component\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/fantassin/core/vendor-scoped/symfony/filesystem',
        ),
        'FantassinCoreWordPressVendor\\Symfony\\Component\\DependencyInjection\\' => 
        array (
            0 => __DIR__ . '/..' . '/fantassin/core/vendor-scoped/symfony/dependency-injection',
        ),
        'FantassinCoreWordPressVendor\\Symfony\\Component\\Config\\' => 
        array (
            0 => __DIR__ . '/..' . '/fantassin/core/vendor-scoped/symfony/config',
        ),
        'FantassinCoreWordPressVendor\\Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/fantassin/core/vendor-scoped/psr/container',
        ),
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'PhpToken' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit71fa54f8ead869b9d6b89befb86db61f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit71fa54f8ead869b9d6b89befb86db61f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit71fa54f8ead869b9d6b89befb86db61f::$classMap;

        }, null, ClassLoader::class);
    }
}
