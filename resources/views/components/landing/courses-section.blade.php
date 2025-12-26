@props(['courses' => null])

@php
    $defaultCourses = [
        ['icon' => 'project-manager-icon.svg', 'title' => 'Project Manager', 'desc' => 'Kelola proyek dengan efektif dan efisien'],
        ['icon' => 'vibe-code-icon.svg', 'title' => 'Vibe Code', 'desc' => 'Coding dengan vibe yang menyenangkan'],
        ['icon' => 'uiux-design-icon.svg', 'title' => 'UI/UX Design', 'desc' => 'Desain interface yang user-friendly'],
        ['icon' => 'mobile-code-icon.svg', 'title' => 'Mobile Code', 'desc' => 'Kembangkan aplikasi mobile native'],
    ];
    $coursesData = $courses ?? $defaultCourses;
@endphp

{{-- Courses Section --}}
<section id="courses" class="courses-section">
    {{-- Pattern Background --}}
    <div class="courses-pattern" aria-hidden="true">
        <img src="{{ asset('assets/patterns/Pattern-Bot.png') }}" alt="" class="courses-pattern-img">
    </div>

    <div class="courses-inner">
        <div class="courses-head">
            <h2>Pilih Course</h2>
            <p>Jelajahi kelas yang tersedia dan pilihlah yang sejalan dengan tujuanmu</p>
        </div>

        <div class="courses-grid">
            @foreach ($coursesData as $course)
                <div class="course-card">
                    <div class="course-icon">
                        <img src="{{ asset('assets/icons/' . $course['icon']) }}" alt="{{ $course['title'] }}">
                    </div>
                    <h3>{{ $course['title'] }}</h3>
                    <p>{{ $course['desc'] }}</p>
                    <a href="#" class="course-btn">Lebih Lanjut</a>
                </div>
            @endforeach
        </div>

        <div class="courses-more">
            <a href="#"
                class="text-blue-600 font-semibold text-lg hover:underline transition-all duration-300 cursor-pointer">
                Lihat Lebih Banyak
            </a>
        </div>
    </div>
</section>