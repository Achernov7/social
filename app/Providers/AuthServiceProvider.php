<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Group;
use App\Models\Image;
use App\Policies\Post\PostPolicy;
use App\Policies\Group\GroupPolicy;
use App\Policies\Image\ImagePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Group::class => GroupPolicy::class,
        Post::class => PostPolicy::class,
        Image::class => ImagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('valid-receiver-id', 'App\Http\Gates\ChatMessageGate\ChatMessageGate@validReceiverId');
        Gate::define('valid-relation', 'App\Http\Gates\FriendsGate\FriendsGate@validRelation');
    }
}
