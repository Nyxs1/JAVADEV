// Performance optimization variables
let isReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)"
).matches;
let isLowPerformance = false;

// Detect low performance devices
function detectPerformance() {
    const start = performance.now();
    requestAnimationFrame(() => {
        const delta = performance.now() - start;
        if (delta > 16.67) {
            // More than 60fps threshold
            isLowPerformance = true;
            optimizeForLowPerformance();
        }
    });
}

// Optimize for low performance devices - Less aggressive
function optimizeForLowPerformance() {
    console.log("Low performance detected, optimizing animations...");

    // Reduce animation complexity but keep some plus signs
    const plusSigns = document.querySelectorAll(".plus-sign");
    plusSigns.forEach((sign, index) => {
        if (index % 3 === 0) {
            sign.style.display = "none"; // Hide every third plus sign instead
        }
    });

    // Slow down glow effects instead of removing
    const glowEffects = document.querySelectorAll('[class*="animate-glow"]');
    glowEffects.forEach((effect) => {
        effect.style.animationDuration = "15s"; // Slower but still visible
    });

    // Slow down pattern sway instead of disabling
    const patterns = document.querySelectorAll(".animate-pattern-sway");
    patterns.forEach((pattern) => {
        pattern.style.animationDuration = "12s"; // Slower pattern
    });
}

// Smart hero performance observer - prevents lag when scrolling back up
function setupPerformanceObserver() {
    const heroSection = document.getElementById("home");

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.target.id === "home") {
                    // Use smart hero animation management
                    manageHeroAnimations(entry.isIntersecting);
                }
            });
        },
        {
            threshold: 0.2, // Trigger when 20% visible for smoother experience
            rootMargin: "50px 0px", // Start loading before fully visible
        }
    );

    if (heroSection) {
        observer.observe(heroSection);
    }
}

// Smooth scroll to next section
function scrollToNextSection() {
    console.log("🚀 Scroll button clicked!");

    const aboutSection = document.getElementById("about");
    if (aboutSection) {
        const navbar = document.getElementById("navbar");
        const navbarHeight = navbar ? navbar.offsetHeight : 64;
        const elementPosition = aboutSection.offsetTop;
        const offsetPosition = elementPosition - navbarHeight - 20;

        window.scrollTo({
            top: offsetPosition,
            behavior: "smooth",
        });

        console.log("✅ Scrolling to Who We Are section");
    }
}

// Smooth scroll hijacking for mouse wheel
let isWheelScrolling = false;
let wheelScrollTimeout;

function smoothScrollHandler(e) {
    if (isWheelScrolling) return;

    e.preventDefault();

    const delta = e.deltaY;
    const scrollAmount = delta > 0 ? 100 : -100; // Scroll amount per wheel event
    const currentScroll = window.pageYOffset;
    const targetScroll = currentScroll + scrollAmount;

    isWheelScrolling = true;

    window.scrollTo({
        top: Math.max(0, targetScroll),
        behavior: "smooth",
    });

    // Reset scrolling flag after animation
    clearTimeout(wheelScrollTimeout);
    wheelScrollTimeout = setTimeout(() => {
        isWheelScrolling = false;
    }, 50);
}

