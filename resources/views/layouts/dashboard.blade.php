<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}">

        <link href="{{ mix('/css/app.min.css') }}" rel="stylesheet">

        <title>@yield('title', 'CasinoXMR Dashboard')</title>

    </head>
    <body>
        <!-- Sidenav -->
        <div class="container-fluid body-content">
            <!-- Top banner for chips generation -->
            @include('includes.topbanner')
            <div class="row row-offcanvas row-offcanvas-left">

                <aside class="col-12 col-sm-1" id="sidebar" role="navigation">
                    <div class="sidebar-nav">
                        <ul class="navbar-nav">
                            <li class="nav-item active col-4 col-sm-12 no-padding no-margin">
                                <a class="nav-link pl-0" id="nav-main" href="#"><i class="fas fa-th-large fa-2x"></i></a>
                            </li>
                            <li class="nav-item col-4 col-sm-12 no-padding no-margin">
                                <a class="nav-link pl-0" id="nav-transactions" href="#"><i class="fas fa-exchange-alt fa-2x"></a></i>
                            </li>
                            <li class="nav-logo col-4 col-sm-12 no-padding no-margin">
                                <a class="nav-link pl-0" href="/logout"><i class="fas fa-user fa-2x"></a></i>
                            </li>
                        </ul>
                    </div>
                </aside>

                <div class="col-12 offset-0 col-sm-11 offset-sm-1 dashboard-content">
                    <div class="header">
                        <ul class="menu-list">
                            <li class="menu-title">Dashboard</li>

                            <li class="menu-how">
                                <a class="menu-link" data-toggle="modal" data-target="#tutorial-modal" id="how-to-play">How to Play?</a>
                            </li>
                        </ul>
                    </div>
                    <div class="content">
                        <!-- Dashboard page contents -->
                        @include('pages.dashboard.main')
                        @include('pages.dashboard.transactions')
                    </div>
                </div>

            </div>
        </div>

        @include('includes.tutorials')

        <!-- Countdown modal -->
        <div class="modal fade" id="countdown-modal" tabindex="-1" role="dialog" aria-labelledby="countdown-modal-title" aria-hidden="true" data-current="intro">
          <div class="modal-dialog" role="document" id="countdown-dialog">
            <div class="modal-content">
              <div class="modal-body">
                <h5 class="title" id="countdown-modal-title"></h5>
                <div class="row content" id="countdown-modal-content">
                  <div class="col-12 countdown-modal-content" style="padding: 0px;">

                  </div>
                </div>  
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="tutorial-skip">Got it!</button>
              </div>
            </div>
          </div>
        </div>

        {{ csrf_field() }}

        <input type="hidden" id="coinimp-script" value="{{ Config::get('coinimp.script') }}">
        <input type="hidden" id="raffle-date" value="{{ $nextRaffleDate }}">
        <input type="hidden" id="current-date" value="{{ $currentDate }}">

        <!-- End of content -->
        <script src="{{ mix('/js/app.min.js') }}"></script>
        <script src="{{ mix('/js/dashboard.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>

    </body>
</html>