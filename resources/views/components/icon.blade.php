@props(['name', 'size' => 20])
<svg {{ $attributes->merge(['class' => 'shrink-0']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
@case('dashboard')<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="4" rx="1"/><rect x="14" y="11" width="7" height="10" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>@break
@case('ticket')<path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5V9a3 3 0 0 0 0 6v3.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18.5V15a3 3 0 0 0 0-6z"/><path d="M9 8h6M9 12h4"/>@break
@case('asset')<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>@break
@case('book')<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21zM20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5A2.5 2.5 0 0 1 20 21z"/>@break
@case('users')<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>@break
@case('building')<path d="M4 21V5l8-3 8 3v16M9 9h1M14 9h1M9 13h1M14 13h1M9 17h6"/>@break
@case('tag')<path d="M20 13 13 20l-9-9V4h7z"/><circle cx="8.5" cy="8.5" r="1"/>@break
@case('report')<path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/>@break
@case('profile')<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>@break
@case('logout')<path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/>@break
@case('menu')<path d="M4 7h16M4 12h16M4 17h16"/>@break
@case('close')<path d="m6 6 12 12M18 6 6 18"/>@break
@case('search')<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>@break
@case('plus')<path d="M12 5v14M5 12h14"/>@break
@case('arrow')<path d="m5 12 14 0M14 7l5 5-5 5"/>@break
@case('clock')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@break
@case('check')<path d="m5 12 4 4L19 6"/>@break
@case('alert')<path d="M12 3 2.5 20h19zM12 9v4M12 17h.01"/>@break
@case('message')<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>@break
@case('paperclip')<path d="m20.5 11.5-8.8 8.8a6 6 0 0 1-8.5-8.5l9.5-9.5a4 4 0 0 1 5.7 5.7l-9.6 9.5a2 2 0 0 1-2.8-2.8l8.8-8.8"/>@break
@case('wrench')<path d="M14 6a4 4 0 0 0-5-4l2.4 2.4-3 3L6 5a4 4 0 0 0 4 5L3 17l4 4 7-7a4 4 0 0 0 5-5l-2.4 2.4-3-3z"/>@break
@case('download')<path d="M12 3v12M7 10l5 5 5-5M5 21h14"/>@break
@default<circle cx="12" cy="12" r="9"/>@endswitch
</svg>
