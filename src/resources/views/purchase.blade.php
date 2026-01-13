@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection


@section('content')
<main class="purchase">
  <div class="purchase__inner">

    {{-- 左側 --}}
    <section class="purchase__left">

      {{-- 商品情報 --}}
      <div class="purchase__item">
        <div class="purchase__thumb">
          @if(!empty($item->image))
            <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="purchase__img">
          @else
            <div class="purchase__img-placeholder">商品画像</div>
          @endif
        </div>

        <div class="purchase__item-info">
          <p class="purchase__item-name">{{ $item->name ?? '商品名' }}</p>
          <p class="purchase__item-price">¥{{ number_format($item->price ?? 47000) }}</p>
        </div>
      </div>

      <div class="purchase__line"></div>

      {{-- 支払い方法 --}}
      <div class="purchase__block">
        <p class="purchase__block-title">支払い方法</p>

        <select class="purchase__select" name="payment_method" form="purchase-form" required>
          <option value="" selected disabled>選択してください</option>
          <option value="convenience">コンビニ払い</option>
          <option value="card">カード払い</option>
          <option value="bank">銀行振込</option>
        </select>
      </div>

      <div class="purchase__line"></div>

      {{-- 配送先 --}}
      <div class="purchase__block">
        <div class="purchase__address-head">
          <p class="purchase__block-title">配送先</p>
          <a href="{{ route('purchase.address') }}" class="purchase__address-link">変更する</a>
        </div>

        <div class="purchase__address-body">
          <p class="purchase__address-text">〒 {{ $address->postal_code ?? 'XXX-YYYY' }}</p>
          <p class="purchase__address-text">{{ $address->address ?? 'ここには住所と建物が入ります' }}</p>
        </div>
      </div>

    </section>

    {{-- 右側 --}}
    <aside class="purchase__right">
      <div class="purchase__summary">
        <div class="purchase__row">
          <p class="purchase__row-label">商品代金</p>
          <p class="purchase__row-value">
            <span class="purchase__row-yen">¥</span>{{ number_format($item->price ?? 47000) }}
          </p>
        </div>

        <div class="purchase__summary-line"></div>

        <div class="purchase__row">
          <p class="purchase__row-label">支払い方法</p>
          <p class="purchase__row-method" id="payment-preview">コンビニ払い</p>
        </div>
      </div>

      <form id="purchase-form" action="{{ route('purchase.store', ['item' => $item->id ?? 1]) }}" method="post">
        @csrf
        <button type="submit" class="purchase__btn">購入する</button>
      </form>
    </aside>

  </div>

  <script>
    (function () {
      const select = document.querySelector('.purchase__select');
      const preview = document.getElementById('payment-preview');
      if (!select || !preview) return;

      const labelMap = {
        convenience: 'コンビニ払い',
        card: 'カード払い',
      };

      select.addEventListener('change', () => {
        preview.textContent = labelMap[select.value] ?? '---';
      });
    })();
  </script>
</main>
@endsection