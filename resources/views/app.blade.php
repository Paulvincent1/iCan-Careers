<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
     <link rel="icon" type="image/png" href="{{ asset('/assets/SHORTS.svg') }}" />
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @inertiaHead
    @routes
  </head>
  <body>
    @inertia
  </body>
</html>
