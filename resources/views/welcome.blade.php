@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Hotel Insight
            </h1>
            <p class="text-xl text-gray-600">
                Temukan hotel terbaik dengan data real-time dari Google Places
            </p>
        </div>

        <!-- Google Places Search Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Cari Hotel</h2>
            <form id="hotelSearchForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Kota</label>
                        <input type="text" id="city" name="city" value="Jakarta" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="radius" class="block text-sm font-medium text-gray-700 mb-2">Radius (meter)</label>
                        <select id="radius" name="radius" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1000">1 km</option>
                            <option value="5000" selected>5 km</option>
                            <option value="10000">10 km</option>
                            <option value="25000">25 km</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Cari Hotel
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Import Hotels Button -->
        <div class="text-center mb-8">
            <button id="importHotelsBtn" 
                    class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                Import Hotels dari Google Places
            </button>
        </div>

        <!-- Search Results -->
        <div id="searchResults" class="hidden">
            <h3 class="text-2xl font-semibold text-gray-800 mb-6">Hasil Pencarian</h3>
            <div id="hotelsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Hotels will be populated here -->
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="hidden text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Mencari hotel...</p>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <span id="errorText"></span>
        </div>

        <!-- Success Message -->
        <div id="successMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <span id="successText"></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('hotelSearchForm');
    const importBtn = document.getElementById('importHotelsBtn');
    const searchResults = document.getElementById('searchResults');
    const hotelsList = document.getElementById('hotelsList');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');

    // Search Hotels
    searchForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const city = document.getElementById('city').value;
        const radius = document.getElementById('radius').value;
        
        showLoading();
        hideMessages();
        
        try {
            const response = await fetch(`/api/hotels/search?city=${encodeURIComponent(city)}&radius=${radius}`);
            const data = await response.json();
            
            if (data.success) {
                displayHotels(data.data);
                showSuccess(`Ditemukan ${data.count} hotel di ${data.city}`);
            } else {
                showError(data.message || 'Error searching hotels');
            }
        } catch (error) {
            showError('Network error: ' + error.message);
        } finally {
            hideLoading();
        }
    });

    // Import Hotels
    importBtn.addEventListener('click', async function() {
        const city = document.getElementById('city').value;
        const radius = document.getElementById('radius').value;
        
        showLoading();
        hideMessages();
        
        try {
            const response = await fetch('/api/hotels/import', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ city, radius })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccess(data.message);
            } else {
                showError(data.message || 'Error importing hotels');
            }
        } catch (error) {
            showError('Network error: ' + error.message);
        } finally {
            hideLoading();
        }
    });

    function displayHotels(hotels) {
        if (hotels.length === 0) {
            hotelsList.innerHTML = '<p class="text-gray-500 text-center col-span-full">Tidak ada hotel ditemukan</p>';
        } else {
            hotelsList.innerHTML = hotels.map(hotel => `
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">${hotel.name}</h4>
                        <p class="text-gray-600 text-sm mb-3">${hotel.address}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-yellow-500">★</span>
                                <span class="ml-1 text-sm text-gray-700">${hotel.rating.toFixed(1)} (${hotel.user_ratings_total})</span>
                            </div>
                            ${hotel.price_level ? `<span class="text-green-600 font-medium">${'$'.repeat(hotel.price_level)}</span>` : ''}
                        </div>
                        <a href="/hotels" 
                           class="mt-3 w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm text-center block">
                            Lihat Semua Hotel
                        </a>
                    </div>
                </div>
            `).join('');
        }
        
        searchResults.classList.remove('hidden');
    }

    function showLoading() {
        loadingSpinner.classList.remove('hidden');
        searchResults.classList.add('hidden');
    }

    function hideLoading() {
        loadingSpinner.classList.add('hidden');
    }

    function showError(message) {
        document.getElementById('errorText').textContent = message;
        errorMessage.classList.remove('hidden');
    }

    function showSuccess(message) {
        document.getElementById('successText').textContent = message;
        successMessage.classList.remove('hidden');
    }

    function hideMessages() {
        errorMessage.classList.add('hidden');
        successMessage.classList.add('hidden');
    }
});

// Global function for hotel details
async function getHotelDetails(placeId) {
    try {
        const response = await fetch(`/api/hotels/${placeId}/details`);
        const data = await response.json();
        
        if (data.success) {
            // Redirect to hotels page where user can see all hotels
            window.location.href = '/hotels';
        } else {
            alert('Error fetching hotel details');
        }
    } catch (error) {
        alert('Network error: ' + error.message);
    }
}
</script>
@endsection
