<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Post;
use App\Models\Post;
use DB;
use Auth;
use Gate;
use Image;
class PostController extends Controller
{
    public function index(){    
        $posts=Post::where('published',true)->join('users','users.id' , '=' ,'posts.user_id')->select('posts.*','users.name')->paginate(20);
        return view('posts.index',compact('posts'));
      }

    public function create()
    {     
        return view('posts.create'); 
    }

    /**
     * Post detail page for logined users.
     *
     * @param  int  $id
     * @return Response
     */    
    public function show($id)
    {       
        $post = Post::where('posts.id',$id)->join('users','users.id' , '=' ,'posts.user_id')->select('posts.*','users.name')->first();

        if(Gate::denies('only-editor-view'))
        { 
            $post = Post::where('posts.id',$id)->where('user_id',Auth::user()->id)->join('users','users.id' , '=' ,'posts.user_id')->first(); 
        } 
        return  $post?view('posts.show',compact('post')):abort(404);
    }

     /**
     * Post detail page for guest.
     *
     * @param  int  $id
     * @return Response
     */
    public function showFront($id)
    {
        $post = Post::where('posts.id',$id)->where('published',true)->join('users','users.id' , '=' ,'posts.user_id')->first();
        return  $post?view('posts.show',compact('post')):abort(404);
    }

    /**
     * Insert new post here.
     *
     * @param  Request  $request
     * @return Response
     */    
    public function store(Request $request)
    {        

        $data = $request->only('title','body');
        $image = $request->file('file');
        if($image)
        {
            $imageName = time().'.'.$image->extension();
            $image->move(\public_path('images'),$imageName);
            $data['postimage'] = $imageName;
        }
        
        $data['slug'] = str_slug($data['title']);
        $data['user_id'] = Auth::user()->id;
        $post = Post::create($data);
        // Notification inserted here
        $user  = Auth::user();
        $user->notify(new \App\Notifications\PostStatus(\App\Models\User::findOrfail($user->id),$post->id));
        $msg = "Post Created Successfully";
        return redirect()->route('posts.list_drafts')->with(['id' => $post->id,'toast_success' =>$msg]);
    }

    /**
     * Get detail of the users for edit
     *
     * @param  Request  $request
     * @return Response
     */
    public function edit(Request $request,Post $post)
    {   
       $id = (int) $request->input('id');
       $post = Post::find($id);
       return $post?view('posts.edit',compact('post')):abort(403);
               
    }
    
     /**
     * Post update method
     *
     * @param  Request  $request
     * @return Response
     */

    public function update(Request $request)
    {   
        $id = $request->input('id');
        $data = Post::find($id);
        $data->title = $request->input('title');
        $data->body = $request->input('body');
        $image = $request->file('file');
        
        if($image == null){
            if($data->postimage !=null)
            {
                $data->postimage = $data->postimage;
            }
        }
        else
        {
            $imageName = time().'.'.$image->extension();
            $image->move(\public_path('images'),$imageName);
            $data->postimage = $imageName;
        }  
        $data->save();
        return redirect()->route('posts.list_drafts');

    }

    public function drafts()
    {   
       $draftsQuery = Post::where('published',false);
       
       if(Gate::denies('see-all-drafts'))
       {
         $draftsQuery = $draftsQuery->where('user_id',auth()->user()->id); 
       }
       $draftsQuery = $draftsQuery->join('users','users.id' , '=' ,'posts.user_id')->select('posts.*','users.name','users.id as author_id');
       $posts = $draftsQuery->get();
       return view('posts.drafts',compact('posts'));
    }

