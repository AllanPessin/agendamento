import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import listPlugin from "@fullcalendar/list";
import timeGridPlugin from "@fullcalendar/timegrid";
import axios from "axios";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap/dist/css/bootstrap.css";
import bootstrap5Plugin from "@fullcalendar/bootstrap5";
import ptbrLocale from "@fullcalendar/core/locales/pt-br";

let calendarEl = document.getElementById("calendar");
let calendar = new Calendar(calendarEl, {
    locale: ptbrLocale,
    plugins: [
        dayGridPlugin,
        timeGridPlugin,
        listPlugin,
        interactionPlugin,
        bootstrap5Plugin,
    ],
    themeSystem: "bootstrap5",
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,listWeek",
    },
    events: function (fetchInfo, successCallback, failureCallback) {
        axios
            .get("/eventos")
            .then((response) => {
                let events = response.data.map((event) => ({
                    id: event.id,
                    title: event.name + " - " + event.address,
                    start: event.init,
                    end: event.end,
                    allDay: false,
                    extendedProps: {
                        id: event.id,
                        name: event.name,
                        address: event.address,
                        email: event.email,
                        phone: event.phone,
                    },
                }));
                successCallback(events);
            })
            .catch((error) => {
                console.error("Erro ao buscar eventos: ", error);
                failureCallback(error);
            });
    },

    dateClick: function (info) {
        let availableHour = [];
        for (let h = 8; h < 18; h++) {
            availableHour.push(`${h.toString().padStart(2, "0")}:00`);
            availableHour.push(`${h.toString().padStart(2, "0")}:30`);
        }

        let modal = document.createElement("div");
        modal.id = "modalSchedule";
        modal.innerHTML = `
        <div id="modalBackdrop" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; 
                z-index: 999;">
            <div class="bg-white p-4 rounded" style="z-index:1000">
                <h3>Agendar visita técnica</h3>
                <label for="hour">Horário</label>
                <select id="hour" class="form-select">
                    ${availableHour
                        .map(
                            (hour) => `<option value="${hour}">${hour}</option>`
                        )
                        .join("")}
                </select>
                <label for="name">Nome:</label>
                <input id="name" type="text" placeholder="Digite seu nome" class="form-control" />
                <label for="address">Endereço:</label>
                <input id="address" type="text" placeholder="Digite seu nome" class="form-control" />
                <label for="phone">Número de telefone:</label>
                <input id="phone" type="text" placeholder="Digite seu telefone" class="form-control" />
                <label for="email">e-mail:</label>
                <input id="email" type="text" placeholder="Digite seu número" class="form-control" />
                <div class="d-flex justify-content-between pt-2">
                    <button id="btnConfirm" class="btn btn-success">Confirmar</button>
                    <button id="btnCancel" class="btn btn-warning">Cancelar</button>
                </div>
            </div>
        </div>
        `;

        document.body.appendChild(modal);

        document
            .getElementById("btnConfirm")
            .addEventListener("click", function () {
                let name = document.getElementById("name").value.trim();
                let address = document.getElementById("address").value.trim();
                let email = document.getElementById("email").value;
                let phone = document.getElementById("phone").value;
                let selectedHour = document.getElementById("hour").value;
                let [hour, minute] = selectedHour.split(":");

                if (!name || !address) {
                    alert("Os campos nome e endereço são obrigatórios!");
                    return;
                }

                let startDate = new Date(info.date);
                startDate.setHours(hour, minute, 0);
                let endDate = new Date(startDate);
                endDate.setHours(startDate.getHours() + 2);

                let events = calendar.getEvents();
                let conflict = events.some((event) => {
                    let startEvent = new Date(event.start);
                    let endEvent = new Date(event.end);
                    return (
                        (startDate >= startEvent && startDate <= endEvent) ||
                        (endDate >= startEvent && startDate <= endEvent) ||
                        (startDate == startEvent && endDate == endEvent)
                    );
                });

                if (conflict) {
                    alert(
                        "Conflito de horário! Já existe uma visita agendada nesse intervalo."
                    );
                    return;
                }
                axios
                    .post(
                        "/schedule",
                        {
                            name: name,
                            address: address,
                            init: startDate,
                            end: endDate.toISOString(),
                            email: email,
                            phone: phone,
                        },
                        {
                            headers: {
                                "Content-Type": "application/json",
                            },
                        }
                    )
                    .then(function () {
                        calendar.addEvent({
                            title: name + " - " + address,
                            start: startDate.toISOString(),
                            end: endDate.toISOString(),
                            allDay: false,
                        });
                        alert("Visita técnica agendada!");
                        location.reload();
                        closeModal();
                        console.log("teste");
                    })
                    .catch((error) => {
                        alert("Erro ao agendar visita.");
                        console.error(error);
                    });
            });

        document
            .getElementById("btnCancel")
            .addEventListener("click", function () {
                closeModal();
            });

        function closeModal() {
            let modal = document.getElementById("modalSchedule");
            if (modal) {
                modal.remove();
            }
        }
    },

    eventClick: function (info) {
        let schedule = info.event;
        let modalDetails = document.createElement("div");
        modalDetails.id = "modalDetails";
        modalDetails.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center;
            z-index: 999;">

             <div style="background: white; padding: 20px; border-radius: 8px; position: relative; min-width: 320px; z-index: 1000;">
                <h3>Detalhes da visita</h3>
                <p><b>Nome: ${schedule.extendedProps.name}</b></p>
                <p><b>Endereço: ${schedule.extendedProps.address}</b></p>
                <p><b>Horario:${schedule.start.toLocaleDateString("pt-BR", {
                    timeZone: "UTC",
                })} - ${schedule.end.toLocaleTimeString("pt-BR", {
            timeZone: "UTC",
        })}</b></p>
                <p><b>E-mail:${schedule.email}</b></p>
                <p><b>Telefone:${schedule.phone}</b></p>
                <div class="d-flex justify-content-between">
                    <button id="btnClose" class="btn btn-warning">Fechar</button>
                    <button id="btnExclude" class="btn btn-danger">Excluir</button>
                </div>
                
            </div>
        </div>
        `;

        document.body.append(modalDetails);

        document
            .getElementById("btnClose")
            .addEventListener("click", function () {
                let modalDetails = document.getElementById("modalDetails");
                if (modalDetails) {
                    modalDetails.remove();
                }
            });

        document
            .getElementById("btnExclude")
            .addEventListener("click", function () {
                let id = schedule.id;
                schedule.remove();
                modalDetails.remove();
                axios.delete(`/schedule/${id}`, {
                    headers: {
                        "Content-Type": "application/json",
                    },
                });
            });
    },
});
calendar.render();
