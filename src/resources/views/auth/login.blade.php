@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ filemtime(public_path('css/login.css')) }}">
@endsection

@section('content')
<h2 class="login-title">Login</h2>

<div class="login-card">
  <form id="login-form" class="login-form" method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <div class="form__group">
      <label class="form__label">メールアドレス</label>
      <div class="form__group-content">
        <input type="email" name="email" value="{{ old('email') }}" placeholder="例: test@example.com">
        <div class="form__error">
          @error('email') {{ $message }} @enderror
        </div>
      </div>
    </div>

    <div class="form__group">
      <label class="form__label">パスワード</label>
      <div class="form__group-content">
        <input type="password" name="password" placeholder="例: password1234">
        <div class="form__error">
          @error('password') {{ $message }} @enderror
        </div>
      </div>
    </div>
  </form>

  <button type="submit" class="form__button-submit" form="login-form">ログイン</button>
</div>
@endsection