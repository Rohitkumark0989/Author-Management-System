@extends('layouts.app')

@section('content')
<section style="padding-top-60px">
   <div class="container">
      <div class="row">
         <div class="col-md-6 offset-md-3">
             <div class="card">
                 <div class="card-header">
                      Update Post
                 </div>
                 <div class="card-body">
                  @if(Session::has('msg'))
                  <div class="alert alert-success" role="alert">
                     {{Session::get('msg')}}
                  </div>
                  @endif
                    <form method="POST" action="{{ route('posts.update_post')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('title')? 'has-error' : ''}}">
                            <label for="Title">Title</label>
                            <input type="text" name="title" class="form-control" value="{{$post->title??''}}" require onfocus>
                            <input type="hidden" name="id" value="{{$post->id??$post->id}}">
                            @if($errors->has('title'))
                              <span class="help-block">
                                 <strong>{{$errors->first('title')}}</strong>
                              </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('body') ? 'has-error' : ''}}">
                            <label for="body">Body</label>
                            <textarea type="text" name="body" id="body" cols="30" rows="10" class="form-control" readdird>{{$post->body??''}}</textarea>
                            @if($errors->has('body'))
                              <span class="help-block">
                                 <strong>{{$errors->first('body')}}</strong>
                              </span>
                           @endif   
                        </div>
                        <div class="form-group">
                           <label for="file">Choose Image</label>
                            <input type="file" name="file" class="form-control" onchange="previewFile(this)">
                            <img id="previewImg" src="{{asset('images')}}/{{$post->postimage}}" alt="post image" style="max-width:130px; margin-top:20px">  
                        </div>
                        <div class="form-group">
                           <button type="submit" class="btn btn-primary">
                           Update
                           </button>  
                        </div>
                    </form>
                 </div>
             </div> 
         </div>
      </div>
   </div>

</section>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
// $(function(){
//     Swal.fire({
//        'title':'Post update',
//        'text':'This is from sweetalert',
//        'type':'success',
//     })
// })
  function previewFile(input){
     var file=$("input[type=file]").get(0).files[0];
     if(file)
     {
        var reader = new FileReader()
        reader.onload = function(){
           $('#previewImg').attr("src",reader.result);
        }
        reader.readAsDataURL(file);
     }
  }
</script>
