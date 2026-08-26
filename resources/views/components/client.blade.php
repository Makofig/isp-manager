<html>

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
        rel="stylesheet"
        as="style"
        onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />

    <title>Stitch Design</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>
<body>
    <div class="container max-w-6xl">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Table Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Clients Members</h2>
                        <p class="text-gray-500 mt-1">Manage your clients members and their account permissions here.</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex gap-2">
                        <a href="{{ route('clients.export', 'all') }}" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            Export PDF
                        </a>
                        <a href="{{ route('clients.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            Add Client
                        </a>
                    </div>
                </div>     
            </div>

            <!-- Table -->
            @livewire('clients-table')

        </div>
    </div>
</body>
</html>