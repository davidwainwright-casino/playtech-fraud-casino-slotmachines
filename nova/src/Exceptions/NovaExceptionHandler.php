<?php

namespace Laravel\Nova\Exceptions;

use Closure;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Laravel\Nova\Nova;
use Laravel\Nova\Util;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class NovaExceptionHandler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     *
     * Used only on Laravel 8 and above.
     *
     * @return void
     */
    public function register()
    {
        with(Nova::$reportCallback, function ($handler) {
            /** @var (callable(\Throwable):void)|(\Closure(\Throwable):void)|null $handler */
            if ($handler instanceof Closure || is_callable($handler)) {
                $this->reportable(function (Throwable $e) use ($handler) {
                    call_user_func($handler, $e);
                })->stop();
            }
        });

        Nova::$reportCallback = null;
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $e)
    {
        with(Nova::$reportCallback, function ($handler) use ($e) {
            /** @var (callable(\Throwable):void)|(\Closure(\Throwable):void)|null $handler */
            if ($handler instanceof Closure || is_callable($handler)) {
                return call_user_func($handler, $e);
            }

            parent::report($e);
        });
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        if (Util::isNovaRequest($request)) {
            return $this->renderInertiaException($request, $this->prepareException($e));
        }

        return parent::render($request, $e);
    }

    /**
     * Render Inertia Exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface|\Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderInertiaException($request, $e)
    {
        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

        Inertia::setRootView('nova::layout');

        if ($statusCode === 403) {
            return Inertia::render('Nova.Error403')->toResponse($request)->setStatusCode($statusCode);
        } elseif ($statusCode === 404) {
            return Inertia::render('Nova.Error404')->toResponse($request)->setStatusCode($statusCode);
        }

        if ($request->inertia()) {
            return Inertia::render('Nova.Error')->toResponse($request)->setStatusCode(500);
        }

        return parent::render($request, $e);
    }
}
