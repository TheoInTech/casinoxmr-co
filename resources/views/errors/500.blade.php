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
    <img class="error-icon" src="{{ asset('images/common/ic-error-500.svg') }}" alt="Icon"/>
    <div class="error-title">Something went wrong</div>
    @if (!Auth::check())
      <a class="error-button" href="/">Back to home</a>
    @else
      <a class="error-button" href="/dashboard">Back to dashboard</a>
    @endif
  </div>
@endsection