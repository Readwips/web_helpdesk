<div x-show="menu" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-ink/35 backdrop-blur-sm lg:hidden" @click="menu = false" aria-hidden="true"></div>

<aside
    :class="menu ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 flex w-[264px] flex-col border-r border-line bg-white transition duration-300 lg:translate-x-0"
    aria-label="Navigasi utama"
>
    <div class="flex h-[4.5rem] items-center justify-between border-b border-line px-5">
        <x-brand-mark href="{{ route('dashboard') }}" />
        <button type="button" class="icon-button lg:hidden" @click="menu = false" aria-label="Tutup menu navigasi">
            <x-icon name="close" class="h-5 w-5" />
        </button>
    </div>

    @php
        $navItems = [
            ['dashboard', 'dashboard', 'Dashboard'],
            ['tickets.index', 'ticket', 'Tiket Helpdesk'],
            ['assets.index', 'asset', 'Inventaris Aset'],
            ['knowledge.index', 'book', 'Pusat Pengetahuan'],
        ];

        $adminItems = [
            ['users.index', 'users', 'Pengguna'],
            ['departments.index', 'building', 'Departemen'],
            ['ticket-categories.index', 'tag', 'Kategori Tiket'],
            ['asset-categories.index', 'tag', 'Kategori Aset'],
            ['reports.index', 'report', 'Laporan'],
        ];

        $routePatterns = [
            'dashboard' => 'dashboard',
            'tickets.index' => 'tickets.*',
            'assets.index' => ['assets.*', 'repairs.*'],
            'knowledge.index' => 'knowledge.*',
            'users.index' => 'users.*',
            'departments.index' => 'departments.*',
            'ticket-categories.index' => 'ticket-categories.*',
            'asset-categories.index' => 'asset-categories.*',
            'reports.index' => 'reports.*',
        ];
    @endphp

    <nav class="flex-1 overflow-y-auto p-4">
        <p class="px-3 pb-2 pt-1 text-[0.65rem] font-bold uppercase tracking-[0.18em] text-muted">Ruang kerja</p>
        <div class="space-y-1">
            @foreach($navItems as [$routeName, $icon, $label])
                @php $active = request()->routeIs($routePatterns[$routeName]); @endphp
                <a href="{{ route($routeName) }}" class="nav-item {{ $active ? 'nav-item-active' : '' }}" @click="menu = false" @if($active) aria-current="page" @endif>
                    <x-icon :name="$icon" class="h-[1.125rem] w-[1.125rem] shrink-0" />
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>

        @if(auth()->user()->isAdmin())
            <p class="px-3 pb-2 pt-7 text-[0.65rem] font-bold uppercase tracking-[0.18em] text-muted">Administrasi</p>
            <div class="space-y-1">
                @foreach($adminItems as [$routeName, $icon, $label])
                    @php $active = request()->routeIs($routePatterns[$routeName]); @endphp
                    <a href="{{ route($routeName) }}" class="nav-item {{ $active ? 'nav-item-active' : '' }}" @click="menu = false" @if($active) aria-current="page" @endif>
                        <x-icon :name="$icon" class="h-[1.125rem] w-[1.125rem] shrink-0" />
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </nav>

    <div class="border-t border-line p-4">
        <a class="nav-item {{ request()->routeIs('profile.*') ? 'nav-item-active' : '' }}" href="{{ route('profile.edit') }}">
            <x-icon name="profile" class="h-[1.125rem] w-[1.125rem]" />
            <span>Profil Saya</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit" class="nav-item w-full">
                <x-icon name="logout" class="h-[1.125rem] w-[1.125rem]" />
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
