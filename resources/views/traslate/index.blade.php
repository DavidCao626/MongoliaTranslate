<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>人工付费翻译</title>
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    </head>
    <body>
       <div id="app">
        <traslate></traslate>
       </div>
       <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
