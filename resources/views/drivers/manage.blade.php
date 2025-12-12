<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>lb3</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&family=Montserrat:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body class="page-driver-manage">
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
            Управление карточками сезона 2021
        </h1>

        <div class="container my-5">
            <div class="row justify-content-center mb-4">
                <div class="col-12 col-md-8 col-lg-6">
                    <form method="GET" action="{{ route('drivers.manage') }}">
                        <div class="form-group">
                            <label for="driver_id">Выберите карточку</label>
                            <select name="driver_id" id="driver_id" class="form-control" onchange="this.form.submit()">
                                <option value="">Новая карточка</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}"
                                        @if(optional($selectedDriver)->id === $d->id) selected @endif>
                                    @if(Auth::user()->is_admin)
                                        {{ $d->title }}
                                        ({{ optional($d->user)->name ?? 'без владельца' }}@if($d->trashed()) / удалена@endif)
                                    @else
                                        {{ $d->title }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                
                    @php
                        $isEdit = isset($selectedDriver);
                    @endphp

                    <form id="driver-form" method="POST"action="{{ $isEdit ? route('drivers.update', $selectedDriver) : route('drivers.store') }}"novalidate>
                        @csrf
                        @if($selectedDriver)
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="title">Заголовок</label>
                            <input type="text"
                                id="title"
                                name="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $selectedDriver->title ?? '') }}"
                                required
                                maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="track_name">Трасса</label>
                            <input type="text"
                                id="track_name"
                                name="track_name"
                                class="form-control @error('track_name') is-invalid @enderror"
                                value="{{ old('track_name', $selectedDriver->track_name ?? '') }}"
                                maxlength="255">
                            @error('track_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="short_description">Краткое описание</label>
                            <textarea id="short_description"
                                    name="short_description"
                                    class="form-control @error('short_description') is-invalid @enderror"
                                    rows="3"
                                    required
                                    maxlength="2000">{{ old('short_description', $selectedDriver->short_description ?? '') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="details_html">Детальное описание (HTML)</label>
                            <textarea id="details_html"
                                    name="details_html"
                                    class="form-control @error('details_html') is-invalid @enderror"
                                    required
                                    rows="6">{{ old('details_html', $selectedDriver->details_html ?? '') }}</textarea>
                            @error('details_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image_path">Путь к картинке (в storage)</label>
                            <input type="text"
                                id="image_path"
                                name="image_path"
                                class="form-control @error('image_path') is-invalid @enderror"
                                value="{{ old('image_path', $selectedDriver->image_path ?? '') }}"
                                maxlength="255"
                                pattern="^.+\.(jpg|jpeg|png|svg)$">
                            @error('image_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>

                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <button type="submit"
                                form="driver-form"
                                class="btn btn-success">
                            {{ $selectedDriver ? 'Сохранить изменения' : 'Добавить карточку' }}
                        </button>

                        @if($selectedDriver)
                            @if(!$selectedDriver->trashed())
                                @can('driver-delete', $selectedDriver)
                                    <form action="{{ route('drivers.destroy', $selectedDriver) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            Удалить (soft delete)
                                        </button>
                                    </form>
                                @endcan
                            @else
                                @can('driver-restore', $selectedDriver)
                                    <form action="{{ route('drivers.restore', $selectedDriver->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            Восстановить
                                        </button>
                                    </form>
                                @endcan

                                @can('driver-force-delete', $selectedDriver)
                                    <form action="{{ route('drivers.force-delete', $selectedDriver->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Удалить окончательно?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            Удалить окончательно
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        @endif

                    </div>


                </div>
            </div>
        </div>

        <div class="bottom d-flex align-items-center justify-content-between py-3">
            
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
    </body>
</html>
