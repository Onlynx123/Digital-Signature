<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — DigiSign</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/digisign.css') }}">
    @stack('styles')
</head>
<body>

{{-- ===== SIDEBAR ===== --}}
<nav id="sidebar">
    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-pen-fill"></i></div>
        <div>
            <span class="brand-name">DigiSign</span>
            <span class="brand-sub">Proof of Concept</span>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="sidebar-nav mt-2">
        <div class="sidebar-section-label">Menu Utama</div>

        <div class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                Dashboard
            </a>
        </div>

        <div class="sidebar-section-label">Dokumen</div>

        <div class="nav-item">
            <a href="{{ route('documents.index') }}"
               class="nav-link {{ request()->routeIs('documents.index', 'documents.show') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-pdf"></i>
                Semua Dokumen
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('documents.create') }}"
               class="nav-link {{ request()->routeIs('documents.create') ? 'active' : '' }}">
                <i class="bi bi-cloud-upload"></i>
                Upload Dokumen
            </a>
        </div>

        <div class="nav-item">
            @php $pendingCount = auth()->user()->pendingSignatures()->count(); @endphp
            <a href="{{ route('signatures.pending') }}"
               class="nav-link {{ request()->routeIs('signatures.pending') ? 'active' : '' }}">
                <i class="bi bi-pen"></i>
                Pending Tanda Tangan
                @if($pendingCount > 0)
                    <span class="badge-count">{{ $pendingCount }}</span>
                @endif
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('verify.index') }}"
               class="nav-link {{ request()->routeIs('verify.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                Verify Dokumen
            </a>
        </div>

        <hr class="sidebar-divider">

        <div class="nav-item">
            <a href="{{ route('audit.index') }}"
               class="nav-link {{ request()->routeIs('audit.index') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                Audit Log Saya
            </a>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="sidebar-section-label">Admin</div>

        <div class="nav-item">
            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                Kelola User
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.audit.index') }}"
               class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                <i class="bi bi-list-columns"></i>
                Semua Audit Log
            </a>
        </div>
        @endif
    </div>

    {{-- User card at bottom --}}
    <div class="sidebar-user-card mt-auto">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </button>
        </form>
    </div>
</nav>

{{-- ===== MAIN CONTENT ===== --}}
<div id="main-content">

    {{-- Topbar --}}
    <header id="topbar">
        <div>
            <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
            @hasSection('breadcrumb')
                <div class="topbar-breadcrumb">@yield('breadcrumb')</div>
            @endif
        </div>
        <div class="d-flex align-items-center gap-3">
            @yield('topbar-actions')
            {{-- Mobile menu toggle --}}
            <button id="sidebar-toggle" class="topbar-mobile-toggle">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </header>

    {{-- Flash messages --}}
    <div class="px-4 pt-3">
        @if(session('success'))
        <div class="ds-alert success">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <div>{{ session('success') }}</div>
            <button onclick="this.parentElement.remove()" class="ds-alert-close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="ds-alert error">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <div>{{ session('error') }}</div>
            <button onclick="this.parentElement.remove()" class="ds-alert-close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        @endif

        @if($errors->any())
        <div class="ds-alert error">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <ul style="margin: 0; padding-left: 1.1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- Main Page Content --}}
    <main class="page-content">
        @yield('content')
    </main>
</div>

<script>
// Mobile sidebar toggle
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar = document.getElementById('sidebar');
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
}
// Close sidebar on outside click
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && e.target !== sidebarToggle) {
        sidebar.classList.remove('open');
    }
});
</script>
@stack('scripts')
</body>
</html>
