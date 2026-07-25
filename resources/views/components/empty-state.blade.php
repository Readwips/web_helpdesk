@props(['title' => 'Belum ada data', 'description' => 'Data akan tampil di sini setelah tersedia.', 'icon' => 'ticket'])
<div {{ $attributes->merge(['class'=>'empty-state']) }}><div class="empty-icon"><x-icon :name="$icon" size="22"/></div><div class="empty-title">{{ $title }}</div><p class="empty-copy">{{ $description }}</p>@isset($action)<div class="mt-5">{{ $action }}</div>@endisset</div>