    public function publish(Request $request,Post $post)
    {    
        $authorId = ((int) $request->input('author_id'));
        $user = \App\Models\User::find($authorId);
        $id = (int) $request->input('id');
        
        $post = Post::find($id);
        $post->published = true;
        $post->published_by = auth()->user()->id;
        $post->save();
        foreach ($user->unreadNotifications as $notification) {
            if(\in_array($id,$notification->data)){ 
                $notification->markAsRead();
                $notification->update(['data' => ['user_id' =>$authorId,'post_id' =>$id,'editor' => auth()->user()->id,'view' => false]]);
            }
        }
        $msg = "Post Published Successfully";
        return back()->with(['toast_success' =>$msg]);
    }

    public function unPublish(Request $request,Post $post)
    {
        $authorId = ((int) $request->input('author_id'));
        $user = \App\Models\User::find($authorId);
        $id = (int) $request->input('id');

        $post = Post::find($id);
        $post->published = false;
        $post->un_published_by = auth()->user()->id;
        $post->save();
        foreach ($user->readnotifications as $notification) {
            if(\in_array($id,$notification->data) && $notification->data['post_id'] == $id){ 
                $notification->markAsUnRead();
                $notification->update(['data' => ['user_id' =>$authorId,'post_id' =>$id,'editor' => auth()->user()->id]]);
            }
        }
        $msg = "Post Unpublished Successfully";
        return back()->with(['toast_success' =>$msg]);
    }
    public function loginedUserPost()
    {   
        if(Gate::denies('see-all-posts'))
        {
            $posts = Post::where('user_id',Auth::user()->id)->paginate(20);
            return view('posts.index',compact('posts')); 
        }
        abort(403, 'Unauthorized action.');
        
    }

    public function allNotifications()
    {   
        $id = Auth::user()->id;
        $user = \App\Models\User::find($id);
        $count = 0;
        $allNotifications = [];
         
        $this->getAuthorNotification($allnotification = true);
        
        foreach ($user->readNotifications  as $notification) {
            
            if(\in_array($id,$notification->data) && array_key_exists('editor',$notification->data))
            {   

                 $notification->update(['data' => [
                     'user_id' =>$id,
                     'post_id' =>$notification->data['post_id'],
                     'editor' => $notification->data['editor'],
                     'view' => true
                     ]]);
                     
                 $notificationData=Post::where('id',$notification->data['post_id'])->get();
                 $userData=$user::where('id',$notification->data['editor'])->get(['name']);
                 $collection = collect($notificationData);
                 $merged     = $collection->merge($userData);
                 $allNotifications[]   = $merged->all();
                 
            }
        }
         
        //return view('posts.notifications',compact('allNotifications'));
        return view('posts.notifications')->with('allNotifications',$allNotifications);
    }
    public static  function getEditorNotification()
    {
       return $unread = DB::table('notifications')->where('read_at',NULL)->count();
    }

    public static function getAuthorNotification($allnotification=false){
        
        $id = Auth::user()->id;
        $user = \App\Models\User::find($id);
        $count = 0;

        if($allnotification || request()->segment(2) == 'notifications'){
            return $count;
            exit;        
        }
        
        foreach ($user->notifications as $k => $notification) {
            if(\in_array($id,$notification->data) && isset($notification->data['view']) && $notification->data['view'] == false){
                $count++;          
            }
        }
        return $count;
    }

    public function createThumbnail($path,$width,$height)
    {  
       $img = Image::make($path)->resize($width,$height,function($constraint){
           $constraint->aspectRatio();
       });
       $img->save($path);

        // $img = Image::make($path)->resize($width,$height)->save($path);
    }

    public function publishedPosts()
    {
        $draftsQuery = Post::where('published',true)->where('published_by',auth()->user()->id);
       
       if(Gate::denies('see-all-drafts'))
       {
         $draftsQuery = $draftsQuery->where('user_id',auth()->user()->id); 
       }
       $draftsQuery = $draftsQuery->join('users','users.id' , '=' ,'posts.user_id')->select('posts.*','users.name','users.id as author_id');
       $posts = $draftsQuery->get();
       
       return view('posts.published',compact('posts'));          
    }
}
