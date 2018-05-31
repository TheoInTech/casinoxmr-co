<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="{{ mix('/css/app.min.css') }}" rel="stylesheet">

        <title>@yield('title', 'CasinoXMR | Cryptocurrency Wallet and Miner | 24/7 Crypto and Blockchain News')</title>

    </head>
    <body>
        <!-- Content -->
        @yield('content')
        <!-- End of content -->

        @include('includes.footer')
        @include('includes.privacypolicy')
        @include('includes.termsandconditions')

        <input type="hidden" id="raffle-date" value="{{ $nextRaffleDate }}">
        <input type="hidden" id="current-date" value="{{ $currentDate }}">

        <script src="{{ mix('/js/app.min.js') }}"></script>

        @yield('scripts')
    </body>
</html>