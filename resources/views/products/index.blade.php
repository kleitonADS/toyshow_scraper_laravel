<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - ToyShow Scraper</title>
    @vite('resources/css/app.css')
    <style>

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class=""bg-white text-clack p-4 flex justify-between items-center shadow-md">
    <div class="navbar bg-white text-clack p-4 flex justify-between items-center shadow-md">
        <div class="logo">
            <a href="/" class="text-lg font-bold">ToyShow Scraper</a>
        </div>

        <a href="{{ route('index') }}" class="btn bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
            Scraper
        </a>
    </div>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Produtos Scraped</h1>


        <form method="GET" action="{{ route('products.index') }}" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label for="name" class="block font-bold text-sm">Nome do Produto</label>
                    <input type="text" name="name" id="name" value="{{ request('name') }}"
                        class="w-full p-2 rounded border dark:bg-gray-700 dark:border-gray-600">
                </div>

                <div>
                    <label for="brand" class="block font-bold text-sm">Marca</label>
                    <select name="brand" id="brand"
                        class="w-full p-2 rounded border dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Selecione</option>
                        @foreach($products->pluck('brand')->unique() as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="price_range" class="block font-bold text-sm">Faixa de Preço</label>
                    <select name="price_range" id="price_range"
                        class="w-full p-2 rounded border dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Selecione</option>
                        <option value="under_100" {{ request('price_range') == 'under_100' ? 'selected' : '' }}>Até R$100</option>
                        <option value="100_200" {{ request('price_range') == '100_200' ? 'selected' : '' }}>R$100 - R$200</option>
                        <option value="over_200" {{ request('price_range') == 'over_200' ? 'selected' : '' }}>Mais de R$200</option>
                    </select>
                </div>
            </div>
            <div class="flex space-x-4">

                <button type="submit"
                    class="mt-4 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Filtrar</button>

                <button type="reset"
                    class="mt-4 bg-gray-400 text-white py-2 px-4 rounded hover:bg-gray-500">Limpar</button>
            </div>
        </form>


        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-4">
            @foreach($products as $product)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow cursor-pointer" onclick="openModal('{{ $product->image_src }}', '{{ $product->name }}', '{{ $product->description }}')">

                    <img src="{{ $product->image_src }}" alt="{{ $product->image_alt }}"
                        class="w-full h-48 object-contain rounded">
                    <h2 class="font-bold text-lg mt-2">{{ $product->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->brand }}</p>
                    <p class="text-blue-600 font-bold mt-2">{{ number_format($product->price_off, 2, ',', '.') }}</p>
                    @if($product->price)
                        <p class="text-red-500 line-through text-sm">{{ number_format($product->price, 2, ',', '.') }}</p>
                    @endif
                    <p class="text-sm mt-2">{{ Str::limit($product->description, 50) }}</p>
                </div>
            @endforeach
        </div>


        <div class="mt-6 flex justify-between items-center">

            <div class="text-sm text-gray-700 dark:text-gray-300">
                Exibindo {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} produtos
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalImage" src="" alt="" class="w-full h-64 object-contain mb-4">
            <h2 id="modalTitle" class="text-xl font-bold mb-4"></h2>
            <p id="modalDescription" class="text-sm"></p>
        </div>
    </div>

    <script>

        function openModal(imageSrc, title, description) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalDescription').innerText = description;
            document.getElementById('productModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('productModal').style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById('productModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
