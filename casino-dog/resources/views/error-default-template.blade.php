<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 extraitem">
    <div class="flex flex-col items-center justify-center w-screen h-screen">
        <div class="flex flex-col items-center w-full max-w-lg p-8 mx-auto mt-8 bg-white border border-gray-100 shadow-xl rounded-xl">

            <h1 class="text-3xl font-semibold text-blue-600">Error {{ $error['status'] }}</h1>
            <p class="text-lg mt-5 text-gray-500">
                @php
                    if($error['message'] !== NULL) {
                        echo $error['message'];
                    } else {
                        echo 'Oops, something weird happened.';
                    }
                @endphp
            </p>
            <a href="{{ env('APP_URL') ?? '/' }}" class="flex justify-center w-full px-4 py-2 mt-8 text-lg font-medium text-white transition duration-200 ease-in-out bg-blue-600 border border-transparent rounded-md hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-wave active:bg-blue-700">
                Home
            </a>
        </div>
    </div>
</body>
</html>
