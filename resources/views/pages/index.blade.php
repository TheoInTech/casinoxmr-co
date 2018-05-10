@extends('layouts.app')

@section('content')
  <!-- Splash -->
  <div class="home-splash" id="home">
    <div class="row">
      <div class="col-sm-5 col-xs-12 text-left home-left">
        <img class="logo" src="{{ asset('images/common/logo-cm-full.svg') }}" alt="CasinoXMR Logo"/>
        <h1 class="head">
          A lottery, FREE of the fee.
        </h1>
        <h2 class="body">
          Join the world's only Monero lottery.<br>
          No fees, just use your PC.
        </h2>
      </div>

      <div class="col-sm-7 col-xs-12 text-center home-right">
        <span class="dollar-sign">&dollar;</span>
        <span class="value-list">
          @foreach($digits as $digit)
            <span class="value-item">{{ $digit }}</span>
          @endforeach
        </span>
        <div class="note-text">*Current pot</div>
      </div>
    </div>
  </div>

  <!-- Address -->
  <div class="section -address" id="address">
    <div class="main-container -address">
      <div class="header -address">
        <div class="head -address">Just enter your Monero address here!</div>
      </div>
      <div class="content -address">
        <form class="cm-form" method="POST" id="signup-form" action="/signup">
          <div class="input-group box-shadow">
            <input type="text" value="{{ old('address') }}" name="address" class="form-control cm-input" placeholder="Enter your monero address" maxlength="106" minlength="95" required autofocus>
            <div class="input-group-btn">
              <button type="submit" class="cm-btn btn-orange-gradient">Start mining</button>
            </div>
          </div>
          <input type="hidden" name="ref" value="{{ app('request')->input('ref') }}">
          {{ csrf_field() }}
          <div class="form-group">
            @if(!empty(Session::get('error_code')) && Session::get('error_code') == 'signup')
              <div class="alert alert-danger text-left">
                <ul class="list">
                  @if ($errors->any())
                    @foreach ($errors->all() as $error)
                      <li class="list-item">{{ $error }}</li>
                    @endforeach
                  @endif
                </ul>
              </div>
            @endif
          </div>
        </form>

        <a href="https://mymonero.com/" class="mymonero" target="_blank">
          Create new monero address
        </a>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  @if(!empty(Session::get('error_code')) && Session::get('error_code') == 'login')
    <script>
      $(function() {
          $('#loginModal').modal('show');
      });
    </script>
  @endif
@endsection