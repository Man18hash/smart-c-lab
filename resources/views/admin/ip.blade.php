@extends('layouts.admin')
@section('title', 'IP Assets')

@section('content')
<style>
  .filter-pills .nav-link{ border-radius:999px; }
  .status-badge{text-transform:capitalize}
  .table thead th{ white-space:nowrap; }
  .map-wrap { height: 320px; border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb; }
  .ol-zoom { top: .5rem; left: .5rem; }
  .coords-help { font-size: .85rem; color: #6c757d; }
</style>

<div class="bg-white rounded-3 shadow-sm p-4">
  <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <h5 class="mb-0">IP Assets</h5>

    <form class="d-flex" method="GET" action="{{ route('admin.ip') }}">
      <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
      <input type="search" name="q" class="form-control me-2" placeholder="Search name or IP" value="{{ $q ?? '' }}">
      <button class="btn btn-outline-secondary" type="submit">Search</button>
    </form>
  </div>

  @php
    $tab = $status ?? 'all';
    $tabs = ['all'=>'All','free'=>'Available','assigned'=>'Assigned','blocked'=>'Blocked'];
  @endphp
  <ul class="nav nav-pills filter-pills mb-3">
    @foreach($tabs as $k=>$label)
      <li class="nav-item">
        <a class="nav-link {{ $tab===$k ? 'active':'' }}"
           href="{{ route('admin.ip', ['status'=>$k] + (!empty($q) ? ['q'=>$q] : [])) }}">{{ $label }}</a>
      </li>
    @endforeach
  </ul>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <div class="d-flex justify-content-end mb-2">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createIpModal">
      <i class="fa-solid fa-plus me-1"></i> Add IP
    </button>
  </div>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Name</th>
          <th>IP Address</th>
          <th>Status</th>
          <th>Notes</th>
          <th>Location</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($ipAssets as $ip)
          @php
            $map = ['free'=>'success','assigned'=>'warning','blocked'=>'danger'];
            $cls = $map[$ip->status] ?? 'secondary';
            $label = $ip->status === 'free' ? 'Available' : ucfirst($ip->status);
          @endphp
          <tr>
            <td class="fw-semibold">{{ $ip->name }}</td>
            <td><code>{{ $ip->ip_address }}</code></td>
            <td><span class="badge bg-{{ $cls }} status-badge">{{ $label }}</span></td>
            <td class="text-muted">{{ $ip->notes ?: '—' }}</td>
            <td class="text-muted">
              @if($ip->hasLocation())
                <span class="d-inline-flex align-items-center gap-2">
                  <i class="fa-solid fa-location-dot"></i>
                  <span>{{ number_format($ip->latitude, 6) }}, {{ number_format($ip->longitude, 6) }}</span>
                  <button class="btn btn-sm btn-outline-secondary ms-2"
                          data-bs-toggle="modal" data-bs-target="#viewMap-{{ $ip->id }}">
                    View Map
                  </button>
                </span>
              @else
                —
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editIp-{{ $ip->id }}">
                Edit
              </button>
              <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteIp-{{ $ip->id }}">
                Delete
              </button>
            </td>
          </tr>

          {{-- View Map (readonly) --}}
          @if($ip->hasLocation())
          <div class="modal fade" id="viewMap-{{ $ip->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title">Location — {{ $ip->name }}</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div id="ol-view-{{ $ip->id }}" class="map-wrap"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
          @endif

          {{-- Edit --}}
          <div class="modal fade" id="editIp-{{ $ip->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.ip.update', $ip) }}">
                  @csrf @method('PUT')
                  <div class="modal-header">
                    <h6 class="modal-title">Edit IP</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Name</label>
                          <input type="text" name="name" class="form-control" value="{{ old('name',$ip->name) }}" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">IP Address</label>
                          <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address',$ip->ip_address) }}" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Status</label>
                          <select name="status" class="form-select" required>
                            @foreach(['free'=>'Available','assigned'=>'Assigned','blocked'=>'Blocked'] as $val=>$txt)
                              <option value="{{ $val }}" @selected(old('status',$ip->status)===$val)>{{ $txt }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Notes</label>
                          <textarea name="notes" rows="2" class="form-control">{{ old('notes',$ip->notes) }}</textarea>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Address (optional)</label>
                          <input type="text" name="address" class="form-control" value="{{ old('address',$ip->address) }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-2">
                          <label class="form-label">Latitude & Longitude</label>
                          <div class="row g-2">
                            <div class="col-6">
                              <input id="lat-edit-{{ $ip->id }}" type="text" name="latitude" class="form-control"
                                     value="{{ old('latitude', $ip->latitude) }}" placeholder="Latitude (-90..90)">
                            </div>
                            <div class="col-6">
                              <input id="lng-edit-{{ $ip->id }}" type="text" name="longitude" class="form-control"
                                     value="{{ old('longitude', $ip->longitude) }}" placeholder="Longitude (-180..180)">
                            </div>
                          </div>
                          <div class="coords-help mt-1">Tip: Click on the map to set the pin & coordinates.</div>
                        </div>
                        <div id="ol-edit-{{ $ip->id }}" class="map-wrap"></div>
                        <div class="mt-2 d-flex gap-2">
                          <button class="btn btn-sm btn-outline-secondary" type="button"
                                  onclick="olLocateMe('ol-edit-{{ $ip->id }}','lat-edit-{{ $ip->id }}','lng-edit-{{ $ip->id }}')">
                            Use my location
                          </button>
                          <button class="btn btn-sm btn-outline-secondary" type="button"
                                  onclick="olCenterFromInputs('ol-edit-{{ $ip->id }}','lat-edit-{{ $ip->id }}','lng-edit-{{ $ip->id }}')">
                            Center to inputs
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          {{-- Delete --}}
          <div class="modal fade" id="deleteIp-{{ $ip->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.ip.destroy', $ip) }}">
                  @csrf @method('DELETE')
                  <div class="modal-header">
                    <h6 class="modal-title">Delete IP</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete <strong>{{ $ip->name }}</strong> ({{ $ip->ip_address }})?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @empty
          <tr><td colspan="6" class="text-muted">No IPs found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($ipAssets instanceof \Illuminate\Contracts\Pagination\Paginator && $ipAssets->hasPages())
    <div class="mt-2">@php echo $ipAssets->links('pagination::bootstrap-5'); @endphp</div>
  @endif
</div>

{{-- Create --}}
<div class="modal fade" id="createIpModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.ip.store') }}">
        @csrf
        <div class="modal-header">
          <h6 class="modal-title">Add IP</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">IP Address</label>
                <input type="text" name="ip_address" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                  <option value="free">Available</option>
                  <option value="assigned">Assigned</option>
                  <option value="blocked">Blocked</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-control" placeholder="Optional"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Address (optional)</label>
                <input type="text" name="address" class="form-control" placeholder="e.g., Library, Room 203">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-2">
                <label class="form-label">Latitude & Longitude</label>
                <div class="row g-2">
                  <div class="col-6">
                    <input id="lat-create" type="text" name="latitude" class="form-control" placeholder="Latitude (-90..90)">
                  </div>
                  <div class="col-6">
                    <input id="lng-create" type="text" name="longitude" class="form-control" placeholder="Longitude (-180..180)">
                  </div>
                </div>
                <div class="coords-help mt-1">Click on the map to set the pin & coordinates.</div>
              </div>
              <div id="ol-create" class="map-wrap"></div>
              <div class="mt-2 d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" type="button"
                        onclick="olLocateMe('ol-create','lat-create','lng-create')">
                  Use my location
                </button>
                <button class="btn btn-sm btn-outline-secondary" type="button"
                        onclick="olCenterFromInputs('ol-create','lat-create','lng-create')">
                  Center to inputs
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add IP</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  {{-- OpenLayers (no key needed) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v9.2.4/ol.css">
  <script src="https://cdn.jsdelivr.net/npm/ol@v9.2.4/dist/ol.js"></script>

  <script>
    // Store map instances by container id
    const OL_MAPS = {};

    function olInitPicker(containerId, latInputId, lngInputId, defaultLat = 14.5995, defaultLng = 120.9842, zoom = 15) {
      // Use existing inputs if present
      const latEl = document.getElementById(latInputId);
      const lngEl = document.getElementById(lngInputId);
      let lat = parseFloat(latEl?.value) || defaultLat;
      let lng = parseFloat(lngEl?.value) || defaultLng;

      // Build layers
      const tile = new ol.layer.Tile({ source: new ol.source.OSM() });
      const vectorSource = new ol.source.Vector();
      const vectorLayer = new ol.layer.Vector({ source: vectorSource });

      const center = ol.proj.fromLonLat([lng, lat]);
      const map = new ol.Map({
        target: containerId,
        layers: [tile, vectorLayer],
        view: new ol.View({ center, zoom }),
      });

      const feature = new ol.Feature({ geometry: new ol.geom.Point(center) });
      vectorSource.addFeature(feature);

      // On click, move pin & update inputs
      map.on('singleclick', (evt) => {
        feature.getGeometry().setCoordinates(evt.coordinate);
        const [lon, la] = ol.proj.toLonLat(evt.coordinate);
        if (latEl) latEl.value = la.toFixed(6);
        if (lngEl) lngEl.value = lon.toFixed(6);
      });

      OL_MAPS[containerId] = { map, vectorLayer, feature };
      // Fix rendering when map is inside hidden modal
      setTimeout(() => map.updateSize(), 150);
    }

    function olCenterFromInputs(containerId, latInputId, lngInputId) {
      const ctx = OL_MAPS[containerId];
      if (!ctx) return;

      const lat = parseFloat(document.getElementById(latInputId)?.value);
      const lng = parseFloat(document.getElementById(lngInputId)?.value);
      if (isNaN(lat) || isNaN(lng)) return;

      const coord = ol.proj.fromLonLat([lng, lat]);
      ctx.map.getView().animate({ center: coord, duration: 300 });
      ctx.feature.getGeometry().setCoordinates(coord);
    }

    function olLocateMe(containerId, latInputId, lngInputId) {
      if (!navigator.geolocation) return alert('Geolocation not available');
      navigator.geolocation.getCurrentPosition((pos) => {
        const { latitude: lat, longitude: lng } = pos.coords;
        document.getElementById(latInputId).value = lat.toFixed(6);
        document.getElementById(lngInputId).value = lng.toFixed(6);
        olCenterFromInputs(containerId, latInputId, lngInputId);
      }, (err) => {
        alert('Unable to get current position');
      });
    }

    // Hook modal show events to init maps
    document.addEventListener('shown.bs.modal', (e) => {
      const modal = e.target;

      // CREATE
      if (modal.id === 'createIpModal') {
        if (!OL_MAPS['ol-create']) {
          olInitPicker('ol-create', 'lat-create', 'lng-create');
        } else {
          setTimeout(() => OL_MAPS['ol-create'].map.updateSize(), 150);
        }
      }

      // EDIT
      if (modal.id.startsWith('editIp-')) {
        const id = modal.id.split('editIp-')[1];
        const mapId = `ol-edit-${id}`;
        const latId = `lat-edit-${id}`;
        const lngId = `lng-edit-${id}`;
        if (!OL_MAPS[mapId]) {
          // Read current values if exist; fallback to Manila
          const lat = parseFloat(document.getElementById(latId)?.value) || 14.5995;
          const lng = parseFloat(document.getElementById(lngId)?.value) || 120.9842;
          olInitPicker(mapId, latId, lngId, lat, lng);
        } else {
          setTimeout(() => OL_MAPS[mapId].map.updateSize(), 150);
        }
      }

      // VIEW (readonly)
      if (modal.id.startsWith('viewMap-')) {
        const id = modal.id.split('viewMap-')[1];
        const wrapId = `ol-view-${id}`;
        if (!OL_MAPS[wrapId]) {
          const wrap = document.getElementById(wrapId);
          if (!wrap) return;

          const lat = parseFloat(wrap.dataset.lat || wrap.getAttribute('data-lat') || '{{ 14.5995 }}');
          const lng = parseFloat(wrap.dataset.lng || wrap.getAttribute('data-lng') || '{{ 120.9842 }}');

          // Build a simple readonly map
          const tile = new ol.layer.Tile({ source: new ol.source.OSM() });
          const vectorSource = new ol.source.Vector();
          const vectorLayer = new ol.layer.Vector({ source: vectorSource });

          const center = ol.proj.fromLonLat([lng, lat]);
          const map = new ol.Map({
            target: wrapId,
            layers: [tile, vectorLayer],
            view: new ol.View({ center, zoom: 16 }),
          });

          const feature = new ol.Feature({ geometry: new ol.geom.Point(center) });
          vectorSource.addFeature(feature);

          OL_MAPS[wrapId] = { map, vectorLayer, feature };
          setTimeout(() => map.updateSize(), 150);
        } else {
          setTimeout(() => OL_MAPS[wrapId].map.updateSize(), 150);
        }
      }
    });

    // Inject lat/lng into view map containers (data- attrs)
    document.addEventListener('DOMContentLoaded', function () {
      @foreach($ipAssets as $ip)
        @if($ip->hasLocation())
          (function(){
            const el = document.getElementById('ol-view-{{ $ip->id }}');
            if (el) { el.setAttribute('data-lat', '{{ $ip->latitude }}'); el.setAttribute('data-lng', '{{ $ip->longitude }}'); }
          })();
        @endif
      @endforeach
    });
  </script>
@endpush
