document.addEventListener("DOMContentLoaded", () => {
    fetchProviders();
});

async function fetchProviders() {
    try {
        // Points to your PHP backend
        const response = await fetch('api/get_providers.php'); 
        const providers = await response.json();
        
        const grid = document.getElementById('providerGrid');
        
        providers.forEach(provider => {
            const card = document.createElement('div');
            card.className = 'card';
            card.innerHTML = `
                <h3>${provider.full_name} <span>★ ${provider.rating}</span></h3>
                <div class="specialty">${provider.specialty}</div>
                <div class="rate">Starts at $${provider.hourly_rate} /hr</div>
                <button onclick="viewDetails(${provider.id})">View Details →</button>
            `;
            grid.appendChild(card);
        });
    } catch (error) {
        console.error("Error loading providers:", error);
    }
}

function viewDetails(id) {
    // Navigate to the profile page (image 3)
    window.location.href = `profile.php?id=${id}`;
}