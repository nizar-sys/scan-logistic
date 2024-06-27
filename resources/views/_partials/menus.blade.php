
@if (Auth::check())
    <li class="nav-item">
        <a class="nav-link {{ Route::is('home.*') ? 'active' : '' }}" href="{{ route('home') }}">
            <i class="ni ni-tv-2 text-primary"></i>
            <span class="nav-link-text">Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Route::is('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
            <i class="fas fa-users text-warning"></i>
            <span class="nav-link-text">Data Pengguna</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Route::is('products.*') ? 'active' : '' }}"
            href="{{ route('products.index') }}">
            <i class="fas fa-building text-primary"></i>
            <span class="nav-link-text">Data Produk</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Route::is('tracking-records.*') ? 'active' : '' }}"
            href="{{ route('tracking-records.index') }}">
            <i class="fas fa-route text-primary"></i>
            <span class="nav-link-text">Data Pengiriman</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Route::is('invoices.*') ? 'active' : '' }}"
            href="{{ route('invoices.index') }}">
            <i class="fas fa-receipt text-danger"></i>
            <span class="nav-link-text">Data Invoice</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Route::is('profile.*') ? 'active' : '' }}" href="{{ route('profile') }}">
            <i class="fas fa-user-tie text-success"></i>
            <span class="nav-link-text">Profile</span>
        </a>
    </li>
@else
    <li class="nav-item">
        <a class="nav-link {{ Route::is('scan.*') ? 'active' : '' }}" href="{{ route('scan.index') }}">
            <i class="ni ni-tv-2 text-primary"></i>
            <span class="nav-link-text">Scan Nomor Resi</span>
        </a>
    </li>
@endif
