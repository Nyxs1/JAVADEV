@props(['events' => null])

@php
    $defaultEvents = [
        ['image' => 'Who We Are.png', 'title' => 'Kelas UI/UX Design', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'profesional.png', 'title' => 'Vibe Code With Framer', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'student.png', 'title' => 'Build Website From Scratch...', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'Who We Are.png', 'title' => 'Build Apps With Kotlin', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'profesional.png', 'title' => 'How To Start Data Analys', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'student.png', 'title' => 'Ethical Hacker : Penetration...', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
    ];
    $eventsData = $events ?? $defaultEvents;
@endphp

{{-- Events Section --}}
<section id="events" class="py-20 bg-white">
    <div class="lp-container">

        {{-- Section Header --}}
        <div class="text-center mb-16 events-header">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6 animate-on-scroll events-title">
                Agenda Acara/Kegiatan
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto animate-on-scroll events-desc">
                Ikuti berbagai event menarik yang kami selenggarakan untuk mengembangkan skill dan networking
            </p>
        </div>

        {{-- Events Cards Grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12 events-cards-grid">
            @foreach ($eventsData as $event)
                <div
                    class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 animate-on-scroll event-card">
                    <div class="h-48 overflow-hidden">
                        <img src="{{ asset('assets/images/photos/' . $event['image']) }}" alt="{{ $event['title'] }}"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-3">{{ $event['title'] }}</h3>

                        {{-- Date and Location --}}
                        <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                            <div class="flex items-center gap-1">
                                <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="Calendar" class="w-4 h-4">
                                <span>{{ $event['date'] }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <img src="{{ asset('assets/icons/location-icon.svg') }}" alt="Location" class="w-4 h-4">
                                <span>{{ $event['location'] }}</span>
                            </div>
                        </div>

                        {{-- Members and Join Button --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    @for ($i = 1; $i <= 3; $i++)
                                        <div
                                            class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                            <span class="text-xs text-gray-500 font-medium">U{{ $i }}</span>
                                        </div>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600">Members Join</span>
                            </div>
                            <button
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                                Join Event
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- CTA Link --}}
        <div class="text-center animate-on-scroll">
            <a href="#"
                class="text-blue-600 font-semibold text-lg hover:underline transition-all duration-300 cursor-pointer">
                Lihat Semua Events
            </a>
        </div>

    </div>
</section>