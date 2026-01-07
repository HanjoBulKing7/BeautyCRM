gsap.registerPlugin(ScrollTrigger);

window.addEventListener("load", () => {
    const root = document.querySelector(".hero-wrapper-element");
    if (!root) return;

    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: root,
            start: "top top",
            end: "+=100%",
            pin: true,
            scrub: true,
            invalidateOnRefresh: true,
            anticipatePin: 1,
            // markers: true,
        },
    });

    tl.to(".hero-image-container img", {
        scale: 2,
        z: 250,
        transformOrigin: "center center",
    })
        .to(
            ".hero-section.hero-main-section",
            {
                scale: 1.4,
                boxShadow: "10000px 0 0 0 rgba(0,0,0,0.5) inset",
                transformOrigin: "center center",
            },
            "<"
        )
        .to(".hero-image-container", { autoAlpha: 0 })
        .to(".hero-intro", { autoAlpha: 0 }, "<");
});
