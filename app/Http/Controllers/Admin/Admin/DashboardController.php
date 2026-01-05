<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama (Cards)
        // Kita menghitung data real-time dari database.
        // Konsep: Gunakan method agregat database (SUM, COUNT) daripada menarik data ke PHP (get() -> count()).
        // Alasan: Jauh lebih hemat memori server.

        $stats = [
            'total_revenue' => Order::whereIn('status', ['processing', 'completed'])
                                    ->sum('total_amount'), // SQL: SELECT SUM(total_amount) FROM orders WHERE ...

            'total_orders' => Order::count(), // SQL: SELECT COUNT(*) FROM orders

            // Pending Orders: Yang perlu tindakan segera admin
            'pending_orders' => Order::where('status', 'pending')
                                     ->where('payment_status', 'paid') // Sudah bayar tapi belum diproses
                                     ->count(),

            'total_products' => Product::count(),

            'total_customers' => User::where('role', 'customer')->count(),

            // Stok Rendah: Produk dengan stok <= 5
            // Berguna untuk notifikasi re-stock
            'low_stock' => Product::where('stock', '<=', 5)->count(),
        ];

        // 2. Data Tabel Pesanan Terbaru (5 transaksi terakhir)
        // Eager load 'user' untuk menghindari N+1 Query Problem saat menampilkan nama customer di blade.
        $recentOrders = Order::with('user')
            ->latest() // alias orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 3. Produk Terlaris
        // Tantangan: Menghitung total qty terjual dari tabel relasi (order_items)
        // Solusi: withCount dengan query modifikasi (SUM quantity)
        $topProducts = Product::withCount(['orderItems as sold' => function ($q) {
                // Kita hanya hitung item yang berasal dari order yang SUDAH DIBAYAR (paid)
                $q->select(DB::raw('SUM(quantity)'))
                  ->whereHas('order', function($query) {
                      $query->where('payment_status', 'paid');
                  });
            }])
            ->having('sold', '>', 0) // Filter: Hanya tampilkan yang pernah terjual
            ->orderByDesc('sold')    // Urutkan dari yang paling laku
            ->take(5)
            ->get();

        // 4. Data Grafik Pendapatan (7 Hari Terakhir)
        // Kasus: Grouping data per tanggal
        // Kita gunakan DB::raw untuk format tanggal dari timestamp 'created_at'
        $revenueChart = Order::select([
                DB::raw('DATE(created_at) as date'), // Ambil tanggalnya saja (2024-12-10)
                DB::raw('SUM(total_amount) as total') // Total omset hari itu
            ])
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(7)) // Filter 7 hari ke belakang
            ->groupBy('date') // Kelompokkan baris berdasarkan tanggal
            ->orderBy('date', 'asc') // Urutkan kronologis
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'revenueChart'));
    }

    @extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        {{-- 1. Stats Cards Grid --}}

        {{-- Revenue Card --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.8rem">Total Pendapatan</p>
                            <h4 class="fw-bold mb-0 text-success">
                                Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-wallet2 text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Action Card --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.8rem">Perlu Diproses</p>
                            <h4 class="fw-bold mb-0 text-warning">
                                {{ $stats['pending_orders'] }}
                            </h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-box-seam text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Low Stock Card --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-4 border-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.8rem">Stok Menipis</p>
                            <h4 class="fw-bold mb-0 text-danger">
                                {{ $stats['low_stock'] }}
                            </h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-exclamation-triangle text-danger fs-3"></i>
                        </div>
                    </div>@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        {{-- 1. Stats Cards Grid --}}

        {{-- Revenue Card --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.8rem">Total Pendapatan</p>
                            <h4 class="fw-bold mb-0 text-success">
                                Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-wallet2 text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Action Card --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.8rem">Perlu Diproses</p>
                            <h4 class="fw-bold mb-0 text-warning">
                                {{ $stats['pending_orders'] }}
                            </h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-box-seam text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Low Stock Card --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-4 border-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.8rem">Stok Menipis</p>
                            <h4 class="fw-bold mb-0 text-danger">
                                {{ $stats['low_stock'] }}
                            </h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-exclamation-triangle text-danger fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Products --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.8rem">Total Produk</p>
                            <h4 class="fw-bold mb-0 text-primary">
                                {{ $stats['total_products'] }}
                            </h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-tags text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- 2. Revenue Chart --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Grafik Penjualan (7 Hari)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. Recent Orders --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Pesanan Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($recentOrders as $order)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                                <div>
                                    <div class="fw-bold text-primary">#{{ $order->order_number }}</div>
                                    <small class="text-muted">{{ $order->user->name }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                    <span class="badge rounded-pill
                                        {{ $order->payment_status == 'paid' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none fw-bold">
                        Lihat Semua Pesanan &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Top Selling Products --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Produk Terlaris</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @foreach($topProducts as $product)
                    <div class="col-6 col-md-2 text-center">
                        <div class="card h-100 border-0 hover-shadow transition">
                            <img src="{{ $product->image_url }}" class="card-img-top rounded mb-2" style="max-height: 100px; object-fit: cover;">
                            <h6 class="card-title text-truncate" style="font-size: 0.9rem">{{ $product->name }}</h6>
                            <small class="text-muted">{{ $product->sold }} terjual</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // Data dari Controller (Blade to JS)
        const labels = {!! json_encode($revenueChart->pluck('date')) !!};
        const data = {!! json_encode($revenueChart->pluck('total')) !!};

        new Chart(ctx, {
            type: 'line', // Jenis grafik: Line chart
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    borderColor: '#0d6efd', // Bootstrap Primary Color
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    tension: 0.3, // Membuat garis sedikit melengkung (smooth)
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting agar Chart menyesuaikan container
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                // Format Tooltip jadi Rupiah
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4] },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
@endsection
                </div>
            </div>
        </div>

        {{-- Total Products --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.8rem">Total Produk</p>
                            <h4 class="fw-bold mb-0 text-primary">
                                {{ $stats['total_products'] }}
                            </h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-tags text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- 2. Revenue Chart --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Grafik Penjualan (7 Hari)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. Recent Orders --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Pesanan Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($recentOrders as $order)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                                <div>
                                    <div class="fw-bold text-primary">#{{ $order->order_number }}</div>
                                    <small class="text-muted">{{ $order->user->name }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                    <span class="badge rounded-pill
                                        {{ $order->payment_status == 'paid' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none fw-bold">
                        Lihat Semua Pesanan &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Top Selling Products --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Produk Terlaris</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @foreach($topProducts as $product)
                    <div class="col-6 col-md-2 text-center">
                        <div class="card h-100 border-0 hover-shadow transition">
                            <img src="{{ $product->image_url }}" class="card-img-top rounded mb-2" style="max-height: 100px; object-fit: cover;">
                            <h6 class="card-title text-truncate" style="font-size: 0.9rem">{{ $product->name }}</h6>
                            <small class="text-muted">{{ $product->sold }} terjual</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // Data dari Controller (Blade to JS)
        const labels = {!! json_encode($revenueChart->pluck('date')) !!};
        const data = {!! json_encode($revenueChart->pluck('total')) !!};

        new Chart(ctx, {
            type: 'line', // Jenis grafik: Line chart
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    borderColor: '#0d6efd', // Bootstrap Primary Color
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    tension: 0.3, // Membuat garis sedikit melengkung (smooth)
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting agar Chart menyesuaikan container
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                // Format Tooltip jadi Rupiah
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4] },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
@endsection
}
