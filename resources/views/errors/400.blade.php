@extends('layouts.app')

@section('content')
  <div class="section -notfound">
    <div class="main-container -notfound">
      <div class="main-header -notfound">
        <img class="logo -error" src="{{ asset('images/common/logo-cm-full.svg') }}" alt="CasinoXMR Logo"/>
        <br>
        400 Something went wrong
      </div>
        <div class="content -notfound">
          <div class="col-lg-4 col-lg-offset-4">
            <div class="row">
              <div class="col-lg-8 col-lg-offset-2">
                @if (!Auth::check())
                <a class="btn cm-btn btn-orange" href="/">Go back to Home</a>
                @else
                <a class="btn cm-btn btn-orange" href="/dashboard">Go back to Dashboard</a>
                @endif
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection