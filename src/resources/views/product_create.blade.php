@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product_create.css') }}" />
@endsection

@section('content')
<main class="product-create">
  <div class="product-create__inner">

    <h1 class="product-create__title">商品の出品</h1>

    <form class="product-create__form" action="/sell" method="post" enctype="multipart/form-data">
      @csrf

      {{-- 商品画像 --}}
      <div class="product-create__block">
        <p class="product-create__label">商品画像</p>

        <div class="product-create__image-area">
          <input
            class="product-create__file-input"
            type="file"
            name="image"
            id="image"
            accept="image/*"
          >

          <label class="product-create__file-button" for="image">画像を選択する</label>

          {{-- プレビュー（任意） --}}
          <img id="imagePreview" class="product-create__preview" alt="" />
        </div>

        @error('image')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 商品の詳細 --}}
      <div class="product-create__section">
        <h2 class="product-create__section-title">商品の詳細</h2>

        {{-- カテゴリー --}}
        <div class="product-create__block">
        <p class="product-create__label">カテゴリー</p>

        <div class="product-create__categories">
            @foreach($categories as $category)
                <input
                    type="checkbox"
                    name="category_ids[]"
                    id="cat-{{ $category->id }}"
                    value="{{ $category->id }}"
                    class="product-create__category-input"
                    {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}
                >
                <label for="cat-{{ $category->id }}" class="product-create__category-pill">
                    {{ $category->name }}
                </label>
            @endforeach
        </div>

        @error('category_ids')
            <p class="form-error">{{ $message }}</p>
        @enderror
        </div>

        {{-- 商品の状態 --}}
        <div class="product-create__block">
        <p class="product-create__label">商品の状態</p>

        <select class="product-create__select" name="condition_id">
            <option value="">選択してください</option>
            @foreach($conditions as $condition)
                <option value="{{ $condition->id }}" {{ (string)old('condition_id') === (string)$condition->id ? 'selected' : '' }}>
                    {{ $condition->name }}
                </option>
            @endforeach
        </select>

          @error('condition_id')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- 商品名と説明 --}}
      <div class="product-create__section">
        <h2 class="product-create__section-title">商品名と説明</h2>

        <div class="product-create__block">
          <p class="product-create__label">商品名</p>
          <input
            class="product-create__input"
            type="text"
            name="name"
            value="{{ old('name') }}"
          >
          @error('name')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="product-create__block">
          <p class="product-create__label">ブランド名</p>
          <input
            class="product-create__input"
            type="text"
            name="brand"
            value="{{ old('brand') }}"
          >
          @error('brand')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="product-create__block">
          <p class="product-create__label">商品の説明</p>
          <textarea
            class="product-create__textarea"
            name="description"
          >{{ old('description') }}</textarea>
          @error('description')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="product-create__block">
          <p class="product-create__label">販売価格</p>
          <div class="product-create__price">
            <span class="product-create__yen">¥</span>
            <input
              class="product-create__price-input"
              type="text"
              name="price"
              value="{{ old('price') }}"
              inputmode="numeric"
            >
          </div>
          @error('price')
            <p class="form-error">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <button class="product-create__submit" type="submit">出品する</button>
    </form>

  </div>
</main>

{{-- 画像プレビュー（任意） --}}
<script>
  (function () {
    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    if (!input || !preview) return;

    input.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (!file) {
        preview.src = '';
        preview.style.display = 'none';
        return;
      }
      const url = URL.createObjectURL(file);
      preview.src = url;
      preview.style.display = 'block';
    });
  })();
</script>
@endsection