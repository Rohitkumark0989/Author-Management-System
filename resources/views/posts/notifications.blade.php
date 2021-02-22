@extends('layouts.app')

@section('content')
<div class="container">

 @foreach($allNotifications as $notification)
    <div class="row justify-content-center mt-4" >
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h3>{{$notification[0]->title}}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                     <div class="col-6">
                           <h6>Published By: <strong class="text-info">{{$notification[1]->name}}</strong></h6>
                     </div>
                     <div class="col-4">
                           <h6 >Published On: <strong class="text-info">{{date('d M Y', strtotime($notification[0]->updated_at))}}</strong></h6>
                     </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
 @endforeach   
</div>
@endsection