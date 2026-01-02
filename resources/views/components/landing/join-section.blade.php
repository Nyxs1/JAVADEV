{{-- Join CTA Section --}}
<section id="requirements" class="relative py-20 overflow-hidden">
    {{-- Background Image with Overlay --}}
    <div class="absolute inset-0">
        <img src="{{ asset('assets/images/photos/Who We Are 1.png') }}" alt="Join Us Background"
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/70"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10">
        <div class="lp-container">
            <div class="text-center max-w-4xl mx-auto">

                {{-- Main Title --}}
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 animate-on-scroll">
                    Yuk Gabung Sama Kami
                </h2>

                {{-- Description --}}
                <p class="text-lg md:text-xl text-white/90 mb-4 animate-on-scroll">
                    Komunitas untuk mendukung kamu belajar dan bertumbuh,
                </p>
                <p class="text-lg md:text-xl text-white/90 mb-12 animate-on-scroll">
                    untuk saling support memberi insight satu sama lain.
                </p>

                {{-- CTA Button --}}
                <div class="flex justify-center items-center">
                    <a href="{{ route('register') }}"
                        class="w-48 py-3 bg-blue-600 text-white text-lg font-semibold rounded-xl hover:bg-blue-700 transition-colors animate-on-scroll text-center inline-block">
                        Join Sekarang
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>