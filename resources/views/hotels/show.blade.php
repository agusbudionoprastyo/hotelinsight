@extends('layouts.app')

@section('title', $hotel->name . ' - Hotel Insight')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $hotel->name }}</h1>
                <p class="text-gray-600 mb-2">{{ $hotel->location }}</p>
                @if($hotel->rating)
                    <div class="flex items-center">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $hotel->rating)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="ml-2 text-gray-600">{{ number_format($hotel->rating, 1) }}/5</span>
                    </div>
                @endif
            </div>
            <button id="fetchOtaData" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Fetch Latest OTA Data
            </button>
        </div>

        @if($hotel->description)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Description</h2>
                <p class="text-gray-700">{{ $hotel->description }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">OTA Prices & Reviews</h2>
                
                <div id="otaDataContainer">
                    @foreach($aggregatedData as $otaSlug => $otaData)
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $otaData['name'] }}</h3>
                                <a href="{{ $otaData['website_url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Visit {{ $otaData['name'] }}
                                </a>
                            </div>
                            
                            @if($otaData['prices']->count() > 0)
                                <div class="mb-3">
                                    <h4 class="font-medium text-gray-700 mb-2">Prices</h4>
                                    <div class="space-y-2">
                                        @foreach($otaData['prices']->take(3) as $price)
                                            <div class="flex justify-between items-center bg-white p-2 rounded">
                                                <span class="text-gray-600">{{ $price['room_type'] }}</span>
                                                <span class="font-semibold text-green-600">
                                                    Rp {{ number_format($price['price'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            @if($otaData['reviews']->count() > 0)
                                <div>
                                    <h4 class="font-medium text-gray-700 mb-2">Reviews</h4>
                                    <div class="flex items-center mb-2">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $otaData['average_rating'])
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">
                                            {{ number_format($otaData['average_rating'], 1) }}/5 ({{ $otaData['total_reviews'] }} reviews)
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Fetch OTA Data</h2>
                <div class="bg-blue-50 rounded-lg p-4">
                    <form id="otaDataForm" class="space-y-4">
                        <div>
                            <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Check-in Date</label>
                            <input type="date" id="check_in" name="check_in" required 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">Check-out Date</label>
                            <input type="date" id="check_out" name="check_out" required 
                                   min="{{ date('Y-m-d', strtotime('+2 days')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Fetch OTA Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otaDataForm = document.getElementById('otaDataForm');
    const fetchOtaDataBtn = document.getElementById('fetchOtaData');
    
    otaDataForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(otaDataForm);
        const checkIn = formData.get('check_in');
        const checkOut = formData.get('check_out');
        
        if (!checkIn || !checkOut) {
            alert('Please select both check-in and check-out dates');
            return;
        }
        
        fetchOtaDataBtn.disabled = true;
        fetchOtaDataBtn.textContent = 'Fetching...';
        
        fetch(`/hotels/{{ $hotel->id }}/fetch-ota-data`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                check_in: checkIn,
                check_out: checkOut
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('OTA data fetched successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching OTA data');
        })
        .finally(() => {
            fetchOtaDataBtn.disabled = false;
            fetchOtaDataBtn.textContent = 'Fetch Latest OTA Data';
        });
    });
    
    fetchOtaDataBtn.addEventListener('click', function() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const dayAfterTomorrow = new Date(today);
        dayAfterTomorrow.setDate(dayAfterTomorrow.getDate() + 2);
        
        document.getElementById('check_in').value = tomorrow.toISOString().split('T')[0];
        document.getElementById('check_out').value = dayAfterTomorrow.toISOString().split('T')[0];
        
        otaDataForm.dispatchEvent(new Event('submit'));
    });
});
</script>
@endsection
