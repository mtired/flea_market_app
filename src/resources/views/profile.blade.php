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
        <div class="profile__avatar">
          @if(!empty($profile?->image))
            <img src="{{ asset('storage/' . $profile->image) }}">
          @endif
        </div>
        <div class="profile__name">{{ $user->name }}</div>
      </div>

      <a href="/mypage/profile" class="profile__edit-btn">プロフィールを編集</a>
    </section>

    {{-- タブ --}}
    <nav class="profile__tabs">
      <a href="{{ route('mypage', ['page' => 'sell']) }}"
        class="profile__tab {{ $page === 'sell' ? 'is-active' : '' }}">
        出品した商品
      </a>

      <a href="{{ route('mypage', ['page' => 'buy']) }}"
        class="profile__tab {{ $page === 'buy' ? 'is-active' : '' }}">
        購入した商品
      </a>
    </nav>

    <div class="profile__divider"></div>

    {{-- 商品一覧 --}}
    <section class="profile__items">
      <div class="profile__grid">
        @forelse ($items as $item)
        <article class="profile__card">
          <a href="{{ route('items.show', $item->id) }}">
            <div class="profile__item-image">
              <img src="{{ asset($item->image_url) }}" alt="{{ $item->name }}">
            </div>
          </a>

          <p class="profile__item-name">{{ $item->name }}</p>
        </article>
        @empty
          <p class="profile__empty">
            {{ $page === 'buy' ? '購入した商品はありません' : '出品した商品はありません' }}
          </p>
        @endforelse
      </div>
    </section>

  </div>
</main>
@endsection