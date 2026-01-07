<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>フリマアプリ</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>

<body>
<header class="header">
  <div class="header__inner">

    {{-- ロゴ --}}
    <a href="/" class="header__logo-link">
      <img
        src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}"
        alt="COACHTECH"
        class="header__logo"
      />
    </a>

    @unless (request()->is('login') || request()->is('register'))
    {{-- 検索 --}}
    <form class="header__search" action="/" method="get">
      <input
        class="header__search-input"
        type="text"
        name="keyword"
        placeholder="なにをお探しですか？"
      >
    </form>

    {{-- 右側メニュー --}}
    <nav class="header__nav">
      @auth
        <form action="/logout" method="get">
          @csrf
          <button class="header__nav-text" type="submit">ログアウト</button>
        </form>

        <a href="/mypage" class="header__nav-text">マイページ</a>

        <a href="/" class="header__sell-button">
          出品
        </a>
      @endauth

      @guest
        <a href="/login" class="header__nav-text">ログイン</a>
      @endguest
    </nav>
    @endunless
    
  </div>
</header>

<main>
  @yield('content')
</main>
</body>
</html>