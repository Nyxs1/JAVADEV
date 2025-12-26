@props(['tools' => null])

@php
    $defaultTools = [
        ['logo' => 'logos_css-3.png', 'name' => 'CSS'],
        ['logo' => 'logos_bootstrap.png', 'name' => 'Bootstrap'],
        ['logo' => 'logos_nextjs.png', 'name' => 'Next Js'],
        ['logo' => 'logos_php.png', 'name' => 'PHP'],
        ['logo' => 'logos_go.png', 'name' => 'Golang'],
        ['logo' => 'logos_javascript.png', 'name' => 'Javascript'],
        ['logo' => 'logos_figma.png', 'name' => 'Figma'],
        ['logo' => 'logos_nodejs.png', 'name' => 'Node Js'],
        ['logo' => 'logos_adobe-illustrator.png', 'name' => 'Adobe Illustrator'],
        ['logo' => 'logos_adobe-photoshop.png', 'name' => 'Adobe Photoshop'],
        ['logo' => 'logos_laravel.png', 'name' => 'Laravel'],
        ['logo' => 'logos_java.png', 'name' => 'Java'],
    ];
    $toolsData = $tools ?? $defaultTools;
@endphp

{{-- Tools & Apps Section --}}
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
                @foreach ($toolsData as $tool)
                    <div class="tool-item">
                        <img src="{{ asset('assets/tech-logos/' . $tool['logo']) }}" alt="{{ $tool['name'] }}">
                        <span>{{ $tool['name'] }}</span>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</section>