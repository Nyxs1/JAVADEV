@props(['tab'])

<div class="text-center py-12">
    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <img src="{{ asset('assets/icons/lock.svg') }}" alt="" class="w-8 h-8 opacity-50">
    </div>
    <h3 class="text-lg font-medium text-slate-900 mb-2">{{ $tab }} is Private</h3>
    <p class="text-slate-600 max-w-sm mx-auto">
        This user has set their {{ strtolower($tab) }} to private.
    </p>
</div>
