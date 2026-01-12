@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product_detail.css') }}" />
@endsection

@section('content')

<main class="product-detail">
  <div class="product-detail__inner">

    <div class="product-detail__top">
      {{-- 左：商品画像 --}}
      <div class="product-detail__image-wrap">
        @if($item->image)
            <img
              src="{{ asset($item->image) }}"
              alt="{{ $item->name }}"
              class="product-detail__image"
            >
        @else
            商品画像
        @endif
      </div>

      {{-- 右：商品情報 --}}
      <div class="product-detail__right">
        <h1 class="product-detail__name">{{ $item->name ?? '商品名がここに入る' }}</h1>

        <p class="product-detail__brand">{{ $item->brand ?? 'ブランド名' }}</p>

        <p class="product-detail__price">
          <span class="product-detail__price-yen">¥{{ number_format($item->price ?? 47000) }}</span>
          <span class="product-detail__price-tax">(税込)</span>
        </p>

        <div class="product-detail__meta">
          {{-- いいね --}}
          <form class="product-detail__meta-like" action="{{ route('items.like.toggle', $item->id) }}" method="post">
            @csrf
            <button type="submit" class="like-button">
              <img
                class="product-detail__meta-icon"
                src="{{ asset($isLiked ? 'images/ハートロゴ_ピンク.png' : 'images/ハートロゴ_デフォルト.png') }}"
                alt="いいね"
              >
              <span class="product-detail__meta-count">{{ $item->likes->count() ?? 0 }}</span>
            </button>
          </form>

          {{-- コメント --}}
          <div class="product-detail__meta-item">
            <img class="product-detail__meta-icon product-detail__meta-icon--comment"
                 src="{{ asset('images/ふきだしロゴ.png') }}"
                 alt="コメント">
            <span class="product-detail__meta-count">{{ $item->comments->count() ?? 0 }}</span>
          </div>
        </div>

        <a class="product-detail__buy-button" href="/sell">
          購入手続きへ
        </a>

        {{-- 商品説明 --}}
        <section class="product-detail__section">
          <h2 class="product-detail__section-title">商品説明</h2>
          <p class="product-detail__description">
            {{ $item->description ?? '未設定' }}
          </p>
        </section>

        {{-- 商品の情報 --}}
        <section class="product-detail__section">
          <h2 class="product-detail__section-title">商品の情報</h2>

          <div class="product-detail__info">
            <div class="product-detail__info-row">
              <div class="product-detail__info-label">カテゴリー</div>
              <div class="product-detail__info-value">
                @forelse($item->categories as $category)
                  <span class="product-detail__category-pill">{{ $category->name }}</span>
                @empty
                  <span class="product-detail__category-pill">未設定</span>
                @endforelse
              </div>
            </div>

            <div class="product-detail__info-row">
              <div class="product-detail__info-label">商品の状態</div>
              <div class="product-detail__info-state">{{ $item->condition->name ?? '未設定' }}</div>
            </div>
          </div>
        </section>

        {{-- コメント一覧 --}}
        <section class="product-detail__section">
          <h2 class="product-detail__comments-title">
            コメント({{ $item->comments->count() }})
          </h2>
          <div class="product-detail__comments">
            @forelse($item->comments as $comment)
              <div class="product-detail__comment">
                <div class="product-detail__comment-head">
                <div class="product-detail__avatar"></div>
                <div class="product-detail__commenter">
                  {{ $comment->user?->name ?? '匿名' }}
                </div>
              </div>
            <div class="product-detail__comment-body">
              {{ $comment->content }}
            </div>
          </div>
          @empty
          <div class="product-detail__comment">
            <div class="product-detail__comment-head">
              <div class="product-detail__avatar"></div>
              <div class="product-detail__commenter">---</div>
            </div>
              <div class="product-detail__comment-body">
                コメントはまだありません。
              </div>
          </div>
          @endforelse
          </div>
        </section>

        {{-- コメント投稿 --}}
        <form action="{{ route('items.comments.store', $item->id) }}" method="post">
          @csrf

          <label class="product-detail__comment-label">
            商品へのコメント
          </label>

          <textarea class="product-detail__comment-textarea" name="content">{{ old('content') }}</textarea>

          @error('content')
            <p class="form-error">{{ $message }}</p>
          @enderror
          <button type="submit" class="product-detail__comment-submit">
            コメントを送信する
          </button>
        </form>
      </div>
    </div>
  </div>
</main>
@endsection