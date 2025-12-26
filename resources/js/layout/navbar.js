const nav = document.getElementById("navbar");
const navLinks = document.querySelectorAll(".nav-link");
const sections = document.querySelectorAll("section[id]");

// shadow saat discroll
if (nav) {
    window.addEventListener("scroll", () => {
        if (window.scrollY > 10) {
            nav.classList.add("shadow-md");
        } else {
            nav.classList.remove("shadow-md");
        }
    });
}

// scrollspy - only if sections exist
if (sections.length > 0) {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    navLinks.forEach((link) =>
                        link.classList.remove("nav-active")
                    );
                    const active = document.querySelector(
                        `.nav-link[href="#${entry.target.id}"]`
                    );
                    if (active) active.classList.add("nav-active");
                }
            });
        },
        { threshold: 0.45 }
    );

    sections.forEach((sec) => observer.observe(sec));
}

// smooth scroll dengan offset tinggi navbar - only for anchor links
if (nav && navLinks.length > 0) {
    navLinks.forEach((link) => {
        link.addEventListener("click", (e) => {
            const href = link.getAttribute("href");

            // Only handle anchor links (starting with # or containing #)
            // Let regular URL routes navigate normally
            if (!href || !href.includes("#")) {
                return; // Allow normal navigation for route URLs
            }

            // Extract the hash part (for URLs like "/path#section")
            const hashIndex = href.indexOf("#");
            const hash = href.substring(hashIndex);

            // If href is just "#something" (local anchor), do smooth scroll
            if (href.startsWith("#")) {
                e.preventDefault();
                const target = document.querySelector(hash);
                if (!target) return;

                const offset = nav.offsetHeight + 8;
                const top = target.offsetTop - offset;

                window.scrollTo({ top, behavior: "smooth" });
            }
            // If href is "url#section", let browser navigate (no preventDefault)
        });
    });
}

// Smooth scroll untuk logo ke home
const homeLink = document.querySelector('a[href="#home"]');
if (homeLink) {
    homeLink.addEventListener("click", (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
}
