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
        <img
          class="product-detail__image"
          src="{{ $item->image_url ?? asset('images/no-image.png') }}"
          alt="商品画像"
        >
      </div>

      {{-- 右：商品情報 --}}
      <div class="product-detail__right">
        <h1 class="product-detail__name">{{ $item->name ?? '商品名がここに入る' }}</h1>

        <p class="product-detail__brand">{{ $item->brand_name ?? 'ブランド名' }}</p>

        <p class="product-detail__price">
          <span class="product-detail__price-yen">¥{{ number_format($item->price ?? 47000) }}</span>
          <span class="product-detail__price-tax">(税込)</span>
        </p>

        <div class="product-detail__meta">
          {{-- いいね --}}
          <div class="product-detail__meta-item">
            <img class="product-detail__meta-icon"
                 src="{{ asset('images/icon_heart.png') }}"
                 alt="いいね">
            <span class="product-detail__meta-count">{{ $likeCount ?? 3 }}</span>
          </div>

          {{-- コメント --}}
          <div class="product-detail__meta-item">
            <img class="product-detail__meta-icon product-detail__meta-icon--comment"
                 src="{{ asset('images/icon_comment.png') }}"
                 alt="コメント">
            <span class="product-detail__meta-count">{{ $commentCount ?? 1 }}</span>
          </div>
        </div>

        <a class="product-detail__buy-button" href="/">
          購入手続きへ
        </a>

        {{-- 商品説明 --}}
        <section class="product-detail__section">
          <h2 class="product-detail__section-title">商品説明</h2>
          <p class="product-detail__description">
            {{ $item->description ?? 'カラー：グレー

新品
商品の状態は良好です。傷もありません。
購入後、即発送いたします。' }}
          </p>
        </section>

        {{-- 商品の情報 --}}
        <section class="product-detail__section">
          <h2 class="product-detail__section-title">商品の情報</h2>

          <div class="product-detail__info">
            <div class="product-detail__info-row">
              <div class="product-detail__info-label">カテゴリー</div>
              <div class="product-detail__info-value">
                @forelse(($categories ?? ['洋服','メンズ']) as $cat)
                  <span class="product-detail__category-pill">{{ $cat }}</span>
                @empty
                @endforelse
              </div>
            </div>

            <div class="product-detail__info-row">
              <div class="product-detail__info-label">商品の状態</div>
              <div class="product-detail__info-state">{{ $item->condition ?? '良好' }}</div>
            </div>
          </div>
        </section>

        {{-- コメント一覧 --}}
        <section class="product-detail__section">
          <h2 class="product-detail__comments-title">コメント({{ $commentCount ?? 1 }})</h2>

          <div class="product-detail__comments">
            @forelse(($comments ?? []) as $comment)
              <div class="product-detail__comment">
                <div class="product-detail__comment-head">
                  <div class="product-detail__avatar"></div>
                  <div class="product-detail__commenter">{{ $comment->user_name }}</div>
                </div>
                <div class="product-detail__comment-body">
                  {{ $comment->body }}
                </div>
              </div>
            @empty
              {{-- デザイン見本用ダミー --}}
              <div class="product-detail__comment">
                <div class="product-detail__comment-head">
                  <div class="product-detail__avatar"></div>
                  <div class="product-detail__commenter">admin</div>
                </div>
                <div class="product-detail__comment-body">
                  こちらにコメントが入ります。
                </div>
              </div>
            @endforelse
          </div>
        </section>

        {{-- コメント投稿 --}}
        <section class="product-detail__section product-detail__section--comment-form">
          <form action="/" method="get">  
            @csrf
            <label class="product-detail__comment-label" for="comment">商品へのコメント</label>
            <textarea
              class="product-detail__comment-textarea"
              id="comment"
              name="comment"
              placeholder=""
            >{{ old('comment') }}</textarea>

            <button class="product-detail__comment-submit" type="submit">
              コメントを送信する
            </button>
          </form>
        </section>

      </div>
    </div>

  </div>
</main>
@endsection