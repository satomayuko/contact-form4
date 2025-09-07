<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fashionablylate</title>

  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&display=swap" rel="stylesheet" />

  @yield('css')
</head>

@php
  $isAuthPage = request()->is('login') || request()->is('register');
  $hideHeader = request()->is('thanks');
@endphp

<body class="{{ $isAuthPage ? 'auth-page' : '' }}">
  @if (! $hideHeader)
    <header class="header">
      <div class="header__inner">
        <a class="header__logo" href="/">Fashionablylate</a>

        @php
          $onAdmin   = request()->routeIs('admin') || request()->is('admin');
          $onLogin   = request()->is('login');
          $onRegister= request()->is('register');
        @endphp

        @if ($onAdmin)
          @auth
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
              @csrf
              <button type="submit" class="header__login-btn">logout</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="header__login-btn">login</a>
          @endauth

        @elseif ($onRegister)
          <a href="{{ route('login') }}" class="header__login-btn">login</a>

        @elseif ($onLogin)
          <a href="{{ route('register') }}" class="header__login-btn">register</a>

        @else
          @auth
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
              @csrf
              <button type="submit" class="header__login-btn">logout</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="header__login-btn">login</a>
          @endauth
        @endif
      </div>
      <div class="header-line"></div>
    </header>
  @endif

  <main>
    <div class="page-1512">
      @yield('content')
    </div>
  </main>
</body>
</html>