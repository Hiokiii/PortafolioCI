<?php

namespace Config;

use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = ['csrf' => CSRF::class, 'toolbar' => DebugToolbar::class, 'honeypot' => Honeypot::class, 'invalidchars' => InvalidChars::class, 'secureheaders' => SecureHeaders::class, 'SesionAdmin' => \App\Filters\SesionAdmin::class, 'SesionSocio' => \App\Filters\SesionSocio::class, 'SesionEquipoTecnico' => \App\Filters\SesionEquipoTecnico::class, 'SesionJugador' => \App\Filters\SesionJugador::class, 'SesionEntrenador' => \App\Filters\SesionEntrenador::class, 'SesionDirector' => \App\Filters\SesionDirector::class, 'cors' => Cors::class, 'forcehttps' => ForceHTTPS::class, 'pagecache' => PageCache::class, 'performance' => PerformanceMetrics::class];
    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = ['before' => [], 'after' => []];
    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don’t expect could bypass the filter.
     */
    public array $methods = [];
    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $filters = [];
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
    public array $required = ['before' => [
        'forcehttps',
        // Force Global Secure Requests
        'pagecache',
    ], 'after' => [
        'pagecache',
        // Web Page Caching
        'performance',
        // Performance Metrics
        'toolbar',
    ]];
}
