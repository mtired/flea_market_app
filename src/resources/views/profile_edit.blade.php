@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}" />
@endsection

@section('content')
<main class="profile-edit">
  <div class="profile-edit__inner">
    <h1 class="profile-edit__title">プロフィール設定</h1>

    <form
        class="profile-edit__form"
        action="{{ route('profile.update') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

      {{-- 画像 --}}
      <div class="profile-edit__image-row">
        <div class="profile-edit__image">
          @if(!empty($profile?->image_url))
            <img
              id="jsProfilePreview"
              class="profile-edit__image-preview"
              src="{{ asset('storage/' . $profile->image_url) }}"
              alt="プロフィール画像"
            >
          @else
            <div id="jsProfilePlaceholder" class="profile-edit__image-placeholder"></div>
            <img id="jsProfilePreview" class="profile-edit__image-preview is-hidden" alt="プロフィール画像プレビュー">
          @endif
        </div>

        <div class="profile-edit__image-action">
          <input
            id="image"
            class="profile-edit__file"
            type="file"
            name="image"
            accept="image/*"
          >
          <label class="profile-edit__file-button" for="image">画像を選択する</label>

          @error('profile')
            <p class="profile-edit__error">{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- 入力 --}}
      <div class="profile-edit__group">
        <label class="profile-edit__label" for="name">ユーザー名</label>
        <input
          class="profile-edit__input profile-edit__input--bold"
          id="name"
          name="name"
          type="text"
          value="{{ old('name', $user->name ?? '') }}"
          placeholder="既存の値が入力されている"
        >
        @error('name')
          <p class="profile-edit__error">{{ $message }}</p>
        @enderror
      </div>

      <div class="profile-edit__group">
        <label class="profile-edit__label" for="postal_code">郵便番号</label>
        <input
          class="profile-edit__input"
          id="postal_code"
          name="postal_code"
          type="text"
          value="{{ old('postal_code', $profile->postal_code ?? '') }}"
          placeholder="既存の値が入力されている"
        >
        @error('postal_code')
          <p class="profile-edit__error">{{ $message }}</p>
        @enderror
      </div>

      <div class="profile-edit__group">
        <label class="profile-edit__label" for="address">住所</label>
        <input
          class="profile-edit__input"
          id="address"
          name="address"
          type="text"
          value="{{ old('address', $profile->address ?? '') }}"
          placeholder="既存の値が入力されている"
        >
        @error('address')
          <p class="profile-edit__error">{{ $message }}</p>
        @enderror
      </div>

      <div class="profile-edit__group">
        <label class="profile-edit__label" for="building">建物名</label>
        <input
          class="profile-edit__input"
          id="building"
          name="building"
          type="text"
          value="{{ old('building', $profile->building ?? '') }}"
          placeholder="既存の値が入力されている"
        >
        @error('building')
          <p class="profile-edit__error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 更新ボタン --}}
      <button class="profile-edit__submit" type="submit">更新する</button>
    </form>
  </div>
</main>

{{-- 画像プレビュー（任意） --}}
<script>
  (function () {
    const input = document.getElementById('profile_image');
    const preview = document.getElementById('jsProfilePreview');
    const placeholder = document.getElementById('jsProfilePlaceholder');

    if (!input || !preview) return;

    input.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (!file) return;

      const url = URL.createObjectURL(file);
      preview.src = url;
      preview.classList.remove('is-hidden');
      if (placeholder) placeholder.style.display = 'none';
    });
  })();
</script>
@endsection