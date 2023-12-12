<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="icon" id="favicon" href="/favicon.ico" type="image/gif">
    <title>Social network</title>
</head>
<body>
    <div class="p-4" id="app">
        @yield('content')
    </div>
</body>
</html>