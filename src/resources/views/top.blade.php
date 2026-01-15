@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/top.css') }}" />
@endsection

@section('content')
<section class="items">

  {{-- タブ --}}
  <div class="items__tabs">
    <a
      href="/"
      class="items__tab {{ $activeTab === 'recommend' ? 'is-active' : '' }}"
    >
      おすすめ
    </a>
    <a
      href="/?tab=mylist"
      class="items__tab {{ $activeTab === 'mylist' ? 'is-active' : '' }}"
    >
      マイリスト
    </a>
  </div>

  {{-- 商品一覧 --}}
  <div class="items__grid">
    @foreach ($products as $product)
      <a href="items/{{ $product->id }}" class="item-card">
        <div class="item-card__img">
          <!-- Sold表示 -->
          @if($product->status === 1)
            <div class="item-card__sold">Sold</div>
          @endif
          
          @if($product->image_url)
            <img
              src="{{ asset($product->image_url) }}"
              alt="{{ $product->name }}"
            >
          @else
            商品画像
          @endif
        </div>
        <p class="item-card__name">
          {{ $product->name }}
        </p>
      </a>
    @endforeach
  </div>
</section>
@endsection