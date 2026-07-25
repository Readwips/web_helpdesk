@props(['type' => 'status', 'value'])
@php
$colors = [
    'baru'=>'border-blue-200 bg-blue-50 text-blue-700','ditugaskan'=>'border-violet-200 bg-violet-50 text-violet-700',
    'diproses'=>'border-amber-200 bg-amber-50 text-amber-800','menunggu_konfirmasi'=>'border-cyan-200 bg-cyan-50 text-cyan-800',
    'selesai'=>'border-emerald-200 bg-emerald-50 text-emerald-700','dibatalkan'=>'border-line bg-[#EFEFEA] text-[#666660]',
    'rendah'=>'border-line bg-[#F5F5F1] text-[#666660]','sedang'=>'border-blue-200 bg-blue-50 text-blue-700',
    'tinggi'=>'border-orange-200 bg-orange-50 text-orange-700','kritis'=>'border-red-200 bg-red-50 text-red-700',
    'tersedia'=>'border-emerald-200 bg-emerald-50 text-emerald-700','digunakan'=>'border-blue-200 bg-blue-50 text-blue-700',
    'dipinjamkan'=>'border-violet-200 bg-violet-50 text-violet-700','diperbaiki'=>'border-amber-200 bg-amber-50 text-amber-700',
    'rusak'=>'border-red-200 bg-red-50 text-red-700','tidak_aktif'=>'border-line bg-[#EFEFEA] text-[#666660]',
    'active'=>'border-emerald-200 bg-emerald-50 text-emerald-700','inactive'=>'border-line bg-[#EFEFEA] text-[#666660]',
    'baik'=>'border-emerald-200 bg-emerald-50 text-emerald-700','perlu_perawatan'=>'border-amber-200 bg-amber-50 text-amber-700',
    'rusak_ringan'=>'border-orange-200 bg-orange-50 text-orange-700','rusak_berat'=>'border-red-200 bg-red-50 text-red-700',
    'published'=>'border-emerald-200 bg-emerald-50 text-emerald-700','draft'=>'border-amber-200 bg-amber-50 text-amber-700',
    'archived'=>'border-line bg-[#EFEFEA] text-[#666660]',
];
@endphp
<span {{ $attributes->merge(['class'=>'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11px] font-bold '.($colors[$value]??'border-line bg-[#F5F5F1] text-[#666660]')]) }}><span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>{{ str($value)->replace('_',' ')->title() }}</span>
