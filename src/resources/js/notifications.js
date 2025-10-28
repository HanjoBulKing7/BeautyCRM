// Control del panel de notificaciones
export function initNotifications() {
    const notificationsToggle = document.getElementById('notifications-toggle');
    const notificationsPanel = document.getElementById('notifications-panel');
    const notificationCount = document.getElementById('notification-count');

    if (!notificationsToggle || !notificationsPanel) return;

    // Toggle del panel de notificaciones
    notificationsToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationsPanel.classList.toggle('hidden');
        loadNotifications();
    });

    // Cerrar panel al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!notificationsPanel.contains(e.target) && e.target !== notificationsToggle) {
            notificationsPanel.classList.add('hidden');
        }
    });

    // Cargar notificaciones cada 15 segundos
    setInterval(loadNotifications, 15000);
    
    // Cargar notificaciones al iniciar
    loadNotifications();
}

// Función para cargar notificaciones
function loadNotifications() {
    const notificationsRoute = document.getElementById('notifications-data')?.dataset.route;
    if (!notificationsRoute) return;

    fetch(notificationsRoute)
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.getElementById('notifications-list');
            const notificationCount = document.getElementById('notification-count');
            
            if (!notificationsList) return;

            if (data.success && data.notifications && data.notifications.length > 0) {
                renderNotifications(notificationsList, data.notifications);
                
                // Actualizar contador
                if (notificationCount) {
                    notificationCount.textContent = data.notifications.length;
                    notificationCount.classList.remove('hidden');
                }
                
            } else {
                showNoNotifications(notificationsList, notificationCount);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            showNotificationError();
        });
}

// Renderizar lista de notificaciones
function renderNotifications(container, notifications) {
    container.innerHTML = '';
    
    notifications.forEach(notification => {
        const notificationElement = document.createElement('div');
        notificationElement.className = `p-3 border-b hover:bg-gray-50 dark:hover:bg-gray-700 ${notification.is_recent ? 'bg-yellow-50 dark:bg-yellow-900' : ''}`;
        
        const typeText = notification.record_type_formatted;
        const methodText = notification.method_formatted;
        
        notificationElement.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-1">
                        <p class="font-medium text-gray-900 dark:text-white">${notification.employee_name}</p>
                        <span class="px-2 py-1 text-xs rounded-full ${notification.record_type === 'entrada' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'}">
                            ${typeText}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <i class="fas fa-clock mr-1"></i>${notification.record_time}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-calendar mr-1"></i>${notification.record_date}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <i class="fas fa-map-marker-alt mr-1"></i>${notification.employee_sucursal}
                        <span class="ml-2">
                            <i class="fas fa-fingerprint mr-1"></i>${methodText}
                        </span>
                    </p>
                    ${notification.is_recent ? `
                    <div class="mt-1 flex items-center text-xs text-yellow-600 dark:text-yellow-400">
                        <i class="fas fa-circle mr-1 notification-pulse"></i>
                        Nuevo registro
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        container.appendChild(notificationElement);
    });
    
    // Agregar footer con última actualización
    const footer = document.createElement('div');
    footer.className = 'p-2 border-t text-center text-xs text-gray-500 dark:text-gray-400';
    footer.innerHTML = `
        <i class="fas fa-sync-alt mr-1"></i>
        Actualizado: ${new Date().toLocaleTimeString()}
    `;
    container.appendChild(footer);
}

// Mostrar estado sin notificaciones
function showNoNotifications(container, countElement) {
    container.innerHTML = `
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <i class="fas fa-bell-slash text-2xl mb-2"></i>
            <p>No hay notificaciones recientes</p>
            <p class="text-xs mt-2">Los registros de asistencia aparecerán aquí</p>
        </div>
    `;
    
    if (countElement) {
        countElement.classList.add('hidden');
    }
}

// Mostrar error de carga
function showNotificationError() {
    const container = document.getElementById('notifications-list');
    if (!container) return;
    
    container.innerHTML = `
        <div class="text-center py-4 text-red-500">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Error al cargar notificaciones
        </div>
    `;
}