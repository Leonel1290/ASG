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


class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
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
        'auth'          => \CodeIgniter\Shield\Filters\ChainAuth::class,
        'SessionAdmin'  => \App\Filters\SessionAdmin::class,
    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * Filters set by default provide framework functionality. If removed,
     * those functions will no longer work.
     *
     * @see https://codeigniter.com/user_guide/incoming/filters.html#provided-filters
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            // 'forcehttps', // Force Global Secure Requests (consider habilitar en producción si usas HTTPS)
            // 'pagecache',  // Web Page Caching (considerar para rendimiento)
        ],
        'after' => [
            // 'pagecache',   // Web Page Caching
            // 'performance', // Performance Metrics
            'toolbar',     // Debug Toolbar (deshabilitar en producción)
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot', // Protección básica contra spam en formularios
            // 'csrf', // Protección CSRF (recomendado para formularios POST)
            // 'invalidchars', // Filtra caracteres potencialmente dañinos en la entrada
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders', // Añade cabeceras de seguridad (recomendado para producción)
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'POST' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        // Aplica el filtro SessionAdmin a la ruta /inicio
        // Asegúrate de que la lógica de este filtro redirija si el usuario no cumple los requisitos
        "SessionAdmin" => [
            "before" => [
                "/inicio"
                // Añade aquí otras rutas que solo los administradores deberían acceder
                // Por ejemplo: '/admin/*', '/dashboard', etc.
            ]
        ]
        // Ejemplo de filtro de autenticación general (si tuvieras uno)
        // 'auth' => ['before' => ['/perfil/*', '/enlace', '/enlace/store', '/lecturas_gas/guardar', '/detalles/*']],
    ];
}
