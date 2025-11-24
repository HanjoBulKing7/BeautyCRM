<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Integration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Google Calendar Integration</h1>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-blue-700">
                    🎉 ¡Bienvenido a la integración con Google Calendar!
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Card para conectar con Google -->
                <div class="border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Conectar con Google</h2>
                    <p class="text-gray-600 mb-4">Conecta tu cuenta de Google para sincronizar el calendario.</p>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Conectar Google Calendar
                    </button>
                </div>

                <!-- Card para ver eventos -->
                <div class="border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Ver Eventos</h2>
                    <p class="text-gray-600 mb-4">Visualiza y gestiona tus eventos del calendario.</p>
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        Ver Mis Eventos
                    </button>
                </div>
            </div>

            <!-- Placeholder para el calendario -->
            <div class="mt-8">
                <h2 class="text-2xl font-semibold mb-4">Calendario</h2>
                <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <p class="text-gray-500">Aquí se mostrará el calendario de Google</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>