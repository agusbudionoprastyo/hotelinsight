@extends('layouts.app')

@section('title', 'Beranda - Hotel Insight')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-search mr-2 text-blue-600"></i>
            Temukan Hotel Terbaik
        </h1>
        <p class="text-gray-600">Bandingkan harga dan review dari berbagai platform booking</p>
    </div>

    @if($hotels->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($hotels as $hotel)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    @if($hotel->image_url)
                        <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-hotel text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $hotel->name }}</h3>
                            @if($hotel->rating)
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $hotel->rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                    @endfor
                                </div>
                            @endif
                        </div>
                        
                        @if($hotel->location)
                            <p class="text-gray-600 text-sm mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $hotel->location }}
                            </p>
                        @endif
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $hotel->average_rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                    @endfor
                                    <span class="ml-1 text-sm text-gray-600">
                                        {{ number_format($hotel->average_rating, 1) }} ({{ $hotel->reviews->count() }} review)
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        @if($hotel->latest_prices->count() > 0)
                            <div class="border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Harga Terbaru:</h4>
                                <div class="space-y-2">
                                    @foreach($hotel->latest_prices->take(3) as $latestPrice)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">{{ $latestPrice->otaSource->name }}</span>
                                            <span class="font-semibold text-green-600">
                                                {{ $latestPrice->currency }} {{ number_format($latestPrice->price) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('hotels.show', $hotel) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-eye mr-1"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-hotel text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada hotel</h3>
            <p class="text-gray-600 mb-6">Mulai dengan menambahkan hotel pertama Anda</p>
            <a href="{{ route('hotels.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium">
                <i class="fas fa-plus mr-2"></i>Tambah Hotel
            </a>
        </div>
    @endif
</div>
@endsection
