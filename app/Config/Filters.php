<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\AuthFilter; // Asegúrate de que esta línea esté presente si no lo está


class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => \CodeIgniter\Shield\Filters\ChainAuth::class,, // Asegúrate de que este alias esté correcto
        'SessionAdmin'  => \App\Filters\SessionAdmin::class,
    ];

    public array $required = [
        'before' => [],
        'after' => [
            'toolbar',
        ],
    ];

    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    public array $methods = [];

    public array $filters = [
        "SessionAdmin" => [
            "before" => [
                "/inicio"
            ]
        ],
        // DESCOMENTAR Y APLICAR ESTE FILTRO A LAS RUTAS PROTEGIDAS
        'auth' => ['before' => ['/perfil', '/perfil/*', '/enlace', '/enlace/*', '/lecturas_gas/*', '/detalles/*']],
        // Añade aquí todas las rutas o grupos de rutas que requieran que el usuario esté logueado
        // Por ejemplo: '/perfil' (para la ruta exacta) y '/perfil/*' (para sub-rutas dentro de perfil)
    ];
}
