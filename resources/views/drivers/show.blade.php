<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">    
        <title>lb3</title>
        <meta name="description" content="cards about F1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&family=Montserrat:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body class="page-driver-show">
        <nav class = "navbar nav-topper py-0">
            <div class="container-fluid d-flex flex-nowrap align-items-start justify-content-between h-100">
                <a href="{{ route('home') }}" class="d-flex align-items-center ml-5 text-decoration-none">
                    <div>
                        <img src="{{ asset('storage/images/f1.svg') }}" class="icon d-block img-fluid" alt="f1"/>
                    </div>
                    <div class = "name ml-3 mt-2">
                        F1 сезон 2021
                    </div>
                </a>
                <a href="https://www.formula1.com/en/results/2021/races" id="download_button" class = "btn dwn-btn align-self-center mr-5">
                    Загрузить отчёт о сезоне
                </a>
            </div>
        </nav>
        <h1 class="my-3">
        Сезон Формулы 1 2021 года  
        </h1>

        <div id="detailsModal">
            <div class="container gd my-5 w-20">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="card cd w-100 h-100">

                            <div class="on_top">&nbsp;{{ $driver->track_name }}&nbsp;</div>

                            <a href="{{ $driver->image_url }}" target="_blank">
                                <img src="{{ $driver->image_url }}" class="card-img-top img-fluid" alt="{{ $driver->title }}">
                            </a>

                            <div class="card-body">
                                    <h2 class="card-title">{{ $driver->title }}</h2>
                                    <div id="detailsText" class="mt-3 ">
                                        @include('drivers.part.details')
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom d-flex align-items-center justify-content-between">

            @auth
                <div class="align-self-center d-flex align-items-center gap-2">
                    <span>{{ Auth::user()->name }}</span>

                    <span class="mx-1">|</span>

                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-logout p-0" style="background:none;border:none;color:#fff;cursor:pointer;">
                            Выйти
                        </button>
                    </form>
                </div>
            @else
                <div class="align-self-center">
                    Гость |
                    <a href="{{ route('login') }}">Войти</a>
                    /
                    <a href="{{ route('register') }}">Регистрация</a>
                </div>
            @endauth


            <div class="icons align-self-center">
                <a href="https://vk.com/offfol" class="icon d-block">
                    <img src="{{ asset('storage/images/vk.svg') }}" alt="vk" class="img-fluid"/>
                </a>
                <a href="https://github.com/St0pthink" class="icon d-block">
                    <img src="{{ asset('storage/images/github.svg') }}" alt="github" class="img-fluid " />
                </a>
            </div>
        </div>




    <div class="position-fixed mb-3 mr-3 warn">
        <div id="seasonToast" class="toast color_warn" data-autohide="true" data-delay="4000">
            <div class="toast-body d-flex align-items-center">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                <span>Отчёт о сезоне сейчас недоступен.</span>
            </div>
        </div>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
    <script>$(function () {make_view();});</script>
    </body>
</html>