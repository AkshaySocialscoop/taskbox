 @php
    $authUser = auth()->user();
    $photo = $authUser->userinfo?->profile_photo;
@endphp
 <!--start header-->
  <header class="top-header">
    <nav class="navbar navbar-expand align-items-center gap-4"> 
      <div class="search-bar flex-grow-1">
        <div class="position-relative text-center">
         <h6 class="mb-0"> {{ now()->format('l, d F Y') }} &nbsp;&nbsp; <span id="istClock"></span> </h6> 
        </div>
      </div>
      <ul class="navbar-nav gap-1 nav-right-links align-items-center">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
            data-bs-auto-close="outside"
            data-bs-toggle="dropdown"
            href="javascript:;">
            
            <i class="material-icons-outlined">notifications</i>
 
              @if($notifications->count() > 0)
              <span class="badge-notify">
                  {{ $notifications->count() }}
              </span>
              @endif
          </a>

          <div class="dropdown-menu dropdown-notify dropdown-menu-end shadow">
            <div class="px-3 py-1 d-flex align-items-center justify-content-between border-bottom">
              <h5 class="notiy-title mb-0">Notifications</h5>
              <!-- <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle dropdown-toggle-nocaret option" type="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <span class="material-icons-outlined">
                    more_vert
                  </span>
                </button>
                <div class="dropdown-menu dropdown-option dropdown-menu-end shadow"> 
                  <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                        class="material-icons-outlined fs-6">done_all</i>Mark all as read</a></div>  
                </div>
              </div> -->
            </div>
            
            <div class="notify-list"> 
              
              @foreach($notifications as $notification)
                @if(
                    auth()->user()->role === 'super_admin' ||
                    $notification->created_by === auth()->id()
                ) 
              <div> 
                <a class="dropdown-item border-bottom py-2 " href="javascript:;">
                  <div class="d-flex align-items-center gap-3">
                    <div class=""> 
                        @if($notification->userinfo && $notification->userinfo->profile_photo)
                          <img src="{{ asset('storage/' . $notification->userinfo->profile_photo) }}"
                              class="rounded-circle"
                              width="45"
                              height="45"
                              alt="User">
                        @else  
                          <img src="{{ asset('assets/images/avatars/01.png') }}"
                              class="rounded-circle"
                              width="45"
                              height="45"
                              alt="User">
                        @endif 
                    </div>
                    <div class=""> 
                          <h5 class="notify-title mb-1"> 
                              {{ $notification->user->name }}<span class="badge bg-primary-subtle text-primary fw-semibold ms-2">New</span> 
                        </h5>

                        <p class="mb-1 notify-desc text-muted">
                           {{ $notification->task_name }}
                        </p>

                        <p class="mb-0 notify-time text-secondary small">
                           {{ $notification->created_at->diffForHumans() }}
                        </p> 
                    </div>
                    <div class="notify-close position-absolute end-0 me-3"
                        data-id="{{ $notification->id }}">
                        <i class="material-icons-outlined fs-6">close</i>
                    </div>
                  </div>
                </a>
              </div> 
              {{-- show notification --}}
              @endif
              @endforeach
            </div>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a href="javascrpt:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
             <img src="{{ $photo
        ? asset('storage/' . $photo)
        : asset('assets/images/avatars/01.png') }}" class="rounded-circle p-1 border" width="45" height="45">
          </a>
          <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
            <a class="dropdown-item  gap-2 py-2" href="javascript:;">
              <div class="text-center">
                <img src="{{ $photo
        ? asset('storage/' . $photo)
        : asset('assets/images/avatars/01.png') }}" class="rounded-circle p-1 shadow mb-3" width="90" height="90"
                  alt="">
                <h5 class="user-name mb-0 fw-bold">  {{ auth()->user()->name }}</h5>
              </div>
            </a>
            <hr class="dropdown-divider">
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="/user/profile"><i
              class="material-icons-outlined">person_outline</i>Profile</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" data-bs-toggle="modal" data-bs-target="#checkOutModal"><i
              class="material-icons-outlined">person_outline</i>Check Out</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item d-flex align-items-center gap-2 py-2 logout-btn" href="#">
                <i class="material-icons-outlined">power_settings_new</i> Logout
            </a>


          <form id="logout-form"
                action="{{ route('logout') }}"
                method="POST"
                class="d-none">
              @csrf
          </form>

          </div>
        </li>
      </ul>

    </nav>
  </header>
  <!--end top header-->
