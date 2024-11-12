<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shop Location Check</title>
</head>
<body>

<button id="orderButton" disabled>Shop Now</button>
<p>User Latitude: <span id="userLatitude"></span></p>
<p>User Longitude: <span id="userLongitude"></span></p>
<p>Distance to Shop: <span id="distance"></span> meters</p>

<script>
// const shopLatitude = 20.1241008;
// const shopLongitude = 94.9970469;
const shopLatitude = 20.1241008;
const shopLongitude = 94.9970469;
const distanceThreshold = 2000; // 2000 meters = 2 kilometers

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = lat1 * Math.PI / 180; // φ, λ in radians
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2-lat1) * Math.PI / 180;
    const Δλ = (lon2-lon1) * Math.PI / 180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    const distance = R * c; // Distance in meters
    return distance;
}

function checkLocation() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLatitude = position.coords.latitude;
                const userLongitude = position.coords.longitude;

                const distance = calculateDistance(userLatitude, userLongitude, shopLatitude, shopLongitude);

                document.getElementById("userLatitude").textContent = userLatitude;
                document.getElementById("userLongitude").textContent = userLongitude;
                document.getElementById("distance").textContent = distance.toFixed(2);

                // Enable/disable shop button based on distance
                if (distance <= distanceThreshold) {
                    document.getElementById("orderButton").disabled = false;
                } else {
                    document.getElementById("orderButton").disabled = true;
                }
            },
            function(error) {
                console.error("Error getting user location:", error.message);
            }
        );
    } else {
        console.error("Geolocation is not supported by this browser.");
    }
}

// Call checkLocation function when the page loads
document.addEventListener("DOMContentLoaded", function() {
    checkLocation();
});
</script>

</body>
</html>
