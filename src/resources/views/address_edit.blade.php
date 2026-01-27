@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/address_edit.css') }}" />
@endsection

@section('content')
  <main class="address-edit">
    <div class="address-edit__inner">
      <h1 class="address-edit__title">住所の変更</h1>

      <form class="address-edit__form" action="{{ route('purchase.address.update', ['item' => $item->id]) }}" method="post">
        @csrf
        @method('PUT')

        {{-- 郵便番号 --}}
        <div class="address-edit__group">
          <label class="address-edit__label" for="postal_code">郵便番号</label>
          <input class="address-edit__input" id="postal_code" name="postal_code" type="text"
            value="{{ old('postal_code', $profile->postal_code ?? '') }}">
          @error('postal_code')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        {{-- 住所 --}}
        <div class="address-edit__group">
          <label class="address-edit__label" for="address">住所</label>
          <input class="address-edit__input" id="address" name="address" type="text"
            value="{{ old('address', $profile->address ?? '') }}">
          @error('address')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        {{-- 建物名 --}}
        <div class="address-edit__group">
          <label class="address-edit__label" for="building">建物名</label>
          <input class="address-edit__input" id="building" name="building" type="text"
            value="{{ old('building', $profile->building ?? '') }}">
          @error('building')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <button class="address-edit__button" type="submit">更新する</button>
      </form>
    </div>
  </main>
@endsection
