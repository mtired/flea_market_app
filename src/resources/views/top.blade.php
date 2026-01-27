@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/top.css') }}" />
@endsection

@section('content')
  <section class="items">

    {{-- タブ --}}
    <div class="items__tabs">
      <a href="{{ url('/') }}?{{ http_build_query(array_filter(['tab' => 'recommend', 'keyword' => request('keyword')])) }}"
        class="items__tab {{ $activeTab === 'recommend' ? 'is-active' : '' }}">
        おすすめ
      </a>

      <a href="{{ url('/') }}?{{ http_build_query(array_filter(['tab' => 'mylist', 'keyword' => request('keyword')])) }}"
        class="items__tab {{ $activeTab === 'mylist' ? 'is-active' : '' }}">
        マイリスト
      </a>
    </div>

    {{-- 商品一覧 --}}
    <div class="items__grid">
      @foreach ($products as $product)
        @php
          // public 配下のパスを想定：images/item_images/xxx.jpg
          $imgSrc = $product->image_url ? asset($product->image_url) : null;
        @endphp

        @if ((int) $product->status === 1)
          {{-- SOLD：リンクなし --}}
          <div class="item-card">
            <div class="item-card__img">
              <div class="item-card__sold">Sold</div>

              @if ($imgSrc)
                <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="item-card__image">
              @else
                <div class="item-card__placeholder">商品画像</div>
              @endif
            </div>

            <p class="item-card__name">{{ $product->name }}</p>
          </div>
        @else
          {{-- 未SOLD：カード全体リンク --}}
          <a href="{{ route('items.show', ['item' => $product->id]) }}" class="item-card item-card--link">
            <div class="item-card__img">
              @if ($imgSrc)
                <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="item-card__image">
              @else
                <div class="item-card__placeholder">商品画像</div>
              @endif
            </div>

            <p class="item-card__name">{{ $product->name }}</p>
          </a>
        @endif
      @endforeach
    </div>

  </section>
@endsection
