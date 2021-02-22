@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

      <!-- Post Content Column -->
      <div class="col-lg-8">

        <!-- Title -->
        <h1 class="mt-4">{{$post->title}}</h1>

        <!-- Author -->
        <p class="lead">
          by

          <a href="#">{{$post->name}}</a>
        </p>

        <hr>

        <!-- Date/Time -->
        <p>Posted on {{date('d M Y', strtotime($post->created_at))}} at {{@end(@explode(' ',$post->created_at))}}</p>

        <hr>

        <!-- Preview Image -->
        <img class="img-fluid rounded" src="{{asset('images')}}/{{$post->postimage}}" width="100%" alt="">

        <hr>

        <!-- Post Content -->
        <p class="lead">{{$post->body}}</p>


        <hr>

      </div>

    </div>
    <!-- /.row -->

  </div> 
@endsection