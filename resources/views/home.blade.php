<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link rel="stylesheet" href="{{ mix('css/home.css') }}">
</head>

<body>
    <div class="container">
        <!-- Content here -->
        <div id="app"></div>
    </div>
    <script src="{{ mix('js/home.js') }}"></script>
</body>

</html>