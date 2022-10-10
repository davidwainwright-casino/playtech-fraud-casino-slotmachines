<?php

namespace Laravel\Nova;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Controllers\ForgotPasswordController;
use Laravel\Nova\Http\Controllers\LoginController;
use Laravel\Nova\Http\Controllers\ResetPasswordController;

class PendingRouteRegistration
{
    /**
     * Indicates if the routes have been registered.
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * Register the Nova authentication routes.
     *
     * @param  array<int, class-string|string>  $middleware
     * @return $this
     */
    public function withAuthenticationRoutes($middleware = ['nova'])
    {
        Nova::withAuthentication();

        Route::namespace('Laravel\Nova\Http\Controllers')
            ->domain(config('nova.domain', null))
            ->middleware($middleware)
            ->prefix(Nova::path())
            ->group(function (Router $router) {
                $router->get('/login', [LoginController::class, 'showLoginForm'])->name('nova.pages.login');
                $router->post('/login', [LoginController::class, 'login'])->name('nova.login');
            });

        Route::namespace('Laravel\Nova\Http\Controllers')
            ->domain(config('nova.domain', null))
            ->middleware(config('nova.middleware', []))
            ->prefix(Nova::path())
            ->group(function (Router $router) {
                $router->post('/logout', [LoginController::class, 'logout'])->name('nova.logout');
            });

        return $this;
    }

    /**
     * Register the Nova password reset routes.
     *
     * @param  array<int, class-string|string>  $middleware
     * @return $this
     */
    public function withPasswordResetRoutes($middleware = ['nova'])
    {
        Nova::withPasswordReset();

        Route::domain(config('nova.domain', null))
            ->middleware($middleware)
            ->prefix(Nova::path())
            ->group(function (Router $router) {
                $router->get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('nova.pages.password.email');
                $router->post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('nova.password.email');
                $router->get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('nova.pages.password.reset');
                $router->post('/password/reset', [ResetPasswordController::class, 'reset'])->name('nova.password.reset');
            });

        return $this;
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    public function register()
    {
        $this->registered = true;

        Route::namespace('Laravel\Nova\Http\Controllers')
            ->domain(config('nova.domain', null))
            ->middleware(config('nova.middleware', []))
            ->prefix(Nova::path())
            ->as('nova.pages.')
            ->group(function (Router $router) {
                $router->get('/403', 'Pages\Error403Controller')->name('403');
                $router->get('/404', 'Pages\Error404Controller')->name('404');
            });

        Route::namespace('Laravel\Nova\Http\Controllers')
            ->domain(config('nova.domain', null))
            ->middleware(config('nova.api_middleware', []))
            ->prefix(Nova::path())
            ->as('nova.pages.')
            ->group(function (Router $router) {
                $router->get('/', 'Pages\HomeController')->name('home');
                $router->redirect('dashboard', Nova::url('/'))->name('dashboard');
                $router->get('dashboards/{name}', 'Pages\DashboardController')->name('dashboard.custom');

                $router->get('resources/{resource}', 'Pages\ResourceIndexController')->name('index');
                $router->get('resources/{resource}/new', 'Pages\ResourceCreateController')->name('create');
                $router->get('resources/{resource}/{resourceId}', 'Pages\ResourceDetailController')->name('detail');
                $router->get('resources/{resource}/{resourceId}/edit', 'Pages\ResourceUpdateController')->name('edit');
                $router->get('resources/{resource}/{resourceId}/replicate', 'Pages\ResourceReplicateController')->name('replicate');
                $router->get('resources/{resource}/lens/{lens}', 'Pages\LensController')->name('lens');

                $router->get('resources/{resource}/{resourceId}/attach/{relatedResource}', 'Pages\AttachableController')->name('attach');
                $router->get('resources/{resource}/{resourceId}/edit-attached/{relatedResource}/{relatedResourceId}', 'Pages\AttachedResourceUpdateController')->name('edit-attached');
            });
    }

    /**
     * Handle the object's destruction and register the router route.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}
