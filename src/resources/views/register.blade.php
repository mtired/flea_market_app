@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
@endsection

@section('content')
<main class="register">
  <div class="register__inner">
    <h1 class="register__title">会員登録</h1>
    <form class="register__form" action="/register" method="post">
      @csrf
      <div class="register__group">
        <label class="register__label" for="name">ユーザー名</label>
        <input class="register__input" id="name" name="name" type="text" value="{{ old('name') }}" />
      </div>

      <div class="register__group">
        <label class="register__label" for="email">メールアドレス</label>
        <input class="register__input" id="email" name="email" type="email" value="{{ old('email') }}" />
      </div>

      <div class="register__group">
        <label class="register__label" for="password">パスワード</label>
        <input class="register__input" id="password" name="password" type="password" />
      </div>

      <div class="register__group">
        <label class="register__label" for="password_confirmation">確認用パスワード</label>
        <input class="register__input" id="password_confirmation" name="password_confirmation" type="password" />
      </div>

      <div class="register__actions">
        <button class="register__button" type="submit">登録する</button>
      </div>

      <div class="register__login">
        <a class="register__login-link" href="#">ログインはこちら</a>
      </div>
    </form>
  </div>
</main>
@endsection