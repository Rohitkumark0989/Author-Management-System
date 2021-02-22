<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerPostPolicies();

        //
    }

    public function registerPostPolicies()
    {  
       Gate::define('create-post',function($user){ 
           return $user->hasAccess(['create-post']);  
       });
       
      //  Gate::define('update-post',function($user,$post){
      //   return $user->hasAccess(['update-post']) or $user->id == $post->user_id;  
      // });

       Gate::define('update-post',function($user,$post){ 
         $userId = Post::where('user_id', $user->id)->pluck('user_id');
         return $user->hasAccess(['update-post']) or $user->id == $userId[0];  
       });

       Gate::define('publish-post',function($user){ 
         return $user->hasAccess(['publish-post']);  
       });

       Gate::define('see-all-drafts',function($user){
        return $user->inRole('editor');  
      });
      
      Gate::define('see-all-posts',function($user){
        return $user->inRole('editor');  
      });

      Gate::define('only-editor-view',function($user){
        return $user->inRole('editor');  
      });
    }
}
