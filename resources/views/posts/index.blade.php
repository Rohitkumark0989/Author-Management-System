@extends('layouts.app')

@section('content')
<div class="container">
    @if(Auth::user() && Gate::denies('see-all-posts'))
      <h1 class="text-center">Published & Unpublisehd Posts</h1>
    @endif  
   @can('create-post')
    <div class="row">
        <div class="col-md-4">
              <a href="{{route('posts.create_post')}}" class="publish-btn">Add New</a>
        </div>
    </div>
   @endcan
   <div class="row">

   @foreach($posts as $post)
      <div class="col-md-4 ">
         <div class="single-blog">
            <p class="blog-meta">
               By {{$post->name}} <span>{{date('d M Y', strtotime($post->created_at))}}</span>
            </p>
            @if($post->postimage)
              <img src="{{asset('images')}}/{{$post->postimage}}" alt="">
            @else
              <img src="https://via.placeholder.com/200" alt="">
            @endif

            @if(Auth::user())
              <h2><a href="{{route('posts.show_post',['id' =>$post->id])}}">{{$post->title}}</a></h2>
            @else
              <h2><a href="{{route('posts.show_front_post',['id' =>$post->id])}}">{{$post->title}}</a></h2> 
            @endif
            <p class="blog-text">
                  {{str_limit($post->body,100)}} 
            </p>
            @if(isset($post[0]) && !empty($post[0]))
              @can('update-post',$post)
              <p>
                    <a href="{{route('posts.edit_post',['id' =>$post->id])}}" class="read-more-btn">Edit</a>
              </p>
              @endcan
            @endif
            @if(Auth::user() && $post['published'])
              <p>
                <button type="button" class="btn btn-secondary btn-sm " style="border-radius:18px; height:31px; !important" disabled>Published</button>
              </p>
            @endif
         </div>
      </div>
    @endforeach  
   </div>
</div>
@endsection
