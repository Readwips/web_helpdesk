@props(['type' => 'info'])
@php $classes=['success'=>'alert-success','error'=>'alert-error','info'=>'alert-info','warning'=>'alert border-amber-200 bg-amber-50 text-amber-900'];$icons=['success'=>'check','error'=>'alert','info'=>'message','warning'=>'alert']; @endphp
<div role="alert" {{ $attributes->merge(['class'=>$classes[$type]??$classes['info']]) }}><x-icon :name="$icons[$type]??'message'" size="18" class="mt-0.5"/><div class="min-w-0 flex-1">{{ $slot }}</div></div>
