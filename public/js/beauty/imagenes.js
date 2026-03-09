document.addEventListener('DOMContentLoaded', function() {
    const galleryWrapper = document.querySelector('.gallery-wrapper');
    const cards = document.querySelectorAll('.gallery-card');
    
    // Solo crear dots si estamos en móvil/tablet y hay cards
    if (galleryWrapper && cards.length > 0 && window.innerWidth <= 1024) {
        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'gallery-dots';
        
        cards.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.className = 'gallery-dot' + (index === 0 ? ' active' : '');
            dot.setAttribute('aria-label', `Ir a imagen ${index + 1}`);
            dot.addEventListener('click', () => {
                cards[index].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            });
            dotsContainer.appendChild(dot);
        });
        
        galleryWrapper.parentNode.insertBefore(dotsContainer, galleryWrapper.nextSibling);
        
        const updateActiveDot = () => {
            const scrollPosition = galleryWrapper.scrollLeft;
            const cardWidth = cards[0].offsetWidth + parseInt(getComputedStyle(galleryWrapper).gap);
            
            cards.forEach((_, index) => {
                const cardLeft = cards[index].offsetLeft - galleryWrapper.offsetLeft;
                if (scrollPosition >= cardLeft - 50 && scrollPosition < cardLeft + cardWidth - 50) {
                    document.querySelectorAll('.gallery-dot').forEach((dot, i) => {
                        dot.classList.toggle('active', i === index);
                    });
                }
            });
        };
        
        galleryWrapper.addEventListener('scroll', updateActiveDot);
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 1024) {
                updateActiveDot();
            }
        });
    }
});