@props(['practice', 'size' => '24'])
@php $icon = $practice->display_icon; @endphp
@if($icon['type'] === 'svg')
  <img
    src="{{ $icon['value'] }}"
    alt="{{ $practice->name }} icon"
    width="{{ $size }}"
    height="{{ $size }}"
    style="width:{{ $size }}px; height:{{ $size }}px; object-fit:contain; flex-shrink:0;"
    loading="lazy"
  >
@else
  <span style="font-size:{{ $size }}px; line-height:1; flex-shrink:0;">{{ $icon['value'] }}</span>
@endif
