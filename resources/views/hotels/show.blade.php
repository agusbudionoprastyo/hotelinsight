@extends('layouts.app')

@section('title', $hotel->name . ' - Hotel Insight')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="md:flex">
            <div class="md:w-1/3">
                @if($hotel->image_url)
                    <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}" class="w-full h-64 md:h-full object-cover">
                @else
                    <div class="w-full h-64 md:h-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-hotel text-gray-400 text-6xl"></i>
                    </div>
                @endif
            </div>
            
            <div class="md:w-2/3 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $hotel->name }}</h1>
                        @if($hotel->location)
                            <p class="text-gray-600">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $hotel->location }}
                            </p>
                        @endif
                    </div>
                    
                    <div class="text-right">
                        @if($hotel->rating)
                            <div class="flex items-center justify-end mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $hotel->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">{{ $hotel->rating }} Bintang</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-end">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $hotel->average_rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                            @endfor
                            <span class="ml-2 text-sm text-gray-600">
                                {{ number_format($hotel->average_rating, 1) }} ({{ $hotel->reviews->count() }} review)
                            </span>
                        </div>
                    </div>
                </div>
                
                @if($hotel->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Deskripsi</h3>
                        <p class="text-gray-700">{{ $hotel->description }}</p>
                    </div>
                @endif
                <div class="mt-4">
                    <form method="POST" action="{{ route('hotels.fetchAmadeus', $hotel) }}">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sync mr-1"></i>Ambil Harga dari Amadeus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($hotel->latest_prices->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-dollar-sign mr-2 text-green-600"></i>
                Harga Terbaru
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($hotel->latest_prices as $latestPrice)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">{{ $latestPrice->otaSource->name }}</h4>
                            <span class="text-sm text-gray-500">{{ $latestPrice->last_updated->diffForHumans() }}</span>
                        </div>
                        <div class="text-2xl font-bold text-green-600 mb-2">
                            {{ $latestPrice->currency }} {{ number_format($latestPrice->price) }}
                        </div>
                        <div class="text-sm text-gray-600 mb-3">
                            <div>Check-in: {{ $latestPrice->check_in_date->format('d M Y') }}</div>
                            <div>Check-out: {{ $latestPrice->check_out_date->format('d M Y') }}</div>
                            @if($latestPrice->room_type)
                                <div>Tipe Kamar: {{ $latestPrice->room_type }}</div>
                            @endif
                        </div>
                        @if($latestPrice->booking_url)
                        <a href="{{ $latestPrice->booking_url }}" target="_blank"
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium">
                            <i class="fas fa-external-link-alt mr-1"></i>Booking
                        </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                <i class="fas fa-dollar-sign mr-2 text-green-600"></i>
                Harga Terbaru
            </h2>
            <p class="text-gray-600">Belum ada data harga OTA untuk hotel ini.</p>
        </div>
    @endif

    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-comments mr-2 text-blue-600"></i>
            Review ({{ $hotel->reviews->count() }})
        </h2>
        @if($reviews->count() > 0)
            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="border-b border-gray-200 pb-6 last:border-b-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $review->review_date->format('d M Y') }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-900">{{ $review->reviewer_name }}</h4>
                            @if($review->otaSource)
                                <span class="text-sm text-blue-600 bg-blue-100 px-2 py-1 rounded">
                                    {{ $review->otaSource->name }}
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-700">{{ $review->review_text }}</p>
                        @if($review->review_url)
                            <div class="mt-2">
                                <a href="{{ $review->review_url }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i>Lihat Review Asli
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @elseif(!empty($googleDetails['reviews']))
            <div class="space-y-6">
                @foreach($googleDetails['reviews'] as $gReview)
                    <div class="border-b border-gray-200 pb-6 last:border-b-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= ($gReview['rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600">{{ $gReview['rating'] ?? 0 }}/5</span>
                                </div>
                            </div>
                        </div>
                        <h4 class="font-semibold text-gray-900">{{ $gReview['author_name'] ?? 'Guest' }}</h4>
                        <p class="text-gray-700">{{ $gReview['text'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-comment-slash text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">Belum ada review untuk hotel ini</p>
            </div>
        @endif
    </div>
</div>
@endsection
