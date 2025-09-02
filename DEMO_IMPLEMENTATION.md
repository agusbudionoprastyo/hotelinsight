# Hotel Insight - OTA Data Integration Implementation

## Overview
This implementation provides a comprehensive solution to fetch hotel prices, reviews, and ratings from multiple OTAs (Online Travel Agencies) using SerpAPI. The system aggregates data from:

- **Booking.com**
- **Agoda**
- **Expedia**
- **Traveloka**
- **Tiket.com**

## Key Features Implemented

### 1. SerpAPI Integration
- **API Key**: `4e8aa76b5aed65e0d0e558589264c1becb21e374464353cf70663289c19935b5`
- **Service**: `app/Services/SerpApiService.php`
- **Functionality**: Searches Google for hotel data across multiple OTA platforms

### 2. Multi-OTA Data Aggregation
- **Service**: `app/Services/HotelDataAggregatorService.php`
- **Functionality**: 
  - Fetches data from all configured OTAs
  - Aggregates prices, reviews, and ratings
  - Saves data to database with proper relationships
  - Updates hotel overall rating

### 3. Enhanced Models
- **Hotel**: Enhanced with rating and location fields
- **HotelPrice**: Stores OTA-specific pricing information
- **HotelReview**: Stores OTA-specific reviews and ratings
- **OtaSource**: Manages OTA platform information

### 4. API Endpoints
- `POST /hotels/{hotel}/fetch-ota-data` - Fetch latest OTA data
- `GET /hotels/{hotel}/prices` - Get aggregated price data
- `GET /hotels/{hotel}/reviews` - Get aggregated review data
- `POST /hotels/search` - Search hotels with OTA data

## How It Works

### 1. Data Fetching Process
```php
// Example usage in HotelController
$aggregatedData = $this->hotelDataAggregator->aggregateHotelData(
    $hotel,
    '2024-01-15', // check-in date
    '2024-01-16'  // check-out date
);
```

### 2. SerpAPI Search Strategy
- Searches for each OTA individually using Google search
- Extracts prices using regex patterns for Indonesian Rupiah (Rp)
- Extracts ratings from review snippets
- Caches results for 1 hour (prices) and 2 hours (reviews)

### 3. Data Parsing
- **Price Extraction**: `Rp 1,500,000` → `1500000`
- **Rating Extraction**: `4.5/5 stars` → `4.5`
- **URL Extraction**: Direct links to OTA booking pages

### 4. Database Storage
- **HotelPrices**: Stored per OTA with check-in/out dates
- **HotelReviews**: Stored per OTA with verification status
- **OtaSources**: Auto-created for new OTA platforms

## Usage Examples

### Fetch OTA Data for a Hotel
```javascript
// Frontend JavaScript
fetch('/hotels/1/fetch-ota-data', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        check_in: '2024-01-15',
        check_out: '2024-01-16'
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('OTA data:', data.data);
    }
});
```

### Get Aggregated Hotel Data
```php
// Backend PHP
$aggregatedData = $hotelDataAggregator->getAggregatedHotelData($hotel);

// Returns:
[
    'booking-com' => [
        'name' => 'Booking.com',
        'prices' => [...],
        'reviews' => [...],
        'average_rating' => 4.2,
        'total_reviews' => 15
    ],
    'agoda' => [
        'name' => 'Agoda',
        'prices' => [...],
        'reviews' => [...],
        'average_rating' => 4.0,
        'total_reviews' => 12
    ],
    // ... other OTAs
]
```

## Configuration

### 1. Environment Variables
```env
SERPAPI_KEY=4e8aa76b5aed65e0d0e558589264c1becb21e374464353cf70663289c19935b5
```

### 2. Services Configuration
```php
// config/services.php
'serpapi' => [
    'api_key' => env('SERPAPI_KEY'),
    'base_url' => 'https://serpapi.com/search',
],
```

### 3. OTA Sources
The system automatically manages OTA sources. New OTAs can be added by:
- Updating the `OtaSourceSeeder`
- Adding to the `SerpApiService` OTA list
- The system will auto-create entries

## Data Flow

1. **User Request**: Selects hotel and dates
2. **API Call**: Frontend calls `/fetch-ota-data` endpoint
3. **SerpAPI Search**: Service searches each OTA individually
4. **Data Parsing**: Extracts prices, ratings, and URLs
5. **Database Storage**: Saves aggregated data with relationships
6. **Response**: Returns aggregated data to frontend
7. **Display**: Frontend shows OTA comparison

## Benefits

### 1. Comprehensive Coverage
- Covers major Indonesian and international OTAs
- Real-time data fetching from search engines
- No dependency on individual OTA APIs

### 2. Data Aggregation
- Single source of truth for hotel data
- Historical price tracking
- Review aggregation across platforms

### 3. Scalability
- Caching reduces API calls
- Modular service architecture
- Easy to add new OTA platforms

### 4. User Experience
- One-click data refresh
- Visual OTA comparison
- Direct booking links

## Testing the Implementation

### 1. Create a Hotel
```php
$hotel = Hotel::create([
    'name' => 'Hotel Indonesia Kempinski Jakarta',
    'location' => 'Jakarta, Indonesia',
    'description' => 'Luxury hotel in central Jakarta'
]);
```

### 2. Fetch OTA Data
```php
$aggregator = app(HotelDataAggregatorService::class);
$data = $aggregator->aggregateHotelData(
    $hotel,
    '2024-01-15',
    '2024-01-16'
);
```

### 3. View Results
```php
$aggregatedData = $aggregator->getAggregatedHotelData($hotel);
// Returns structured data for all OTAs
```

## Error Handling

- **API Failures**: Logged and handled gracefully
- **Data Parsing**: Fallback values for missing data
- **Database Errors**: Transaction rollback on failures
- **Rate Limiting**: Built-in caching to reduce API calls

## Performance Considerations

- **Caching**: 1-hour cache for prices, 2-hour for reviews
- **Batch Processing**: Multiple OTA searches in single request
- **Database Indexing**: Proper relationships and indexes
- **Async Processing**: Ready for queue implementation

## Future Enhancements

1. **Queue System**: Background processing for large datasets
2. **More OTAs**: Additional platforms like GoTo, Pegipegi
3. **Price Alerts**: Notifications for price changes
4. **Analytics**: Price trend analysis and insights
5. **Mobile App**: Native mobile application

## Conclusion

This implementation provides a robust, scalable solution for aggregating hotel data from multiple OTAs. The system is designed to be:

- **Easy to use**: Simple API endpoints and frontend integration
- **Maintainable**: Clean service architecture and proper separation of concerns
- **Scalable**: Caching, modular design, and database optimization
- **Reliable**: Comprehensive error handling and data validation

The system successfully addresses the requirement to fetch hotel prices, reviews, and ratings from multiple OTAs using SerpAPI, providing users with comprehensive hotel comparison data in a single interface.
