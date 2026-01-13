// Make ScrollTrigger available for use in GSAP animations
gsap.registerPlugin(ScrollTrigger);

// Select the HTML elements needed for the animation
const scrollSection = document.querySelectorAll(".horizontal-scroll-section");

scrollSection.forEach((section) => {
    const wrapper = section.querySelector(".horizontal-wrapper");
    const items = wrapper.querySelectorAll(".horizontal-item");

    // Initialize
    let direction = null;

    if (section.classList.contains("horizontal-vertical-section")) {
        direction = "vertical";
    } else if (section.classList.contains("horizontal-horizontal-section")) {
        direction = "horizontal";
    }

    initScroll(section, items, direction);
});

function initScroll(section, items, direction) {
    // Initial states - hide all items except the first one
    items.forEach((item, index) => {
        if (index !== 0) {
            direction == "horizontal"
                ? gsap.set(item, { xPercent: 100 })
                : gsap.set(item, { yPercent: 100 });
        }
    });

    // Create a timeline with ScrollTrigger
    const timeline = gsap.timeline({
        scrollTrigger: {
            trigger: section,
            pin: true,
            start: "top top",
            end: () => `+=${items.length * 100}%`,
            scrub: 1,
            invalidateOnRefresh: true,
            // markers: true, // Uncomment for debugging
        },
        defaults: { ease: "none" },
    });

    // Animate each item
    items.forEach((item, index) => {
        // Add scale and border-radius animation to current item
        timeline.to(item, {
            scale: 0.9,
            borderRadius: "10px",
            duration: 0.5,
        });

        // Show next item (if exists)
        if (items[index + 1]) {
            direction == "horizontal"
                ? timeline.to(
                      items[index + 1],
                      {
                          xPercent: 0,
                          duration: 0.5,
                      },
                      "<" // Start at the same time as the previous animation
                  )
                : timeline.to(
                      items[index + 1],
                      {
                          yPercent: 0,
                          duration: 0.5,
                      },
                      "<" // Start at the same time as the previous animation
                  );
        }
    });

    // Optional: Add cleanup for smooth transitions
    ScrollTrigger.addEventListener("refresh", () => {
        // Ensure items are in correct position after refresh
        items.forEach((item, index) => {
            if (index !== 0) {
                direction == "horizontal"
                    ? gsap.set(item, { xPercent: 100 })
                    : gsap.set(item, { yPercent: 100 });
            }
        });
    });
}

// Optional: Add resize handler for better responsiveness
window.addEventListener("resize", () => {
    ScrollTrigger.refresh();
});

// Optional: Initialize GSAP with default settings
gsap.config({
    nullTargetWarn: false,
});

console.log("Horizontal Scroll Component initialized successfully!");
