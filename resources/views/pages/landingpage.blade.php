{{-- ============================= --}}
{{-- HERO SECTION --}}
{{-- ============================= --}}
<section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#2B7FFF]">

    {{-- ============================= --}}
    {{-- BACKGROUND PATTERNS --}}
    {{-- ============================= --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 opacity-60 hero-pattern pattern-container">
            <img src="{{ asset('assets/patterns/Pattern.png') }}" alt="Pattern"
                class="w-full h-full object-cover animate-pattern-sway gpu-accelerated" loading="lazy">
        </div>

        {{-- ============================= --}}
        {{-- PLUS PATTERN DECORATIONS --}}
        {{-- ============================= --}}
        {{-- Plus Pattern - Right Side Only (Left side has your pattern) --}}

        {{-- ============================= --}}
        {{-- RIGHT SIDE - ROW 1 --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 20%; top: 10%;"></div>
        <div class="plus-sign" style="right: 20%; top: 25%;"></div>
        <div class="plus-sign" style="right: 20%; top: 40%;"></div>
        <div class="plus-sign" style="right: 20%; top: 55%;"></div>
        <div class="plus-sign" style="right: 20%; top: 70%;"></div>
        <div class="plus-sign" style="right: 20%; top: 85%;"></div>

        {{-- ============================= --}}
        {{-- RIGHT SIDE - ROW 2 --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 14%; top: 15%;"></div>
        <div class="plus-sign" style="right: 14%; top: 30%;"></div>
        <div class="plus-sign" style="right: 14%; top: 45%;"></div>
        <div class="plus-sign" style="right: 14%; top: 60%;"></div>
        <div class="plus-sign" style="right: 14%; top: 75%;"></div>
        <div class="plus-sign" style="right: 14%; top: 90%;"></div>

        {{-- ============================= --}}
        {{-- RIGHT SIDE - ROW 3 --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 8%; top: 12%;"></div>
        <div class="plus-sign" style="right: 8%; top: 27%;"></div>
        <div class="plus-sign" style="right: 8%; top: 42%;"></div>
        <div class="plus-sign" style="right: 8%; top: 57%;"></div>
        <div class="plus-sign" style="right: 8%; top: 72%;"></div>
        <div class="plus-sign" style="right: 8%; top: 87%;"></div>

        {{-- ============================= --}}
        {{-- RIGHT SIDE - ROW 4 --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 2%; top: 18%;"></div>
        <div class="plus-sign" style="right: 2%; top: 33%;"></div>
        <div class="plus-sign" style="right: 2%; top: 48%;"></div>
        <div class="plus-sign" style="right: 2%; top: 63%;"></div>
        <div class="plus-sign" style="right: 2%; top: 78%;"></div>
        <div class="plus-sign" style="right: 2%; top: 93%;"></div>

        {{-- ============================= --}}
        {{-- RIGHT SIDE - ROW 5 --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 26%; top: 5%;"></div>
        <div class="plus-sign" style="right: 26%; top: 22%;"></div>
        <div class="plus-sign" style="right: 26%; top: 38%;"></div>
        <div class="plus-sign" style="right: 26%; top: 52%;"></div>
        <div class="plus-sign" style="right: 26%; top: 65%;"></div>
        <div class="plus-sign" style="right: 26%; top: 82%;"></div>

        {{-- ============================= --}}
        {{-- RIGHT SIDE - ROW 6 (FAR) --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 32%; top: 8%;"></div>
        <div class="plus-sign" style="right: 32%; top: 28%;"></div>
        <div class="plus-sign" style="right: 32%; top: 45%;"></div>
        <div class="plus-sign" style="right: 32%; top: 68%;"></div>
        <div class="plus-sign" style="right: 32%; top: 85%;"></div>

        {{-- ============================= --}}
        {{-- SCATTERED DECORATIONS --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 38%; top: 18%;"></div>
        <div class="plus-sign" style="right: 38%; top: 35%;"></div>
        <div class="plus-sign" style="right: 38%; top: 58%;"></div>
        <div class="plus-sign" style="right: 38%; top: 78%;"></div>

        {{-- ============================= --}}
        {{-- TOP SCATTERED DECORATIONS --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 35%; top: 12%;"></div>
        <div class="plus-sign" style="right: 45%; top: 6%;"></div>
        <div class="plus-sign" style="right: 50%; top: 15%;"></div>

        {{-- ============================= --}}
        {{-- BOTTOM SCATTERED DECORATIONS --}}
        {{-- ============================= --}}
        <div class="plus-sign" style="right: 55%; top: 88%;"></div>
        <div class="plus-sign" style="right: 35%; top: 95%;"></div>
        <div class="plus-sign" style="right: 48%; top: 92%;"></div>

        {{-- ============================= --}}
        {{-- GRADIENT GLOW EFFECTS --}}
        {{-- ============================= --}}
        <div
            class="absolute top-20 -left-20 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-glow-pulse gpu-accelerated">
        </div>
        <div
            class="absolute bottom-40 -right-20 w-80 h-80 bg-white/5 rounded-full blur-3xl animate-glow-pulse-delay gpu-accelerated">
        </div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-white/3 rounded-full blur-3xl animate-glow-slow gpu-accelerated">
        </div>
    </div>

    {{-- ============================= --}}
    {{-- MAIN HERO CONTENT --}}
    {{-- ============================= --}}
    <div class="relative z-10 w-full py-20">
        <div class="lp-container">

            {{-- ============================= --}}
            {{-- HERO BADGE --}}
            {{-- ============================= --}}
            <div class="text-center mb-12 animate-fade-in">
                <div
                    class="inline-flex items-center gap-3 bg-white/20 backdrop-blur-sm px-8 py-4 rounded-2xl shadow-lg border border-white/30">
                    <img src="{{ asset('assets/icons/terminal-icon.svg') }}" alt="Terminal Icon" class="w-8 h-8">
                    <span class="text-white font-semibold text-lg">Build, Learn, Collaborate, Repeat</span>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- HERO LAYOUT WITH CHARACTERS --}}
            {{-- ============================= --}}
            <div class="relative flex items-center justify-center mb-20">

                {{-- LEFT CHARACTER (FIX: JAUHIN DARI TEKS) --}}
                <div
                    class="absolute -left-24 xl:-left-36 top-[58%] transform -translate-y-1/2 hidden lg:block animate-fade-in-left pointer-events-none">
                    <img src="{{ asset('assets/images/stickers/Memoji Boys 4-16 1.png') }}" alt="Developer"
                        class="w-44 xl:w-56 h-auto drop-shadow-2xl gpu-accelerated" loading="lazy">
                </div>

                {{-- CENTER CONTENT --}}
                <div class="text-center max-w-4xl mx-auto px-4 animate-fade-in-delay hero-content">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                        A Continuous Journey to<br>Create, Learn, and Evolve.
                    </h1>
                    <p class="text-base sm:text-lg lg:text-xl text-white/90 max-w-2xl mx-auto leading-relaxed mb-10">
                        Kami percaya setiap developer hebat lahir dari siklus tak berujung:<br>
                        membangun, belajar, dan berkolaborasi. JAVADEV hadir untuk<br>
                        membentuk generasi pembuat solusi yang siap menghadapi<br>
                        tantangan dunia digital.
                    </p>

                    {{-- CTA BUTTONS --}}
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <button
                            class="w-48 py-3 bg-white text-blue-600 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            Lihat Event
                        </button>
                        <button
                            class="w-48 py-3 border-2 border-white text-white text-lg font-semibold rounded-xl hover:bg-white hover:text-blue-600 transition-all duration-300">
                            Kelas
                        </button>
                    </div>
                </div>

                {{-- RIGHT CHARACTER (FIX: JAUHIN DARI TEKS) --}}
                <div
                    class="absolute -right-24 xl:-right-36 top-[58%] transform -translate-y-1/2 hidden lg:block animate-fade-in-right pointer-events-none">
                    <img src="{{ asset('assets/images/stickers/STICKER.png') }}" alt="Sticker"
                        class="w-44 xl:w-56 h-auto drop-shadow-2xl gpu-accelerated" loading="lazy">
                </div>
            </div>

            {{-- ============================= --}}
            {{-- SCROLL INDICATOR --}}
            {{-- ============================= --}}
            <div class="text-center">
                <button id="scroll-down-btn" onclick="scrollToNextSection()"
                    class="inline-flex flex-col items-center group cursor-pointer hover:scale-110 transition-transform duration-300">
                    <div class="relative">
                        <svg class="w-28 h-28 animate-spin-slow" viewBox="0 0 140 140">
                            <defs>
                                <path id="scrollCircle" d="M 70,70 m -50,0 a 50,50 0 1,1 100,0 a 50,50 0 1,1 -100,0" />
                            </defs>
                            <text class="text-[16px] fill-white font-medium">
                                <textPath href="#scrollCircle" startOffset="0%">
                                    Scroll Ke Bawah
                                </textPath>
                            </text>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img src="{{ asset('assets/icons/arrow-down-icon.svg') }}" alt="Scroll Down"
                                class="w-5 h-5 text-white">
                        </div>
                    </div>
                </button>
            </div>

        </div>
    </div>
</section>

{{-- ============================= --}}
{{-- WHO WE ARE SECTION --}}
{{-- ============================= --}}
<section id="about" class="py-20 bg-white">
    <div class="lp-container">

        {{-- SECTION HEADER --}}
        <div class="mb-12">
            <h2 class="text-4xl font-bold text-gray-800 animate-on-scroll">Who We Are?</h2>
        </div>

        {{-- CONTENT LAYOUT --}}
        <div class="grid lg:grid-cols-2 items-start gap-10 lg:gap-24">
            <div class="animate-on-scroll">
                <img src="{{ asset('assets/images/photos/Who We Are.png') }}" alt="JavaDev Team"
                    class="w-full h-52 object-cover rounded-lg">
            </div>

            <div class="pt-2 animate-on-scroll">
                <p class="text-gray-700 text-base leading-normal">
                    <strong>JAVADEV</strong> merupakan komunitas penggerak inovasi yang
                    menghubungkan mahasiswa dari berbagai bidang untuk
                    bersama-sama menciptakan solusi digital. Kami berfokus pada
                    pengembangan keterampilan teknis, desain, dan kolaboratif agar
                    setiap anggota siap menghadapi tantangan industri masa depan.
                </p>
            </div>
        </div>

    </div>
</section>

{{-- ============================= --}}
{{-- UNTUK SIAPA SECTION --}}
{{-- ============================= --}}
<section id="target" class="py-20 bg-white">
    <div class="lp-container">

        {{-- SECTION HEADER --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4 animate-on-scroll">Untuk Siapa ?</h2>
            <p class="text-gray-600 text-lg animate-on-scroll">
                Untuk kamu yang mau ikut dengan teman semua ada disini
            </p>
        </div>

        {{-- CARDS GRID --}}
        <div class="grid md:grid-cols-2 gap-8">
            {{-- MENTOR CARD --}}
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

            {{-- MAHASISWA CARD --}}
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

{{-- ============================= --}}
{{-- TOOLS & APPS SECTION --}}
{{-- ============================= --}}
<section id="tools" class="tools-section">
    <div class="lp-container">
        <div class="tools-wrap">

            {{-- Section Header --}}
            <div class="tools-header">
                <h2 class="tools-title animate-on-scroll">
                    Tools & Apps Yang Kita Pelajari
                </h2>
                <p class="tools-desc animate-on-scroll">
                    Kami belajar berbagai tools dan aplikasi yang menjadi bagian dari keseharian developer dan kreator
                    masa kini.<br>
                    Dari desain, pengembangan, hingga kolaborasi semua kami pelajari bersama sebagai komunitas yang
                    ingin tumbuh<br>
                    dan beradaptasi dengan dunia digital yang terus berubah.
                </p>
            </div>

            {{-- Tools Grid --}}
            <div class="tools-grid">
                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_css-3.png') }}" alt="CSS">
                    <span>CSS</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_bootstrap.png') }}" alt="Bootstrap">
                    <span>Bootstrap</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_nextjs.png') }}" alt="Next Js">
                    <span>Next Js</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_php.png') }}" alt="PHP">
                    <span>PHP</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_go.png') }}" alt="Golang">
                    <span>Golang</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_javascript.png') }}" alt="Javascript">
                    <span>Javascript</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_figma.png') }}" alt="Figma">
                    <span>Figma</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_nodejs.png') }}" alt="Node Js">
                    <span>Node Js</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_adobe-illustrator.png') }}" alt="Adobe Illustrator">
                    <span>Adobe Illustrator</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_adobe-photoshop.png') }}" alt="Adobe Photoshop">
                    <span>Adobe Photoshop</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_laravel.png') }}" alt="Laravel">
                    <span>Laravel</span>
                </div>

                <div class="tool-item">
                    <img src="{{ asset('assets/tech-logos/logos_java.png') }}" alt="Java">
                    <span>Java</span>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ============================= --}}
{{-- ACHIEVEMENT SECTION --}}
{{-- ============================= --}}
<section id="achievement" class="py-24 bg-white overflow-hidden">
    {{-- SECTION HEADER - CENTERED WITH CONTAINER --}}
    <div class="lp-container">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4 animate-on-scroll">Achievement</h2>
            <p class="text-gray-600 text-lg animate-on-scroll">
                Pencapaian dan prestasi yang telah diraih bersama komunitas
            </p>
        </div>
    </div>

    {{-- SCROLLING GALLERY - FULL WIDTH --}}
    <div class="achievement-gallery-container">
        <div class="achievement-gallery">
            {{-- Achievement Item 1 --}}
            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Juara 1 Hackathon 2024">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Juara 1 Hackathon 2024</h3>
                    <p class="achievement-desc">Tim JAVADEV berhasil meraih juara pertama dalam kompetisi hackathon
                        tingkat nasional</p>
                </div>
            </div>

            {{-- Achievement Item 2 --}}
            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Best Innovation Award">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Best Innovation Award</h3>
                    <p class="achievement-desc">Penghargaan inovasi terbaik untuk solusi digital yang berdampak
                        sosial</p>
                </div>
            </div>

            {{-- Achievement Item 3 --}}
            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Tech Conference Speaker">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Tech Conference Speaker</h3>
                    <p class="achievement-desc">Anggota komunitas menjadi pembicara di konferensi teknologi
                        internasional</p>
                </div>
            </div>

            {{-- Achievement Item 4 --}}
            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Startup Competition Winner">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Startup Competition Winner</h3>
                    <p class="achievement-desc">Memenangkan kompetisi startup dengan ide bisnis berbasis teknologi
                    </p>
                </div>
            </div>

            {{-- Achievement Item 5 --}}
            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Community Impact Award">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Community Impact Award</h3>
                    <p class="achievement-desc">Penghargaan atas kontribusi positif terhadap pengembangan komunitas
                        tech</p>
                </div>
            </div>

            {{-- Achievement Item 6 --}}
            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Open Source Contributor">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Open Source Contributor</h3>
                    <p class="achievement-desc">Kontribusi aktif dalam proyek open source yang digunakan secara
                        global</p>
                </div>
            </div>

            {{-- Duplicate items for seamless loop --}}
            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Juara 1 Hackathon 2024">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Juara 1 Hackathon 2024</h3>
                    <p class="achievement-desc">Tim JAVADEV berhasil meraih juara pertama dalam kompetisi hackathon
                        tingkat nasional</p>
                </div>
            </div>

            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Best Innovation Award">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Best Innovation Award</h3>
                    <p class="achievement-desc">Penghargaan inovasi terbaik untuk solusi digital yang berdampak
                        sosial</p>
                </div>
            </div>

            <div class="achievement-item">
                <div class="achievement-image">
                    <img src="{{ asset('assets/images/photos/Juara.png') }}" alt="Tech Conference Speaker">
                </div>
                <div class="achievement-content">
                    <h3 class="achievement-title">Tech Conference Speaker</h3>
                    <p class="achievement-desc">Anggota komunitas menjadi pembicara di konferensi teknologi
                        internasional</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================= --}}
{{-- PILIH COURSE SECTION --}}
{{-- ============================= --}}
<section id="courses" class="courses-section">
    {{-- PATTERN BACKGROUND --}}
    <div class="courses-pattern" aria-hidden="true">
        <img src="{{ asset('assets/patterns/Pattern-Bot.png') }}" alt="" class="courses-pattern-img">
    </div>

    <div class="courses-inner">
        <div class="courses-head">
            <h2>Pilih Course</h2>
            <p>Jelajahi kelas yang tersedia dan pilihlah yang sejalan dengan tujuanmu</p>
        </div>

        <div class="courses-grid">
            {{-- CARD 1 --}}
            <div class="course-card">
                <div class="course-icon">
                    <img src="{{ asset('assets/icons/project-manager-icon.svg') }}" alt="Project Manager">
                </div>
                <h3>Project Manager</h3>
                <p>Kelola proyek dengan efektif dan efisien</p>
                <a href="#" class="course-btn">Lebih Lanjut</a>
            </div>

            {{-- CARD 2 --}}
            <div class="course-card">
                <div class="course-icon">
                    <img src="{{ asset('assets/icons/vibe-code-icon.svg') }}" alt="Vibe Code">
                </div>
                <h3>Vibe Code</h3>
                <p>Coding dengan vibe yang menyenangkan</p>
                <a href="#" class="course-btn">Lebih Lanjut</a>
            </div>

            {{-- CARD 3 --}}
            <div class="course-card">
                <div class="course-icon">
                    <img src="{{ asset('assets/icons/uiux-design-icon.svg') }}" alt="UI/UX Design">
                </div>
                <h3>UI/UX Design</h3>
                <p>Desain interface yang user-friendly</p>
                <a href="#" class="course-btn">Lebih Lanjut</a>
            </div>

            {{-- CARD 4 --}}
            <div class="course-card">
                <div class="course-icon">
                    <img src="{{ asset('assets/icons/mobile-code-icon.svg') }}" alt="Mobile Code">
                </div>
                <h3>Mobile Code</h3>
                <p>Kembangkan aplikasi mobile native</p>
                <a href="#" class="course-btn">Lebih Lanjut</a>
            </div>
        </div>

        <div class="courses-more">
            <a href="#"
                class="text-blue-600 font-semibold text-lg hover:underline transition-all duration-300 cursor-pointer">Lihat
                Lebih Banyak</a>
        </div>
    </div>
</section>


{{-- ============================= --}}
{{-- EVENTS SECTION --}}
{{-- ============================= --}}
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

            {{-- Event Card 1 --}}
            <div
                class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 animate-on-scroll event-card">
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('assets/images/photos/Who We Are.png') }}" alt="Kelas UI/UX Design"
                        class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Kelas UI/UX Design</h3>

                    {{-- Date and Location --}}
                    <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="Calendar" class="w-4 h-4">
                            <span>12-15 Desember 2025</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/location-icon.svg') }}" alt="Location" class="w-4 h-4">
                            <span>Ruang Lakoda</span>
                        </div>
                    </div>

                    {{-- Members and Join Button --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U1</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U2</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U3</span>
                                </div>
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

            {{-- Event Card 2 --}}
            <div
                class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 animate-on-scroll event-card">
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('assets/images/photos/profesional.png') }}" alt="Vibe Code With Framer"
                        class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Vibe Code With Framer</h3>

                    <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="Calendar" class="w-4 h-4">
                            <span>12-15 Desember 2025</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/location-icon.svg') }}" alt="Location" class="w-4 h-4">
                            <span>Ruang Lakoda</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U1</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U2</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U3</span>
                                </div>
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

            {{-- Event Card 3 --}}
            <div
                class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 animate-on-scroll event-card">
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('assets/images/photos/student.png') }}" alt="Build Website From Scratch"
                        class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Build Website From Scratch...</h3>

                    <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="Calendar" class="w-4 h-4">
                            <span>12-15 Desember 2025</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/location-icon.svg') }}" alt="Location" class="w-4 h-4">
                            <span>Ruang Lakoda</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U1</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U2</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U3</span>
                                </div>
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

            {{-- Event Card 4 --}}
            <div
                class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 animate-on-scroll event-card">
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('assets/images/photos/Who We Are.png') }}" alt="Build Apps With Kotlin"
                        class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Build Apps With Kotlin</h3>

                    <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="Calendar" class="w-4 h-4">
                            <span>12-15 Desember 2025</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/location-icon.svg') }}" alt="Location" class="w-4 h-4">
                            <span>Ruang Lakoda</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U1</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U2</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U3</span>
                                </div>
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

            {{-- Event Card 5 --}}
            <div
                class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 animate-on-scroll event-card">
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('assets/images/photos/profesional.png') }}" alt="How To Start Data Analys"
                        class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">How To Start Data Analys</h3>

                    <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="Calendar" class="w-4 h-4">
                            <span>12-15 Desember 2025</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/location-icon.svg') }}" alt="Location" class="w-4 h-4">
                            <span>Ruang Lakoda</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U1</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U2</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U3</span>
                                </div>
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

            {{-- Event Card 6 --}}
            <div
                class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 animate-on-scroll event-card">
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('assets/images/photos/student.png') }}" alt="Ethical Hacker : Penetration..."
                        class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Ethical Hacker : Penetration...</h3>

                    <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="Calendar" class="w-4 h-4">
                            <span>12-15 Desember 2025</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <img src="{{ asset('assets/icons/location-icon.svg') }}" alt="Location" class="w-4 h-4">
                            <span>Ruang Lakoda</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U1</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U2</span>
                                </div>
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 font-medium">U3</span>
                                </div>
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

{{-- ============================= --}}
{{-- REQUIREMENTS SECTION --}}
{{-- ============================= --}}
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
                        class="w-48 py-3 bg-blue-600 text-white text-lg font-semibold rounded-xl hover:bg-blue-700 transition-colors animate-on-scroll">
                        Join Sekarang
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>