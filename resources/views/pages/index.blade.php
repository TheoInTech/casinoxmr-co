@extends('layouts.app')

@section('content')
  <!-- Splash -->
  <div class="home-splash" id="home">
    <div class="row">
      <div class="col-12 col-sm-5 home-left">

        <img class="logo" src="{{ asset('images/common/logo-cm-full.svg') }}" alt="CasinoXMR Logo"/>
        
        <div class="card -home">
          <div class="card-body content-countdown">
            <span class="card-title">Next draw in</span>
            
              
              <div class="cd-item">
                <div class="number" id="day-number">--</div>
                <div class="rep" id="day-rep">days</div>
              </div>

              <div class="cd-item">
                <div class="number" id="hour-number">--</div>
                <div class="rep" id="hour-rep">hours</div>
              </div>

              <div class="cd-item">
                <div class="number" id="minute-number">--</div>
                <div class="rep" id="minute-rep">minutes</div>
              </div>

              <div class="cd-item">
                <div class="number" id="second-number">--</div>
                <div class="rep" id="second-rep">seconds</div>
              </div>


          </div>
          <div class="card-footer -home content-pot">
            <span class="pot-title text-orange">
              Current pot
            </span>
            <span class="pot-value">
              $ {{ $potSize }}
            </span>
          </div>
        </div>

      </div>

      <div class="col-12 offset-0  col-sm-5 offset-sm-1 home-right">
        <h1 class="head">
          A lottery,<br>FREE of the fee.
        </h1>
        <h2 class="body">
          Join the world's only Monero lottery.<br>
          No fees, just use your PC.
        </h2>
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

        <span class="head">
          No Monero address yet?
          <a href="https://mymonero.com/" class="mymonero" target="_blank">
            Create new monero address
          </a>
        </span>
        
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