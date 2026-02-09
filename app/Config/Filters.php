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
    public array $aliases = [
        'csrf' => CSRF::class,
        'toolbar' => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'invalidchars' => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors' => Cors::class,
        'forcehttps' => ForceHTTPS::class,
        'pagecache' => PageCache::class,
        'performance' => PerformanceMetrics::class,
        'adminAuth' => \App\Filters\AdminAuth::class,
    ];

    public array $required = [
        'before' => [
            'forcehttps',
            'pagecache',
            'csrf',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf' => [
                'except' => [
                    'admin/peliculas/store',      // Guardar nueva película
                    'admin/peliculas/update/*',   // <--- ¡NUEVO! Actualizar película (cualquier ID)
                    'admin/series/store',         // Guardar nueva serie
                    'admin/series/update/*',
                    'admin/usuarios/update/*',      
                    'api/*'                       // API y Buscador
                ]
            
            ],
            // 'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
        ],
    ];

    public array $methods = [];
    // app/Config/Filters.php

    public array $filters = [
        'adminAuth' => [
            'before' => [
                'admin/*',      // ✅ CORRECTO: Solo protege el panel de administración
                // 'api/*',     // ❌ BORRA ESTO: La API es para usuarios, no solo admins
                // 'mi-lista/*' // ❌ BORRA ESTO TAMBIÉN: Si no, tus usuarios no podrán ver su lista
            ],
            'except' => [
                'admin/login',
                'admin/auth/login',
                'auth',
                'auth/*'
                // Ya no hace falta poner excepciones de api aquí si borraste la línea de arriba
            ]
        ],
    ];
}