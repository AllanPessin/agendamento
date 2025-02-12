<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos</title>
    @csrf
    @vite(['resources/js/calendar.js'])
</head>

<body>

    <h2 style="text-align: center;">Agendamento de Visitas Técnicas</h2>


    <div style="width: 80%; margin: 0 auto">
        <div id="calendar"></div>
    </div>

</body>

</html>
