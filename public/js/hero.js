gsap.registerPlugin(ScrollTrigger);

window.addEventListener("load", () => {
    gsap
    .timeline({
        scrollTrigger: {
            trigger: ".hero-wrapper-element",
            start: "top top",
            end: "+=100%",
            pin: true,
            scrub: true,
            //markers: true,
        },
    })
    .to(".hero-image-container img", {
        scale: 2,
        z: 250,
        transformOrigin: "center center",
    })
    .to(
        ".hero-main-section",
    {
        scale: 1.4,
        boxShadow: '10000px 0 0 0 rgba(0,0,0,0.5) inset',
        transformOrigin: "center center",
    },
    "<"
    )
    .to(".hero-image-container",{
        autoAlpha: 0,
    })
    .to([".hero-main-section", ".hero-intro"], {
        height: 400,
    });
});