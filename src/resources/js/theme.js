// resources/js/theme.js
export function initTheme() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

    if (!themeToggle || !themeIcon) {
        console.log('Elementos del tema no encontrados');
        return;
    }

    // Verificar preferencia guardada
    const savedTheme = localStorage.getItem('theme') || 'light';
    const body = document.body;
    
    console.log('Tema guardado:', savedTheme);
    
    // Aplicar tema inicial
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        console.log('Tema oscuro aplicado');
    } else {
        body.classList.remove('dark-mode');
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        console.log('Tema claro aplicado');
    }

    // Configurar evento del botón
    themeToggle.addEventListener('click', function() {
        const isCurrentlyDark = body.classList.contains('dark-mode');
        console.log('Cambiando tema. Actualmente oscuro:', isCurrentlyDark);
        
        if (isCurrentlyDark) {
            // Cambiar a claro
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            console.log('Cambiado a tema claro');
        } else {
            // Cambiar a oscuro
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            console.log('Cambiado a tema oscuro');
        }
    });
}