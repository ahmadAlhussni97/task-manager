<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>API Test - Quote Endpoints</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Quote API Test Page</h1>
        
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- API Endpoints Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Available API Endpoints</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center space-x-2">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">GET</span>
                        <code class="bg-gray-100 px-2 py-1 rounded">{{ url('/api/quotes/random') }}</code>
                        <span class="text-gray-600">- Get random quote</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">GET</span>
                        <code class="bg-gray-100 px-2 py-1 rounded">{{ url('/api/quotes/cached') }}</code>
                        <span class="text-gray-600">- Get cached quote</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">POST</span>
                        <code class="bg-gray-100 px-2 py-1 rounded">{{ url('/api/quotes/refresh') }}</code>
                        <span class="text-gray-600">- Refresh quote</span>
                    </div>
                </div>
            </div>

            <!-- Test Buttons -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Test API Calls</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button onclick="testRandomQuote()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                        Test Random Quote
                    </button>
                    <button onclick="testCachedQuote()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">
                        Test Cached Quote
                    </button>
                    <button onclick="testRefreshQuote()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition-colors">
                        Test Refresh Quote
                    </button>
                </div>
            </div>

            <!-- Results Display -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">API Response</h2>
                <div id="apiResult" class="bg-gray-100 p-4 rounded min-h-[200px]">
                    <p class="text-gray-500">Click a button above to test the API endpoints...</p>
                </div>
            </div>

            <!-- External API Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">External API Information</h2>
                <div class="space-y-2 text-sm">
                    <p><strong>API URL:</strong> <code class="bg-gray-100 px-2 py-1 rounded">https://api.quotable.io/random</code></p>
                    <p><strong>Parameters:</strong></p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li><code>tags</code> - Filter by tags (comma-separated)</li>
                        <li><code>maxLength</code> - Maximum quote length</li>
                        <li><code>minLength</code> - Minimum quote length</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function makeApiCall(url, method = 'GET', body = null) {
            const options = {
                method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            };

            if (body) {
                options.body = JSON.stringify(body);
            }

            try {
                const response = await fetch(url, options);
                const data = await response.json();
                
                document.getElementById('apiResult').innerHTML = `
                    <div class="space-y-2">
                        <p><strong>Status:</strong> ${response.status} ${response.statusText}</p>
                        <p><strong>URL:</strong> ${url}</p>
                        <pre class="bg-white p-3 rounded border text-xs overflow-auto">${JSON.stringify(data, null, 2)}</pre>
                    </div>
                `;
            } catch (error) {
                document.getElementById('apiResult').innerHTML = `
                    <div class="text-red-600">
                        <p><strong>Error:</strong> ${error.message}</p>
                    </div>
                `;
            }
        }

        function testRandomQuote() {
            makeApiCall('{{ route("api.quotes.random") }}');
        }

        function testCachedQuote() {
            makeApiCall('{{ route("api.quotes.cached") }}');
        }

        function testRefreshQuote() {
            makeApiCall('{{ route("api.quotes.refresh") }}', 'POST');
        }
    </script>
</body>
</html> 