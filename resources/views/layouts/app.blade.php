<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ABC BANK') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/bank.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <link rel="stylesheet" href="{{ asset('css/bank.css') }}">
</head>

<body>
    <div id="app">
        @auth
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm border-bottom">
            <div class="container">

                <a class="navbar-brand font-weight-bold" href="{{ url('/') }}">
                    {{ strtoupper(config('app.name', 'Laravel')) }}
                </a>
            </div>
        </nav>
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item {{ \Request::route()->getName()=='home' ? 'active': '' }}">
                            <a class="nav-link" href="{{ URL::to('/') }}">
                            <i class="fas fa-home"></i>
                                Home <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item {{ \Request::route()->getName()=='deposit' ? 'active': '' }}">
                            <a class="nav-link" href="{{ route('deposit') }}">
                            <i class="fas fa-cloud-upload-alt"></i>

                            Deposit
                            </a>
                        </li>
                        <li class="nav-item {{ \Request::route()->getName()=='withdraw' ? 'active': '' }}">
                            <a class="nav-link" href="{{ route('withdraw') }}">
                            <i class="fas fa-cloud-download-alt"></i>

                                Withdraw
                            </a>
                        </li>
                        <li class="nav-item {{ \Request::route()->getName()=='transfer' ? 'active': '' }}">
                            <a class="nav-link" href="{{ route('transfer') }}">
                            <i class="fas fa-exchange-alt"></i>

                            Transfer
                            </a>
                        </li>
                        <li class="nav-item {{ \Request::route()->getName()=='statement' ? 'active': '' }}">
                            <a class="nav-link" href="{{ route('statement') }}">
                            <i class="fas fa-file"></i>

                            Statement
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>

                                    Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @endauth
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>

</html>