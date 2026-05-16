class PetMapApp {
    constructor() {
        this.map = null;
        this.markers = [];
        this.pendingPetId = null;
        this.searchTimeout = null;
        this.petListContainer = document.getElementById('petListContainer');
        this.searchInput = document.getElementById('liveSearchInput');
        this.sightingModal = new bootstrap.Modal(document.getElementById('sightingModal'));
        this.saveSightingBtn = document.getElementById('saveSightingBtn');
        this.isLoggedIn = document.querySelector('meta[name="user-logged-in"]').content === 'true';
        this.init();
    }

    init() {
        this.initMap();
        this.bindEvents();
        this.loadPets();
    }

    initMap() {
        this.map = L.map('mainMap').setView([53.4808, -2.2426], 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap © CARTO'
        }).addTo(this.map);

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition((pos) => {
                this.map.flyTo([pos.coords.latitude, pos.coords.longitude], 13);
            });
        }
    }

    bindEvents() {
        this.searchInput?.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.loadPets(e.target.value), 300);
        });
        this.map.on('click', (e) => this.handleMapClick(e));
        this.saveSightingBtn?.addEventListener('click', () => this.submitSighting());
    }

    async loadPets(keyword = '') {
        try {
            const resp = await fetch(`index.php?controller=api&action=getPets&keyword=${encodeURIComponent(keyword)}`);
            const res = await resp.json();
            if (res.status === 'success') this.renderData(res.data);
        } catch (e) { console.error(e); }
    }

    renderData(pets) {
        this.petListContainer.innerHTML = '';
        this.markers.forEach(m => this.map.removeLayer(m));
        this.markers = [];
        document.getElementById('petCount').innerText = `${pets.length} Results`;

        pets.forEach(pet => {
            const isMissing = pet.status === 'missing';
            const item = document.createElement('div');
            item.className = `list-group-item list-group-item-action status-${pet.status}`;
            item.innerHTML = `
                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                    <h6 class="mb-0 fw-bold">${pet.name}</h6>
                    <span class="text-uppercase small fw-bold ${isMissing ? 'text-danger' : 'text-success'}">${pet.status}</span>
                </div>
                <div class="text-muted small mb-2">${pet.type}</div>
                <p class="mb-3 small text-secondary">${pet.description}</p>
                <div class="d-flex gap-2">
                    ${pet.latest_lat ? `<button class="btn btn-sm btn-primary flex-grow-1 focus-btn">View on Map</button>` : ''}
                    ${this.isLoggedIn ? `<button class="btn btn-sm btn-outline-dark sighting-btn px-3">Report Sighting</button>` : ''}
                </div>`;
            
            this.petListContainer.appendChild(item);
            item.querySelector('.focus-btn')?.addEventListener('click', () => this.map.flyTo([pet.latest_lat, pet.latest_lon], 16));
            item.querySelector('.sighting-btn')?.addEventListener('click', () => this.triggerSighting(pet.id, pet.name));

            if (pet.latest_lat) {
                const m = L.marker([pet.latest_lat, pet.latest_lon]).addTo(this.map)
                    .bindPopup(`<div class="text-center"><strong>${pet.name}</strong><br><span class="small">${pet.status}</span></div>`);
                this.markers.push(m);
            }
        });
    }

    triggerSighting(id, name) {
        this.pendingPetId = id;
        document.getElementById('mainMap').style.border = "2px solid var(--success)";
        document.getElementById('mainMap').style.cursor = "crosshair";
    }

    handleMapClick(e) {
        if (!this.pendingPetId) return;
        document.getElementById('mainMap').style.border = "1px solid var(--border)";
        document.getElementById('mainMap').style.cursor = "";
        document.getElementById('sightingPetId').value = this.pendingPetId;
        document.getElementById('sightingLat').value = e.latlng.lat;
        document.getElementById('sightingLon').value = e.latlng.lng;
        this.sightingModal.show();
        this.pendingPetId = null;
    }

    async submitSighting() {
        const payload = {
            pet_id: document.getElementById('sightingPetId').value,
            lat: document.getElementById('sightingLat').value,
            lon: document.getElementById('sightingLon').value,
            location: document.getElementById('sightingLocation').value,
            note: document.getElementById('sightingNote').value
        };
        try {
            const resp = await fetch('index.php?controller=api&action=addSighting', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            if ((await resp.json()).status === 'success') {
                this.sightingModal.hide();
                this.loadPets(this.searchInput.value);
            }
        } catch (e) { console.error(e); }
    }
}
document.addEventListener('DOMContentLoaded', () => new PetMapApp());