<!-- Check Out Modal -->
<div class="modal fade" id="checkOutModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Check Out</h5>
            </div>

            <div class="modal-body text-center">
                
                <!-- ✅ UNIQUE MAP ID -->
                <div id="checkoutMap" style="height: 300px; width:100%;"></div>

                <!-- ✅ UNIQUE STATUS -->
                <p id="checkoutStatus" class="mt-2"></p>

                <button class="btn btn-danger" id="checkoutBtn" onclick="checkOut()">
                    Check Out
                </button> 

            </div>
        </div>
    </div>
</div>

  <style>
    .dropdown-notify .notify-list {
        max-height: 340px;
        overflow-y: auto;
    }

    .dropdown-notify .notify-unread {
        background-color: #f5f7ff;
    }

    .dropdown-notify .notify-unread .notify-title {
        font-weight: 600;
    }

    .dropdown-notify .notify-title {
        font-size: 0.9rem;
    }

    .dropdown-notify .notify-desc {
        font-size: 0.8rem;
        line-height: 1.3;
    }

    .dropdown-notify .notify-time {
        font-size: 0.75rem;
    }
  </style>


<script>
let checkoutMap;

// ✅ When modal opens → initialize map
document.getElementById('checkOutModal')
    .addEventListener('shown.bs.modal', function () {
        initCheckOutMap();
    });

// ✅ MAP INIT FUNCTION
function initCheckOutMap() {

    let officeLat = 19.093426;
    let officeLng = 72.916283;

    if (checkoutMap) {
        checkoutMap.remove();
    }

    checkoutMap = L.map('checkoutMap').setView([officeLat, officeLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(checkoutMap);

    // Office marker
    L.marker([officeLat, officeLng]).addTo(checkoutMap)
        .bindPopup("Office Location")
        .openPopup();

    // Radius
    L.circle([officeLat, officeLng], {
        radius: 100,
        color: 'green'
    }).addTo(checkoutMap);

    // 🔥 IMPORTANT FIX
    setTimeout(() => {
        checkoutMap.invalidateSize();
    }, 300);
}

// ✅ CHECKOUT FUNCTION
function checkOut() {

    const btn = document.getElementById('checkoutBtn');
    const status = document.getElementById('checkoutStatus');

    btn.disabled = true;
    btn.innerText = "Checking out...";

    if (!navigator.geolocation) {
        alert("Geolocation not supported");
        btn.disabled = false;
        btn.innerText = "Check Out";
        return;
    }

    navigator.geolocation.getCurrentPosition(function(position) {

        let userLat = position.coords.latitude;
        let userLng = position.coords.longitude;

        let officeLat = 19.093426;
        let officeLng = 72.916283;

        let distance = getDistance(userLat, userLng, officeLat, officeLng);

        // Show user marker
        L.marker([userLat, userLng]).addTo(checkoutMap)
            .bindPopup("You are here")
            .openPopup();

        status.innerText = `Distance: ${Math.round(distance)} meters`;

        // ✅ Accuracy check (VERY IMPORTANT)
        if (position.coords.accuracy > 50) {
            status.innerText = "⚠️ Low GPS accuracy, try again";
            btn.disabled = false;
            btn.innerText = "Check Out";
            return;
        }

        if (distance > 100) {
            status.innerText += " ❌ Out of range";
            btn.disabled = false;
            btn.innerText = "Check Out";
            return;
        }

        // ✅ API CALL
        fetch('/check-out', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                lat: userLat,
                lng: userLng
            })
        })
        .then(res => res.json())
        .then(data => {

            if (data.error) {
                status.innerText = "❌ " + data.error;
                btn.disabled = false;
                btn.innerText = "Check Out";
                return;
            }

            status.innerText = "✅ Checked out successfully";
            setTimeout(() => location.reload(), 1000);
        })
        .catch(() => {
            status.innerText = "❌ Server error";
            btn.disabled = false;
            btn.innerText = "Check Out";
        });

    }, function(error) {
        alert(error.message);
        btn.disabled = false;
        btn.innerText = "Check Out";
    }, {
        enableHighAccuracy: true
    });
}


// ✅ DISTANCE FUNCTION
function getDistance(lat1, lon1, lat2, lon2) {

    let R = 6371e3;

    let φ1 = lat1 * Math.PI / 180;
    let φ2 = lat2 * Math.PI / 180;

    let Δφ = (lat2 - lat1) * Math.PI / 180;
    let Δλ = (lon2 - lon1) * Math.PI / 180;

    let a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
        Math.cos(φ1) * Math.cos(φ2) *
        Math.sin(Δλ / 2) * Math.sin(Δλ / 2);

    let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
}
</script>
<script>
setInterval(() => {
    const time = new Date().toLocaleTimeString("en-IN", {
        timeZone: "Asia/Kolkata",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true
    });
    document.getElementById("istClock").innerHTML = time;
}, 1000);
</script>
 