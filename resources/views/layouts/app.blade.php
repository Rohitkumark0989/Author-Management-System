<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Posts Management System</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
 
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
      .custom-badge{
            margin-top: 0;
            width: 25px;
            height: 22px;
            padding-top: 6px;
        }
    </style>
     
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Posts Management System
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))

                                <li class="nav-item">
                                 <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            
                            @if (Route::has('register'))
                                <li class="nav-item ">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- Only editor can view -->
                            @if(!Gate::denies('only-editor-view'))
                                <li class="nav-item custom-badge">
                                   <span class="badge badge-pill badge-success ml-2"><a style="color: #fff !important;" href="{{route('posts.list_drafts')}}">{{\App\Http\Controllers\PostController::getEditorNotification()}}</a></span>
                                </li>
                            @endif
                           
                           <!-- Only Author can view -->
                            @if(Gate::denies('only-editor-view'))
                                <li class="nav-item custom-badge">
                                   <span class="badge badge-pill badge-success ml-2"><a style="color: #fff !important;" href="{{route('posts.notification_list')}}">{{\App\Http\Controllers\PostController::getAuthorNotification()}}</a></span>
                                </li>
                            @endif

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                 
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('posts.list_drafts') }}">
                                       Drafts
                                    </a>
                                    @if(!Gate::denies('only-editor-view'))
                                        <a class="dropdown-item" href="{{ route('posts.published_posts') }}">
                                           Published Posts
                                        </a>
                                    @endif
                                    @if(Gate::denies('see-all-posts'))
                                    
                                    <a class="dropdown-item" href="{{ route('posts.create_post') }}">
                                       New Post
                                    </a>
                                    <a class="dropdown-item" href="{{ route('list_posts') }}">
                                       All Posts
                                    </a>
                                    <a class="dropdown-item" href="{{ route('posts.notification_list') }}">
                                       Notifications
                                    </a>
                                    @endif 
                                   <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                    
                                </div>
                                
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