// Section animation on scroll
// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function setupSectionAnimations() {
    const sections = document.querySelectorAll("section");

    const observerOptions = {
        threshold: 0.1, // Balanced threshold for smooth triggering
        rootMargin: "0px 0px -80px 0px", // Balanced margin for natural feel
    };

    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            const section = entry.target;

            // Skip hero section - it has its own animations
            if (section.id === "home") return;

            if (entry.isIntersecting) {
                // Smooth animate in - no aggressive optimizations
                section.classList.add("section-visible");
                section.classList.remove("section-hidden");

                // Animate children with slower stagger
                const children = section.querySelectorAll(".animate-on-scroll");

                // Special handling for course and events sections - no stagger delay
                if (section.id === "courses" || section.id === "events") {
                    children.forEach((child) => {
                        // Prevent multiple triggers
                        if (!child.classList.contains("animate-in")) {
                            child.style.transitionDelay = "0ms"; // All appear together
                            child.classList.add("animate-in");
                            child.classList.remove("animate-out");
                        }
                    });

                    // FORCE START COURSES PATTERN ANIMATION
                    if (section.id === "courses") {
                        const patternImg = section.querySelector(
                            ".courses-pattern-img"
                        );
                        if (patternImg) {
                            // Force restart animation with multiple attempts
                            patternImg.style.animation = "none";
                            patternImg.style.animationPlayState = "paused";
                            patternImg.offsetHeight; // Trigger reflow

                            // Apply simple animation
                            patternImg.style.animation =
                                "simpleFloat 4s ease-in-out infinite";
                            patternImg.style.animationPlayState = "running";

                            console.log(
                                "🎨 Courses pattern animation FORCE restarted with multiple methods"
                            );
                        }
                    }
                }
                // Special handling for tools section - ELEGANT ANIMATION
                else if (section.id === "tools") {
                    console.log(
                        "🛠️ Tools section visible - triggering elegant animations"
                    );

                    // Simple and elegant tool items animation
                    const toolItems = section.querySelectorAll(".tool-item");
                    toolItems.forEach((tool, index) => {
                        if (!tool.classList.contains("animate-in")) {
                            // Add classes for animation
                            tool.classList.add("animate-on-scroll");

                            // Trigger animation with elegant stagger
                            setTimeout(() => {
                                tool.classList.add("animate-in");
                                tool.classList.remove("animate-out");
                                console.log(
                                    `✨ Tool item ${index + 1} animated: ${
                                        tool.querySelector("span")?.textContent
                                    }`
                                );
                            }, index * 100); // 100ms stagger - smooth and elegant
                        }
                    });

                    // Also animate header elements
                    children.forEach((child, index) => {
                        if (!child.classList.contains("animate-in")) {
                            child.style.transitionDelay = `${index * 150}ms`;
                            child.classList.add("animate-in");
                            child.classList.remove("animate-out");
                        }
                    });
                }
                // Special enhanced animations for Requirements section
                else if (section.id === "requirements") {
                    children.forEach((child, index) => {
                        // Prevent multiple triggers
                        if (!child.classList.contains("animate-in")) {
                            // Staggered delay for dramatic effect
                            child.style.transitionDelay = `${index * 200}ms`;
                            child.classList.add("animate-in");
                            child.classList.remove("animate-out");
                        }
                    });

                    // Add special class to section for enhanced effects
                    section.classList.add("requirements-active");
                } else {
                    // Normal stagger for other sections - SMOOTH
                    children.forEach((child, index) => {
                        // Prevent multiple triggers
                        if (!child.classList.contains("animate-in")) {
                            child.style.transitionDelay = `${index * 150}ms`; // Smoother stagger
                            child.classList.add("animate-in");
                            child.classList.remove("animate-out");
                        }
                    });
                }
                console.log(`✨ Section ${section.id} animated in`);
            } else {
                // ENHANCED animate out with special tools handling
                section.classList.add("section-hidden");
                section.classList.remove("section-visible");

                // Special handling for tools section - ELEGANT EXIT
                if (section.id === "tools") {
                    console.log(
                        "🛠️ Tools section leaving - elegant exit animations"
                    );

                    const toolItems = section.querySelectorAll(".tool-item");
                    toolItems.forEach((tool, index) => {
                        // Simple elegant exit animation
                        setTimeout(() => {
                            tool.classList.add("animate-out");
                            tool.classList.remove("animate-in");
                            console.log(
                                `🌙 Tool item ${index + 1} exit: ${
                                    tool.querySelector("span")?.textContent
                                }`
                            );
                        }, index * 50); // 50ms stagger for quick elegant exit
                    });
                } else {
                    // Normal exit for other sections
                    const children =
                        section.querySelectorAll(".animate-on-scroll");
                    children.forEach((child) => {
                        child.style.transitionDelay = "0ms"; // No delay for out animation
                        child.classList.add("animate-out");
                        child.classList.remove("animate-in");
                    });
                }

                console.log(`🌙 Section ${section.id} animated out`);
            }
        });
    }, observerOptions);

    sections.forEach((section) => {
        // Skip hero section - it has its own animations
        if (section.id === "home") return;

        sectionObserver.observe(section);

        // Add initial classes
        section.classList.add("section-animated");

        // Mark children for animation
        const animatableElements = section.querySelectorAll(
            "h1, h2, h3, p, img, .card"
        );
        animatableElements.forEach((el) => {
            el.classList.add("animate-on-scroll");
        });

        // Special handling for tools section - FIXED
        if (section.id === "tools") {
            // Add animate-on-scroll to all tool items with proper selectors
            const toolItems = section.querySelectorAll(".tool-item");
            toolItems.forEach((tool) => {
                tool.classList.add("animate-on-scroll");
                console.log("Added animate-on-scroll to tool item:", tool);
            });

            // Also try alternative selectors in case the above doesn't work
            const alternativeToolItems = section.querySelectorAll(
                '.tools-grid > div, [class*="tool-item"]'
            );
            alternativeToolItems.forEach((tool) => {
                tool.classList.add("animate-on-scroll");
                console.log(
                    "Added animate-on-scroll to alternative tool item:",
                    tool
                );
            });
        }
    });
}

