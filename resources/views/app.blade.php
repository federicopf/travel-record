<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Travel record</title>
    @vite('resources/js/app.js')
    @routes
</head>
<body>
    @inertia
</body>
</html>

<script>
    window.addEventListener("pageshow", function (event) {
        const TIMEOUT = 30 * 60 * 1000; // 30 minuti
        const now = Date.now();
        const lastVisit = sessionStorage.getItem("lastVisit");

        if (event.persisted || (lastVisit && now - lastVisit > TIMEOUT)) {
            window.location.reload(true); // Forza un reload totale
        }

        sessionStorage.setItem("lastVisit", now);
    });

</script>
