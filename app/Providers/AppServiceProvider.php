<?php

namespace App\Providers;

use App\Components\RegisterNewEloquentFunction;
use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'user' => \App\Models\User::class,
            'group'=> \App\Models\Group::class,
            'post' => \App\Models\Post::class,
            'comment' => \App\Models\Comment::class,
            'music' => \App\Models\Music::class       
        ]);

        RegisterNewEloquentFunction::register();

    }
}