// Make function globally available
window.scrollToNextSection = scrollToNextSection;

// Debounced resize handler
let resizeTimeout;
function handleResize() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        if (window.innerWidth < 480) {
            optimizeForLowPerformance();
        }
    }, 250);
}

// Setup card hover effects
function setupCardHoverEffects() {
    const cards = document.querySelectorAll(".card-hover-container");

    cards.forEach((card) => {
        card.addEventListener("mouseenter", () => {
            console.log("🎯 Card hover started");
            card.classList.add("card-hovered");
        });

        card.addEventListener("mouseleave", () => {
            console.log("👋 Card hover ended");
            card.classList.remove("card-hovered");
        });

        // Add click effect
        card.addEventListener("click", () => {
            console.log("🔥 Card clicked");
            card.classList.add("card-clicked");
            setTimeout(() => {
                card.classList.remove("card-clicked");
            }, 200);
        });
    });
}

// ULTRA GENTLE scroll performance optimization - ZERO LAG
let isScrolling = false;
let scrollTimer = null;

function handleScrollPerformance() {
    // Minimal optimization - just track scrolling state
    isScrolling = true;

    // Very light class for minimal CSS adjustments
    document.body.classList.add("gentle-scrolling");

    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(() => {
        isScrolling = false;
        document.body.classList.remove("gentle-scrolling");
    }, 50); // Ultra quick recovery for instant responsiveness
}

// SMART hero animation - KEEP ANIMATIONS BUT FIX LAG
let heroAnimationsInitialized = false;
let isScrollingToHero = false;

function initializeHeroAnimations() {
    const heroSection = document.getElementById("home");
    if (heroSection && !heroAnimationsInitialized) {
        heroAnimationsInitialized = true;

        // Progressive loading - keep animations but optimize
        heroSection.classList.add("hero-loaded");

        console.log(
            "✨ Hero loaded with beautiful animations (optimized for performance)"
        );
    }
}

// SMART hero visibility management - PREVENT LAG ON SCROLL BACK
function manageHeroAnimations(isVisible) {
    const heroSection = document.getElementById("home");
    if (!heroSection) return;

    if (isVisible) {
        // Detect if user is scrolling back up (potential lag scenario)
        const scrollDirection = window.pageYOffset < 100 ? "up" : "down";

        if (scrollDirection === "up" && window.pageYOffset > 0) {
            // User scrolling back up - use instant mode to prevent lag
            isScrollingToHero = true;
            heroSection.classList.add("hero-loaded", "hero-instant-mode");

            // Temporarily reduce animations during scroll back
            setTimeout(() => {
                isScrollingToHero = false;
                heroSection.classList.remove("hero-instant-mode");
            }, 500); // Quick recovery

            console.log("Hero section visible - instant mode (scroll back up)");
        } else {
            // Normal loading - full animations
            heroSection.classList.add("hero-loaded");
            console.log("Hero section visible - full animations");
        }
    } else {
        // When leaving hero, prepare for potential scroll back
        isScrollingToHero = false;
        heroSection.classList.remove("hero-instant-mode");
    }
}

