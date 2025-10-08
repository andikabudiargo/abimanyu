<!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper flex items-center h-14 max-sm:px-[15px] px-[25px] grow
              bg-transparent lg:bg-transparent 
              max-lg:bg-teal-400 max-lg:text-white relative">

    <!-- [Mobile Burger] tetap tampil di mobile -->
    <div class="me-auto pc-mob-drp">
      <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
        <!-- ======= Menu collapse Icon ===== -->
        <li class="pc-h-item pc-sidebar-collapse max-lg:hidden lg:inline-flex">
          <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="sidebar-hide">
            <i data-feather="menu"></i>
          </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup lg:hidden">
          <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="mobile-collapse">
            <i data-feather="menu"></i>
          </a>
        </li>
      </ul>
    </div>

    <!-- Logo di tengah hanya untuk mobile/tablet -->
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 lg:hidden flex items-center">
      <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-8">
    </div>

    <!-- Right menu -->
    <div class="ms-auto">
      <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
        <li class="dropdown pc-h-item">
          <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" aria-expanded="false">
            <i data-feather="sun"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
            <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
              <i data-feather="moon"></i>
              <span>Dark</span>
            </a>
            <a href="#!" class="dropdown-item" onclick="layout_change('light')">
              <i data-feather="sun"></i>
              <span>Light</span>
            </a>
            <a href="#!" class="dropdown-item" onclick="layout_change_default()">
              <i data-feather="settings"></i>
              <span>Default</span>
            </a>
          </div>
        </li>
    <!--<li class="dropdown pc-h-item">
      <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
        aria-haspopup="false" aria-expanded="false">
        <i data-feather="settings"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
        <a href="#!" class="dropdown-item">
          <i class="ti ti-user"></i>
          <span>My Account</span>
        </a>
        <a href="#!" class="dropdown-item">
          <i class="ti ti-settings"></i>
          <span>Settings</span>
        </a>
        <a href="#!" class="dropdown-item">
          <i class="ti ti-headset"></i>
          <span>Support</span>
        </a>
        <a href="#!" class="dropdown-item">
          <i class="ti ti-lock"></i>
          <span>Lock Screen</span>
        </a>
        <a href="#!" class="dropdown-item">
          <i class="ti ti-power"></i>
          <span>Logout</span>
        </a>
      </div>
    </li>
   @php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$canApprove = $user->roles()->whereIn('name', ['Supervisor Special Access', 'Manager Special Access'])->exists() &&
              $user->departments()->where('name', 'Information & Technology')->exists();

$ticketsToApprove = $canApprove
    ? \App\Models\Ticket::where('status', 'Pending')->latest()->take(5)->get()
    : collect(); // kosong jika tidak berhak
@endphp

@if ($canApprove)
<li class="dropdown pc-h-item relative">
  <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button">
    <i data-feather="bell"></i>
    @if ($ticketsToApprove->count())
      <span class="badge bg-success-500 text-white rounded-full z-10 absolute right-0 top-0">{{ $ticketsToApprove->count() }}</span>
    @endif
  </a>
  <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown p-2 w-96">
    <div class="dropdown-header flex items-center justify-between py-4 px-5">
      <h5 class="m-0">Tickets to Approve</h5>
      <a href="{{ route('it.ticket.index') }}" class="btn btn-link btn-sm">View All</a>
    </div>
    <div class="dropdown-body header-notification-scroll relative py-2 px-3" style="max-height: 400px; overflow-y: auto;">
      @forelse ($ticketsToApprove as $ticket)
      <div class="card mb-2 shadow-sm border border-gray-200">
        <div class="card-body px-3 py-2">
          <div class="text-sm font-semibold text-gray-700 mb-1">
            {{ $ticket->ticket_number }} - {{ $ticket->title }}
          </div>
          <p class="text-xs text-gray-600 mb-2">{{ $ticket->category }}</p>
          <div class="flex justify-end gap-2">
            <a href=""
              class="btn btn-xs btn-outline-primary">Detail</a>
            <button onclick="approveTicket({{ $ticket->id }})" class="btn btn-xs btn-success">Approve</button>
            <button onclick="rejectTicket({{ $ticket->id }})" class="btn btn-xs btn-danger">Reject</button>
          </div>
        </div>
      </div>
      @empty
      <p class="text-sm text-gray-500 text-center">No pending tickets.</p>
      @endforelse
    </div>
    <div class="text-center py-2">
      <a href="{{ route('it.ticket.index') }}" class="text-primary hover:underline">Lihat semua ticket</a>
    </div>
  </div>
</li>
@endif-->


   <li class="dropdown pc-h-item header-user-profile">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-pc-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" data-pc-auto-close="outside" aria-expanded="false">
            <i data-feather="user"></i>
          </a>
          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown p-2 overflow-hidden">
            <div class="dropdown-header flex items-center justify-between py-4 px-5 bg-primary-500">
              <div class="flex mb-1 items-center">
                <div class="shrink-0">
                 <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('img/avatar-dummy.png') }}"
         alt="user-image" class="w-10 h-10 bg-white rounded-full object-cover" />
                </div>
                <div class="grow ms-3">
                  <h6 class="mb-1 text-white font-bold">{{ Auth::user()->name }}</h6>
                  <span class="text-white"> {{ Auth::user()->departments->first()->name ?? '-' }}</span>
                </div>
              </div>
            </div>
            <div class="dropdown-body py-4 px-5">
              <a href="{{ route('setting.user.edit', $user->id) }}" class="dropdown-item">
                <span>
                    <svg class="pc-icon text-muted me-2 inline-block">
                        <use xlink:href="#custom-lock-outline"></use>
                    </svg>
                    <span>Change Password</span>
                </span>
              </a>
              <div class="grid my-3">
               <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-primary flex items-center justify-center w-full">
                    <svg class="pc-icon me-2 w-[22px] h-[22px]">
                      <use xlink:href="#custom-logout-1-outline"></use>
                    </svg>
                    Logout
                  </button>
               </form>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>
<!-- [ Header ] end -->