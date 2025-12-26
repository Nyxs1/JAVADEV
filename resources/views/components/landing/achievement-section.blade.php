@props(['achievements' => null])

@php
    $defaultAchievements = [
        ['image' => 'Juara.png', 'title' => 'Juara 1 Hackathon 2024', 'desc' => 'Tim JAVADEV berhasil meraih juara pertama dalam kompetisi hackathon tingkat nasional'],
        ['image' => 'Juara.png', 'title' => 'Best Innovation Award', 'desc' => 'Penghargaan inovasi terbaik untuk solusi digital yang berdampak sosial'],
        ['image' => 'Juara.png', 'title' => 'Tech Conference Speaker', 'desc' => 'Anggota komunitas menjadi pembicara di konferensi teknologi internasional'],
        ['image' => 'Juara.png', 'title' => 'Startup Competition Winner', 'desc' => 'Memenangkan kompetisi startup dengan ide bisnis berbasis teknologi'],
        ['image' => 'Juara.png', 'title' => 'Community Impact Award', 'desc' => 'Penghargaan atas kontribusi positif terhadap pengembangan komunitas tech'],
        ['image' => 'Juara.png', 'title' => 'Open Source Contributor', 'desc' => 'Kontribusi aktif dalam proyek open source yang digunakan secara global'],
    ];
    $achievementsData = $achievements ?? $defaultAchievements;
@endphp

{{-- Achievement Section --}}
<section id="achievement" class="py-24 bg-white overflow-hidden">
    {{-- Section Header --}}
    <div class="lp-container">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4 animate-on-scroll">Achievement</h2>
            <p class="text-gray-600 text-lg animate-on-scroll">
                Pencapaian dan prestasi yang telah diraih bersama komunitas
            </p>
        </div>
    </div>

    {{-- Scrolling Gallery --}}
    <div class="achievement-gallery-container">
        <div class="achievement-gallery">
            @foreach ($achievementsData as $achievement)
                <div class="achievement-item">
                    <div class="achievement-image">
                        <img src="{{ asset('assets/images/photos/' . $achievement['image']) }}"
                            alt="{{ $achievement['title'] }}">
                    </div>
                    <div class="achievement-content">
                        <h3 class="achievement-title">{{ $achievement['title'] }}</h3>
                        <p class="achievement-desc">{{ $achievement['desc'] }}</p>
                    </div>
                </div>
            @endforeach

            {{-- Duplicate for seamless loop --}}
            @foreach (array_slice($achievementsData, 0, 3) as $achievement)
                <div class="achievement-item">
                    <div class="achievement-image">
                        <img src="{{ asset('assets/images/photos/' . $achievement['image']) }}"
                            alt="{{ $achievement['title'] }}">
                    </div>
                    <div class="achievement-content">
                        <h3 class="achievement-title">{{ $achievement['title'] }}</h3>
                        <p class="achievement-desc">{{ $achievement['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>