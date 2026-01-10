@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

{{-- HERO SECTION --}}
<section class="hello-hero text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge hello-badge mb-3">
                    🎀 Hello Kitty Store
                </span>

                <h1 class="display-5 fw-bold mb-3">
                    Belanja Lucu & <br>
                    <span class="text-warning">Selamat datang di birgistor</span>
                </h1>

                <p class="lead mb-4">
                    Temukan produk imut dan berkualitas ✨
                    Gratis ongkir untuk pembelian pertama!
                </p>

                <a href="{{ route('catalog.index') }}"
                   class="btn hello-btn btn-lg rounded-pill shadow">
                    🛍️ Mulai Belanja
                </a>
            </div>

            <div class="col-lg-6 d-none d-lg-block text-center">
                <img src="{{ asset('images/hello-kitty.png') }}"
                     alt="Hello Kitty"
                     class="img-fluid floating"
                     style="max-height:380px;">
            </div>
        </div>
    </div>
</section>

{{-- KATEGORI --}}
<section class="py-5 hello-section">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">
            🎀 Kategori Favorit
        </h2>

        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                   class="text-decoration-none">
                    <div class="card hello-card text-center h-100">
                        <div class="card-body">
                            <img src="{{ $category->image_url }}"
                                 class="rounded-circle mb-3"
                                 width="80" height="80"
                                 style="object-fit:cover">
                            <h6 class="fw-semibold text-pink mb-0">
                                {{ $category->name }}
                            </h6>
                            <small class="text-muted">
                                {{ $category->products_count }} produk
                            </small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PRODUK UNGGULAN --}}
<section class="py-5 bg-pink-soft">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">💖 Produk Unggulan</h2>
            <a href="{{ route('catalog.index') }}"
               class="btn btn-outline-pink rounded-pill">
                Lihat Semua ➜
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $product)
            <div class="col-6 col-md-4 col-lg-3">
                @include('partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PROMO --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card hello-promo bg-warning text-dark h-100">
                    <div class="card-body">
                        <h3>🎉 Flash Sale</h3>
                        <p>Diskon hingga 50% ✨</p>
                        <a href="#" class="btn btn-dark rounded-pill">
                            Lihat Promo
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card hello-promo bg-pink text-white h-100">
                    <div class="card-body">
                        <h3>🎁 Member Baru</h3>
                        <p>Voucher Rp 50.000 💕</p>
                        <a href="{{ route('register') }}"
                           class="btn btn-light rounded-pill">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PRODUK TERBARU --}}
<section class="py-5 hello-section">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">
            🐱 Produk Terbaru
        </h2>

        <div class="row g-4">
            @foreach($latestProducts as $product)
            <div class="col-6 col-md-4 col-lg-3">
                @include('partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
