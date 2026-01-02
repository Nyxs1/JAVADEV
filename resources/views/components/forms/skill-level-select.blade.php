@props([
    'name' => 'level',
    'selected' => 1,
    'onchange' => null,
])

@php
    use App\Http\Support\Enums\SkillLevel;
@endphp

<select 
    name="{{ $name }}" 
    {{ $onchange ? "onchange={$onchange}" : '' }}
    {{ $attributes->merge(['class' => 'px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500']) }}
>
    @foreach(SkillLevel::options() as $value => $label)
        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>
