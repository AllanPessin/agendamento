<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #calendar {
            max-width: 1100px;
            margin: 40px auto;
        }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Agendamento de Visitas Técnicas</h2>

    <div id="calendar"></div>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    start: 'dayGridMonth timeGridWeek timeGridDay',
                    center: 'title',
                    end: 'today,prev,next'
                },
                locale: 'pt-br',
                // initialView: 'dayGridMonth',
                // selectable: true,
                // editable: true,
                // eventSources: [
                //     {
                //         url: "{{ route('schedule.listar') }}",
                //         method: 'GET',
                //         failure: function () {
                //             alert('Erro ao carregar eventos');
                //         }
                //     }
                // ],
                // dateClick: function (info) {
                //     let horario = prompt("Informe o horário (Formato: HH:mm)");
                //     if (horario) {
                //         let dataHora = info.dateStr + ' ' + horario + ':00';

                //         $.ajax({
                //             url: "{{ route('schedule.store') }}",
                //             method: "POST",
                //             data: {
                //                 _token: "{{ csrf_token() }}",
                //                 nome_cliente: "Cliente Exemplo",
                //                 endereco: "Rua Exemplo, 123",
                //                 horario: dataHora
                //             },
                //             success: function (response) {
                //                 alert(response.message);
                //                 calendar.refetchEvents();
                //             },
                //             error: function (xhr) {
                //                 alert(xhr.responseJSON.error);
                //             }
                //         });
                //     }
                // }
            });

            calendar.render();
        });
    </script> --}}

</body>
</html>
