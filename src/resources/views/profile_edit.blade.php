@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}" />
@endsection

@section('content')
<main class="profile-edit">
  <div class="profile-edit__inner">

    <h1 class="profile-edit__title">プロフィール設定</h1>

    <form class="profile-edit__form" action="mypage/profile" method="get" enctype="multipart/form-data">
      @csrf

      {{-- プロフィール画像 --}}
      <div class="profile-edit__image-row">
        <div class="profile-edit__image-circle">
          {{-- 画像プレビューを入れるなら img を表示（JSで差し替えOK） --}}
          @if(!empty($user->profile_image_url))
            <img class="profile-edit__image" src="{{ $user->profile_image_url }}" alt="プロフィール画像">
          @endif
        </div>

        <div class="profile-edit__image-actions">
          <label class="profile-edit__image-button">
            画像を選択する
            <input class="profile-edit__image-input" type="file" name="profile_image" accept="image/*">
          </label>
        </div>
      </div>

      {{-- ユーザー名 --}}
      <div class="profile-edit__group">
        <label class="profile-edit__label" for="name">ユーザー名</label>
        <input
          class="profile-edit__input"
          id="name"
          name="name"
          type="text"
          value="{{ old('name', $user->name ?? '') }}"
        >
      </div>

      {{-- 郵便番号 --}}
      <div class="profile-edit__group">
        <label class="profile-edit__label" for="postcode">郵便番号</label>
        <input
          class="profile-edit__input"
          id="postcode"
          name="postcode"
          type="text"
          value="{{ old('postcode', $user->postcode ?? '') }}"
        >
      </div>

      {{-- 住所 --}}
      <div class="profile-edit__group">
        <label class="profile-edit__label" for="address">住所</label>
        <input
          class="profile-edit__input"
          id="address"
          name="address"
          type="text"
          value="{{ old('address', $user->address ?? '') }}"
        >
      </div>

      {{-- 建物名 --}}
      <div class="profile-edit__group">
        <label class="profile-edit__label" for="building">建物名</label>
        <input
          class="profile-edit__input"
          id="building"
          name="building"
          type="text"
          value="{{ old('building', $user->building ?? '') }}"
        >
      </div>

      <button class="profile-edit__submit" type="submit">更新する</button>
    </form>

  </div>
</main>
@endsection