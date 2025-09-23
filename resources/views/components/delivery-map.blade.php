@props(['delivery', 'height' => '300px'])

<div class="delivery-map-container" style="height: {{ $height }}">
    <div id="delivery-map-{{ $delivery->id }}" class="w-full h-full rounded-lg border border-gray-200"></div>
</div>

@push('scripts')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map for delivery {{ $delivery->id }}
    const mapContainer = document.getElementById('delivery-map-{{ $delivery->id }}');

    if (mapContainer && !mapContainer._leaflet_id) {
        @if($delivery->latitude && $delivery->longitude)
            // Use delivery coordinates if available
            const lat = {{ $delivery->latitude }};
            const lng = {{ $delivery->longitude }};
        @else
            // Default to Warsaw coordinates and try to geocode address
            let lat = 52.2297;
            let lng = 21.0122;
        @endif

        // Initialize map
        const map = L.map('delivery-map-{{ $delivery->id }}').setView([lat, lng], 15);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add marker for delivery location
        const marker = L.marker([lat, lng]).addTo(map);

        // Create popup content
        const popupContent = `
            <div class="p-2">
                <h3 class="font-bold text-sm">{{ $delivery->klient_nazwa }}</h3>
                <p class="text-xs text-gray-600 mt-1">{{ $delivery->klient_adres }}</p>
                @if($delivery->kod_pocztowy || $delivery->miasto)
                    <p class="text-xs text-gray-600">{{ $delivery->kod_pocztowy }} {{ $delivery->miasto }}</p>
                @endif
                @if($delivery->telefon_kontaktowy)
                    <p class="text-xs text-blue-600 mt-1">
                        <a href="tel:{{ $delivery->telefon_kontaktowy }}">{{ $delivery->telefon_kontaktowy }}</a>
                    </p>
                @endif
                <div class="mt-2">
                    <a href="{{ $delivery->google_maps_url }}"
                       target="_blank"
                       class="inline-block bg-blue-500 text-white text-xs px-2 py-1 rounded hover:bg-blue-600">
                        Nawigacja Google Maps
                    </a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);

        @if(!$delivery->latitude || !$delivery->longitude)
            // Try to geocode the address if no coordinates are available
            const address = '{{ addslashes($delivery->full_address) }}';
            if (address) {
                geocodeAddress(address, map, marker);
            }
        @endif
    }
});

// Function to geocode address using Nominatim
function geocodeAddress(address, map, marker) {
    const encodedAddress = encodeURIComponent(address);
    const nominatimUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodedAddress}&limit=1`;

    fetch(nominatimUrl)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);

                // Update map view and marker position
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);

                // Update delivery coordinates via AJAX if possible
                updateDeliveryCoordinates({{ $delivery->id }}, lat, lng);
            }
        })
        .catch(error => {
            console.warn('Geocoding failed:', error);
        });
}

// Function to update delivery coordinates
function updateDeliveryCoordinates(deliveryId, lat, lng) {
    // This would need to be implemented as an endpoint to update delivery coordinates
    // For now, just log the coordinates
    console.log(`Delivery ${deliveryId} coordinates: ${lat}, ${lng}`);
}
</script>
@endpush