// Initialize all features
document.addEventListener("DOMContentLoaded", () => {
    // Check for reduced motion preference
    if (isReducedMotion) {
        document.body.classList.add("reduce-motion");
        return; // Skip animations if reduced motion is preferred
    }

    // Setup gentle scroll performance optimization
    window.addEventListener("scroll", debounce(handleScrollPerformance, 50), {
        passive: true,
    });

    // Viewport-based optimization
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("in-viewport");
                } else {
                    entry.target.classList.remove("in-viewport");
                }
            });
        },
        { threshold: 0.1 }
    );

    // Observe all sections for viewport optimization
    document.querySelectorAll("section").forEach((section) => {
        observer.observe(section);
    });

    console.log("✅ Advanced scroll performance optimization enabled");

    // Setup scroll button
    const scrollButton = document.getElementById("scroll-down-btn");
    if (scrollButton) {
        scrollButton.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            scrollToNextSection();
        });
        console.log("✅ Scroll button ready");
    }

    // Setup section animations
    setupSectionAnimations();
    console.log("✅ Section animations ready");

    // Setup card hover effects
    setupCardHoverEffects();
    console.log("✅ Card hover effects ready");

    // Performance optimizations
    setTimeout(detectPerformance, 1000);
    setupPerformanceObserver();
    window.addEventListener("resize", handleResize);

    if (window.innerWidth < 480) {
        optimizeForLowPerformance();
    }

    // Initialize hero animations immediately but progressively
    initializeHeroAnimations();

    // FORCE COURSES ANIMATION ON PAGE LOAD
    setTimeout(() => {
        const coursesPatternImg = document.querySelector(
            ".courses-pattern-img"
        );
        if (coursesPatternImg) {
            console.log("🎨 Found courses pattern image, forcing animation...");
            console.log("📍 Image element:", coursesPatternImg);
            console.log("🖼️ Image src:", coursesPatternImg.src);
            console.log("📏 Image dimensions:", {
                width: coursesPatternImg.offsetWidth,
                height: coursesPatternImg.offsetHeight,
                naturalWidth: coursesPatternImg.naturalWidth,
                naturalHeight: coursesPatternImg.naturalHeight,
            });

            // Simple force method
            coursesPatternImg.style.animation =
                "simpleFloat 4s ease-in-out infinite";
            coursesPatternImg.style.animationPlayState = "running";

            // Check if image loaded
            if (coursesPatternImg.complete) {
                console.log("✅ Image loaded successfully");
            } else {
                console.log("⏳ Image still loading...");
                coursesPatternImg.onload = () => {
                    console.log("✅ Image loaded after delay");
                };
                coursesPatternImg.onerror = () => {
                    console.log("❌ Image failed to load!");
                };
            }

            // Log current styles for debugging
            console.log("🔍 Pattern image styles:", {
                animation: coursesPatternImg.style.animation,
                animationPlayState: coursesPatternImg.style.animationPlayState,
                src: coursesPatternImg.src,
            });
        } else {
            console.log("❌ Courses pattern image not found!");

            // Check if courses section exists
            const coursesSection = document.querySelector("#courses");
            if (coursesSection) {
                console.log("✅ Courses section found:", coursesSection);
                const patternDiv =
                    coursesSection.querySelector(".courses-pattern");
                if (patternDiv) {
                    console.log("✅ Pattern div found:", patternDiv);
                } else {
                    console.log("❌ Pattern div not found!");
                }
            } else {
                console.log("❌ Courses section not found!");
            }
        }
    }, 2000);

    console.log("🚀 Landing page fully initialized");
});
