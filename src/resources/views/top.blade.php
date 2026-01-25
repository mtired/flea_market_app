@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/top.css') }}" />
@endsection

@section('content')
<section class="items">

  {{-- タブ --}}
  <div class="items__tabs">
    <a
      href="{{ url('/') }}?{{ http_build_query(array_filter(['tab' => 'recommend', 'keyword' => request('keyword')])) }}"
      class="items__tab {{ $activeTab === 'recommend' ? 'items__tab--active' : '' }}"
    >
      おすすめ
    </a>

    <a
      href="{{ url('/') }}?{{ http_build_query(array_filter(['tab' => 'mylist', 'keyword' => request('keyword')])) }}"
      class="items__tab {{ $activeTab === 'mylist' ? 'items__tab--active' : '' }}"
    >
      マイリスト
    </a>
  </div>

  {{-- 商品一覧 --}}
<div class="items__grid">
  @foreach ($products as $product)
    @if($product->status === 1)
      {{-- SOLD：リンクなし --}}
      <div class="item-card">
        <div class="item-card__img">
          <div class="item-card__sold">Sold</div>

          @if($product->image_url)
            <img
              src="{{ $product->image_url }}"
              alt="{{ $product->name }}"
              class="product-detail__image"
            >
          @else
            商品画像
          @endif
        </div>

        <p class="item-card__name">{{ $product->name }}</p>
      </div>
    @else
      {{-- 未SOLD：リンクあり（画像クリックで詳細へ） --}}
      <a href="{{ route('items.show', ['item' => $product->id]) }}" class="item-card">
        <div class="item-card__img">
          @if($product->image_url)
            <img
              src="{{ $product->image_url }}"
              alt="{{ $product->name }}"
              class="product-detail__image"
            >
          @else
            商品画像
          @endif
        </div>

        <p class="item-card__name">{{ $product->name }}</p>
      </a>
    @endif

  @endforeach
</div>
</section>
@endsection