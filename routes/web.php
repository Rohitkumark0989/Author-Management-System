<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use RealRashid\SweetAlert\Facades\Alert;
Route::get('/sweet',function () {
    //Alert::success('Success Title','Success Message');
    toast('Success Toast','success');
    return view('welcome');
});
Route::get('/x',function () {
    $id = Auth::user()->id;
    $user = \App\Models\User::find($id);
    $count = 0;
    foreach ($user->notifications as $notification) {
        echo "<pre>";print_r($notification->data);echo "</pre>";
        if(\in_array($id,$notification->data) && array_key_exists('editor',$notification->data)){
            $notification->update(['data' => ['user_id' =>$notification->data['user_id'],'post_id' =>$notification->data['post_id'],'editor' => $notification->data['editor'],'view' => false]]);
            $count++;          
        }
    }
    echo $count;
    die;   
    // Create Notification
    $user  = Auth::user();
    $user->notify(new \App\Notifications\PostStatus(\App\Models\User::findOrfail($user->id)));
   
   //Upate  Notification as read
//    foreach(Auth::user()->notifications as $notification){
//        $notification->markAsRead();
//    }
// dd(Auth::user()->unreadNotifications);
    //$test = DB::table('notifications')->where('read_at',NULL)->get();
    //dd($test);
    foreach(Auth::user()->unreadNotifications as $notification){
        dd($notification);
    }
    // Get user name from notification
    //{{Auth::user()->notifications[0]->data['user_name']}}

});


//Route::get('/test', function () { echo "test";die;
    //return view('welcome');
//});
// die;

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/','App\Http\Controllers\PostController@index')->name('welcome');


Route::get('reate','App\Http\Controllers\PostController@create');


Route::get('/posts','App\Http\Controllers\PostController@loginedUserPost')->name('list_posts')->middleware('auth');
Route::group(['prefix' =>'posts','as' => 'posts.'],function(){
        Route::get('/drafts','App\Http\Controllers\PostController@drafts')
        ->name('list_drafts')
        ->middleware('auth');
        
        Route::get('/show/{id}','App\Http\Controllers\PostController@show')
        ->name('show_post')
        ->middleware('auth');

        Route::get('/page/{id}','App\Http\Controllers\PostController@showFront')
        ->name('show_front_post');
        //->middleware('auth');

        Route::get('/create','App\Http\Controllers\PostController@create')
        ->name('create_post')
        ->middleware('can:create-post');

        Route::post('/create','App\Http\Controllers\PostController@store')
        ->name('store_post')->middleware('can:create-post');

        Route::get('/edit/{post?}','App\Http\Controllers\PostController@edit')
        ->name('edit_post')
        ->middleware('can:update-post,post');

        Route::post('edit','App\Http\Controllers\PostController@update')
        ->name('update_post')
        ->middleware('can:update-post,post');

        Route::get('/publish/{post?}','App\Http\Controllers\PostController@publish')
               ->name('publish_post')
               ->middleware('can:publish-post');
        
        Route::get('/unpublish/{post?}','App\Http\Controllers\PostController@unPublish')
        ->name('unpublish_post')
        ->middleware('can:publish-post');       

        Route::get('notifications','App\Http\Controllers\PostController@allNotifications')
        ->name('notification_list');
        
        Route::get('published-posts','App\Http\Controllers\PostController@publishedPosts')
        ->name('published_posts')
        ->middleware('can:publish-post');
      
});