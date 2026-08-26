<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>National Park Academy</title>
    <meta name="description" content="Монгол Улсын Тусгай хамгаалалттай газруудын менежментийг сайжруулах, олон улсын сайн туршлагыг нэвтрүүлэх National Park Academy хөтөлбөр.">
    <link rel="icon" type="image/svg+xml" href="/images/logo.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div id="app"></div>
</body>
</html>
