// import './bootstrap.js';


window.app = {};
window.app.planeRegistration = '';
window.app.reservationDate = '';

app.loadPlanes = function(planeSelectField){
    $.ajax({
        type: "GET",
        url: "/api/plane",
        headers: {"Authorization": 'Bearer ' + sessionStorage.getItem('token')},
        dataType: 'json'
    }).success(function(data){
        let planes = data;
        planes.forEach(function(plane){
            planeSelectField.append(`<option value="${plane.id}">${plane.registration}</option>`);
        });
    }).fail(function(data){
        alert(data.responseJSON.message);
    });
};
 
app.loadDailyPlaneReservations = function(planeRegistration, date, dailyReservationsView){
    $.ajax({
        type: "GET",
        url: "/api/plane/" + planeRegistration + "/reservation/" + date,
        headers: {"Authorization": 'Bearer ' + sessionStorage.getItem('token')},
        dataType: 'json'
    }).success(function(data){
        let dailyReservations = data;
        dailyReservationsView.html('');
        let notConfirmedIcon = '<i class="bi bi-question-circle" style="color: var(--bs-danger);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja niepotwierdzona"></i>';
        let confirmedIcon = '<i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona">';
        
        dailyReservations.forEach(function(item){
            let isConfirmed = item.is_confirmed == true ? confirmedIcon : notConfirmedIcon;
            let canConfirm = item.can_confirm == true
                ? `<button type="button" class="btn btn-primary" data-id="${item.id}">Potwierdź rezerwację</button>`
                : '';
            let canRemove = item.can_remove == true
                ? `<button type="button" class="btn btn-danger" data-id="${item.id}">Usuń</button>`
                : '';
            let row = `<tr>
                    <th>${isConfirmed}</th>
                    <th scope="row">${item.starts_at} - ${item.ends_at}</th>
                    <td>${item.user_name}</td>
                    <td>${canRemove} ${canConfirm}</td>
                </tr>`;
            
            jQuery('#dailyReservations').append(row);
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        });
    }).fail(function(data){
        alert(data.responseJSON.message);
    });
};

app.dashboardInit = function(){
    let that = this;
    that.reservationDate = new Date().toISOString().split('T')[0];
    let selectedDateField = $('#date');
    selectedDateField.val(that.reservationDate);

    let planeSelectField = $('#planeList');

    app.loadPlanes(planeSelectField);

    let changedFieldsHandler = function(){
        that.planeRegistration = planeSelectField.find("option:selected" ).text();
        that.reservationDate = selectedDateField.val();

        $('#reservationListHeading').html(`Tabela godzin ${that.planeRegistration} ${that.reservationDate}`);

        if(that.planeRegistration == '--'){
            $('#section_2').addClass('d-none');
            return;
        }

        $('#section_2').removeClass('d-none');

        app.loadDailyPlaneReservations(
            that.planeRegistration, 
            that.reservationDate, 
            jQuery('#dailyReservations')
        );
    };

    planeSelectField.on('change', changedFieldsHandler);
    selectedDateField.on('change', function(){
        $('html, body').animate({
            scrollTop: $("#section_2").offset().top
        }, 300);
        changedFieldsHandler();
    });
};

app.makeReservation = function(starts_at_value, ends_at_value){
    let that = this;
    return $.ajax({
        type: "POST",
        url: "/api/plane/" + that.planeRegistration + "/reservation/" + that.reservationDate,
        headers: {"Authorization": 'Bearer ' + sessionStorage.getItem('token')},
        dataType: 'json',
        data: {
            starts_at: that.reservationDate + ' ' + starts_at_value + ':00',
            ends_at: that.reservationDate + ' ' + ends_at_value + ':00'
        }
    }).success(function(){
        alert('Rezerwacja została dodana. Oczekuje na potwierdzenie.');
    }).fail(function(data){
        alert(data.responseJSON.message);
    });
};