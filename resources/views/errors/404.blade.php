@extends('layouts.app')

@section('content')
  <!-- Splash -->
  <div class="home-splash" id="home">
    
  </div>

  <!-- Address -->
  <div class="section -address" id="address">
    
  </div>

  <!-- Error Container -->
  <div class="error-container">
    <img class="error-icon" src="{{ asset('images/common/ic-error-404.svg') }}" alt="Icon"/>
    <div class="error-title">Page not found</div>
    <div class="error-description">We can't seem to find the page you are looking for.</div>
    @if (!Auth::check())
      <a class="error-button" href="/">Back to home</a>
    @else
      <a class="error-button" href="/dashboard">Back to dashboard</a>
    @endif
  </div>
@endsection