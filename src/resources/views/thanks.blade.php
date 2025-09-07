@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/thanks.css') }}">
@endsection

@section('content')
  <script>
    document.documentElement.classList.add('no-header');
  </script>

  <div class="thanks-page">
    <p class="thanks-message">お問い合わせありがとうございました</p>

    <div class="thanks-actions">
      <a href="{{ url('/') }}" class="btn-home">HOME</a>
    </div>
  </div>
@endsection