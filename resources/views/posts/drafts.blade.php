@extends('layouts.app')

@section('content')
<div class="container">
   <h1 class="text-center">All Posts In Drafts</h1>
   <div class="row">
   @if(session('success_message'))
      <div class="alert alert-success" role="alert">
         {{session('success_message')}}
      </div>
   @endif
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
            <h2><a href="{{route('posts.show_post',['id' =>$post->id])}}">{{$post->title}}</a></h2>
            <p class="blog-text">
                  {{str_limit($post->body,100)}} 
            </p>
            <p>
               <a href="{{route('posts.edit_post',['id' => $post->id])}}" class="read-more-btn">Edit</a>
               @can('publish-post')
                  <a href="{{route('posts.publish_post',['id' => $post->id,'author_id' => $post->author_id])}}" class="publish-btn">Publish</a>
               @endcan   
            </p>
            
         </div>
      </div>
    @endforeach  
   </div>
</div>
@include('sweetalert::alert')
@endsection

