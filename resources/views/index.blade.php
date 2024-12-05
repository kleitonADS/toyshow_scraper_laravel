<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scraper Toys Show</title>
    <style>
        /* Estilo da Barra de Progresso */
        .progress-container {
            width: 200px;
            background-color: #e0e0e0;
            border-radius: 10px;
            height: 20px;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-bar {
            width: 100%;
            height: 100%;
            background-color: #dc2626;
            border-radius: 10px;
            animation: fillProgress 1.5s linear infinite; /* Animação contínua */
        }

        /* Animação contínua de preenchimento da barra */
        @keyframes fillProgress {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%); /* Move a barra de 0% a 100% */
            }
        }
    </style>

    <!-- Estilos do Livewire -->
    @livewireStyles
    <!-- Adicionando Tailwind e customização do tema -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">




    <div class="min-h-screen flex flex-col">
        <!-- Barra de Navegação -->
        <nav class="bg-white text-clack p-4 flex justify-between items-center shadow-md">
            <div class="flex items-center">
                <!-- Logo -->
                <img src="logo.png" alt="Logo" class="w-40 h-12 mr-5">
                <span class="font-bold text-xl">Bem-vindo ao Scraper</span>
            </div>

            <!-- Formulário com o botão de ação dentro da barra de navegação -->
            <form method="GET" action="{{ route('products.index') }}">
                @csrf
                <button type="submit" class="btn bg-transparent text-black p-4 rounded-2xl hover:bg-red-700 hover:text-white transition">Produtos</button>
            </form>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="min-h-80 flex-1 p-6" w-[320px]>
            <!-- Botões de Ação -->
            <div class="flex items-center space-x-32 p-2">
                <!-- Iniciar/Atualizar Scraping -->
                <div class="flex flex-col items-start space-y-2 w-[320px]" >
                    <button id="start-scraping" class="bg-blue-600 text-white p-2 px-8 rounded-3xl hover:bg-blue-700 transition">
                        Iniciar / Update
                    </button>
                    <div id="scraping-status" class="mt-2 text-gray-700"></div>

                    <!-- Barra de Progresso com animação infinita e Formulário de Parar juntos -->
                    <div id="progress-container" class="flex items-center w-full mt-4 hidden">
                        <div class="progress-container w-full">
                            <div id="progress-bar" class="progress-bar h-20"></div>
                        </div>

                        <!-- Formulário de Parar Scraping (só será mostrado quando a barra de progresso aparecer) -->
                        <form id="stop-scraping-form" action="{{ route('stop-scraper') }}" method="POST" class="ml-4 hidden">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white py-1 px-2 text-x rounded hover:bg-red-700 transition">
                                Parar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mensagens de Status -->
            @if(session('message'))
                <p class="mt-4 text-green-500 p-2">{{ session('message') }}</p>
            @elseif(session('error'))
                <p class="mt-4 text-red-500">{{ session('error') }}</p>
            @endif

            <!-- Componente Livewire de Progresso -->
            @livewire('products-progress')
        </main>
    </div>

    <!-- Scripts do Livewire -->
    @livewireScripts
    <script>
    document.getElementById('start-scraping').addEventListener('click', function () {
        const button = this;
        const statusDiv = document.getElementById('scraping-status');
        const progressContainer = document.getElementById('progress-container');
        const stopForm = document.getElementById('stop-scraping-form');

        // Alterar estado inicial
        button.textContent = 'Iniciando...';
        button.disabled = true;
        statusDiv.innerHTML = '<p>Buscando produtos...</p>';

        // Exibir a barra de progresso com animação infinita
        progressContainer.classList.remove('hidden');
        stopForm.classList.remove('hidden'); // Mostrar o formulário de "Parar"

        // Enviar requisição para o servidor
        fetch('{{ route('scraper.start') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(response => response.json())
        .then(data => {
            // Exibir mensagem de sucesso
            statusDiv.innerHTML = `<p class="text-green-500">${data.message}</p>`;

            // Esconder a barra de progresso após o fim do scraping
            progressContainer.classList.add('hidden');
            stopForm.classList.add('hidden');

            // Forçar o reload da página
            setTimeout(function () {
                location.reload();
            }, 2000); // Esperar 2 segundos antes de recarregar
        })
        .catch(error => {
            console.error('Erro:', error);
            statusDiv.innerHTML = '<p class="text-red-500">Erro ao iniciar o scraping.</p>';

            // Esconder a barra de progresso em caso de erro também
            progressContainer.classList.add('hidden');
            stopForm.classList.add('hidden');
        })
        .finally(() => {
            // Restaurar estado do botão
            button.textContent = 'Iniciar / Update';
            button.disabled = false;
        });
    });


    </script>`

</body>
</html>
