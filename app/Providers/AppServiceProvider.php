<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Paginator::useBootstrapFive();

        Builder::macro('dynamicPaginate', function ($qtd = 10) {

            if (request()->pagination === 'none') {
                return $this->get();
            }

            $page = Paginator::resolveCurrentPage();

            $perPage = request()->perpage ? request()->perpage : null;
            if (empty($perPage)) {
                $perPage = $qtd;
            }

            $results = ($total = $this->toBase()->getCountForPagination())
                ? $this->forPage($page, $perPage)->get(['*'])
                : $this->model->newCollection();

            return $this->paginator($results, $total, $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
        });
    }
}
