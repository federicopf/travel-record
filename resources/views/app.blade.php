<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="/assets/icon/Icon/Color/Travel%20Record%20Icon%20(Color).svg">
    <title>Travel Record</title>
    @vite('resources/js/app.js')
    @routes
</head>
<body>
    @inertia
</body>
</html>

<script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

</script>
