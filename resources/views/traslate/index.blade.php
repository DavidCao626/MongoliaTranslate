<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-Equiv="Cache-Control" Content="no-cache" />
  <meta http-Equiv="Pragma" Content="no-cache" />
  <meta http-Equiv="Expires" Content="0" />
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
