@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<main class="profile">
  <div class="profile__inner">

    {{-- 上段：プロフィール情報 --}}
    <section class="profile__header">
      <div class="profile__user">
        <div class="profile__avatar" aria-label="プロフィール画像"></div>
        <div class="profile__name">ユーザー名</div>
      </div>

      <a href="#" class="profile__edit-btn">プロフィールを編集</a>
    </section>

    {{-- タブ --}}
    <nav class="profile__tabs">
      <a href="#" class="profile__tab is-active">出品した商品</a>
      <a href="#" class="profile__tab">購入した商品</a>
    </nav>

    <div class="profile__divider"></div>

    {{-- 商品一覧 --}}
    <section class="profile__items">
      <div class="profile__grid">
        @for ($i = 0; $i < 8; $i++)
          <article class="profile__card">
            <div class="profile__item-image">商品画像</div>
            <p class="profile__item-name">商品名</p>
          </article>
        @endfor
      </div>
    </section>

  </div>
</main>
@endsection