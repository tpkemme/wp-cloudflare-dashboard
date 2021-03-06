<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit00ab720335e10d22966530f1721378ee
{
    public static $prefixesPsr0 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 
            array (
                0 => __DIR__ . '/..' . '/composer/installers/src',
            ),
        ),
    );

    public static $classMap = array (
        'WPCD_Analytics' => __DIR__ . '/../..' . '/includes/class-analytics.php',
        'WPCD_Assets' => __DIR__ . '/../..' . '/includes/class-assets.php',
        'WPCD_Charts' => __DIR__ . '/../..' . '/includes/class-charts.php',
        'WPCD_Cloudclient' => __DIR__ . '/../..' . '/includes/class-cloudclient.php',
        'WPCD_Options' => __DIR__ . '/../..' . '/includes/class-options.php',
        'WP_Cloudflare_Dashboard' => __DIR__ . '/../..' . '/wp-cloudflare-dashboard.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit00ab720335e10d22966530f1721378ee::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit00ab720335e10d22966530f1721378ee::$classMap;

        }, null, ClassLoader::class);
    }
}
