<?php

namespace ind4skylivey\LaravelSecureBaseline\Scanners;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Str;

class RoutesScanner extends AbstractScanner
{
    public function key(): string
    {
        return 'routes';
    }

    public function label(): string
    {
        return 'Routes & Debug Endpoints';
    }

    public function scan(): array
    {
        $findings = [];
        $paths = $this->config('routes.sensitive_paths', []);
        $router = $this->app['router'];
        $routes = $router->getRoutes();
        $isProduction = $this->app->environment('production');
        $failUnprotected = $this->config('routes.fail_when_unprotected_in_production', true);

        if (empty($paths)) {
            return [
                $this->warn('No sensitive routes configured.', 'Populate routes.sensitive_paths in secure-baseline config.'),
            ];
        }

        foreach ($paths as $path) {
            $route = $this->findRoute($routes, $path);
            if (! $route) {
                $findings[] = $this->pass(sprintf('%s route not registered.', $path));
                continue;
            }

            $middleware = $route->gatherMiddleware();
            $isProtected = $this->routeIsProtected($middleware);
            $methods = implode(',', array_diff($route->methods(), ['HEAD']));
            $summary = sprintf('%s route detected (%s).', $path, $methods ?: 'GET');

            if ($isProtected) {
                $middlewareLabel = empty($middleware) ? 'auth' : implode(', ', $middleware);
                $findings[] = $this->pass(sprintf('%s Protected by middleware: %s.', $summary, $middlewareLabel));
                continue;
            }

            $severity = ($isProduction && $failUnprotected) ? 'fail' : 'warn';
            $findings[] = $this->{$severity}(
                $summary.' No authentication middleware found.',
                'Restrict this tooling route to authorized users or disable it outside local environments.'
            );
        }

        return $findings;
    }

    private function findRoute(RouteCollection $routes, string $path): ?Route
    {
        $needle = trim($path, '/');

        foreach ($routes as $route) {
            $uri = trim($route->uri(), '/');
            if ($uri === $needle || Str::startsWith($uri, $needle)) {
                return $route;
            }
        }

        return null;
    }

    private function routeIsProtected(array $middleware): bool
    {
        $indicators = $this->config('routes.protected_middleware_indicators', []);

        foreach ($middleware as $layer) {
            foreach ($indicators as $indicator) {
                if (Str::contains($layer, $indicator)) {
                    return true;
                }
            }
        }

        return false;
    }
}
