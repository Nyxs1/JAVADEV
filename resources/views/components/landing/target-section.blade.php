{{-- Untuk Siapa Section --}}
<section id="target" class="py-20 bg-white">
    <div class="lp-container">

        {{-- Section Header --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4 animate-on-scroll">Untuk Siapa ?</h2>
            <p class="text-gray-600 text-lg animate-on-scroll">
                Untuk kamu yang mau ikut dengan teman semua ada disini
            </p>
        </div>

        {{-- Cards Grid --}}
        <div class="grid md:grid-cols-2 gap-8">
            {{-- Mentor Card --}}
            <div class="relative group cursor-pointer animate-on-scroll card-hover-container">
                <div class="relative overflow-hidden shadow-lg card-inner">
                    <img src="{{ asset('assets/images/photos/profesional.png') }}" alt="Mentor"
                        class="w-full h-80 object-cover card-image">

                    <div class="card-badge">
                        <div class="badge-inner">
                            <img src="{{ asset('assets/images/stickers/Professional 1.png') }}" alt="Mentor Icon"
                                class="w-8 h-8 rounded-full">
                            <span>Mentor</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mahasiswa Card --}}
            <div class="relative group cursor-pointer animate-on-scroll card-hover-container">
                <div class="relative overflow-hidden shadow-lg card-inner">
                    <img src="{{ asset('assets/images/photos/student.png') }}" alt="Mahasiswa"
                        class="w-full h-80 object-cover card-image">

                    <div class="card-badge">
                        <div class="badge-inner">
                            <img src="{{ asset('assets/images/stickers/Freshgraduate 1.png') }}" alt="Mahasiswa Icon"
                                class="w-8 h-8 rounded-full">
                            <span>Mahasiswa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>