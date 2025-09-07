@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}?v={{ filemtime(public_path('css/register.css')) }}">
@endsection

@section('body-class', 'auth-page')

@section('content')
<div class="page-1512">
  <h2 class="register-title">Register</h2>

  <div class="register-form__content">
    <form id="register-form" class="form" action="{{ route('register') }}" method="POST" novalidate>
      @csrf

      <div class="form__group">
        <label class="form__label">お名前</label>
        <div class="form__group-content">
          <input type="text" name="name" value="{{ old('name') }}" placeholder="例: 山田 太郎">
          <div class="form__error">@error('name') {{ $message }} @enderror</div>
        </div>
      </div>

      <div class="form__group">
        <label class="form__label">メールアドレス</label>
        <div class="form__group-content">
          <input type="email" name="email" value="{{ old('email') }}" placeholder="例: test@example.com">
          <div class="form__error">@error('email') {{ $message }} @enderror</div>
        </div>
      </div>

      <div class="form__group">
        <label class="form__label">パスワード</label>
        <div class="form__group-content">
          <input type="password" name="password" placeholder="例: coachtech1106">
          <div class="form__error">@error('password') {{ $message }} @enderror</div>
        </div>
      </div>
    </form>

    <button type="submit" class="form__button-submit" form="register-form">登録</button>
  </div>
</div>
@endsection