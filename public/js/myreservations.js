
app.html.getMyReservationComponent = function(item){
    let isConfirmed = app.html.getConfirmationTooltipComponent(item.is_confirmed);
    let canRemove = item.can_remove == true
        ? `<button type="button" class="btn btn-danger removeReservation" data-id="${item.id}">usuń</button>`
        : '';

    let users = item.user_name;
    if(item.user2_name){
        users += ', ' + `<span class="fw-bolder">${item.user2_name}</span>`;
    }

    let component = `
<div class="reservation-entry-row">
<div class="themed-grid-col">
    <p class="confirmation-tooltip">${isConfirmed}</p>
</div>
<div class="themed-grid-col">
    <p>${item.starts_at} - ${item.ends_at}</p>
</div>
<div class="themed-grid-col">
    <p>${item.plane_registration}</p>
</div>
<div class="themed-grid-col">
    <p>${users}</p>
</div>
<div class="themed-grid-col">
    <p class="mb-0">${item.comment}</p>

</div>
<div class="text-end themed-grid-col" class="themed-grid-col">
    <p class="mt-1">${canRemove}</p>
</div>
</div>
`;
    return component;
};


app.html.getMyReservationsDateComponent = function(date, reservations){
    let header = `
<div class="col-12 col-md-6 col-sm-12 col-xl-3 themed-grid-col">
    <div class="custom-block bg-white shadow-lg">
        <div class="planeheader">
            <h3 class="mb-2">${date}</h3>
        </div>
        <div class="">
            [reservationList]
        </div>
    </div>
</div>
`;
    let reservationList = '';
    reservations.forEach(function(item){
        reservationList += app.html.getMyReservationComponent(item);
    });
    return header.replace('[reservationList]', reservationList);
};

app.action.myreservationsSite = function(){
    jQuery('#section_1').html(`
<div class="container">
    <div class="row">
        
        <div class="col-lg-8 col-12 mx-auto">
            <h1 id="heading" class="text-white text-center">Moje nadchodzące rezerwacje</h1>
        </div>
    </div>
</div>
`);
    jQuery('#section_2').html(`
<section class="explore-section section-padding" id="section_plane_reservation" style="padding-bottom: 5ex;">
    <div class="container-fluid">
        <div class="myPlanesboard planesboard row mb-3" style="justify-content: center;" id="myReservationsBoard">
            <!-- here reservations will be loaded by js -->
        </div>
    </div>
</section>
`);
    var container = jQuery('#myReservationsBoard');
    let promise = app.getAllReservationsForUserStartingFromDate(new Date().toISOString().split('T')[0], jQuery('#myReservationsBoard'));
    promise.success(function(data){
        
        container.html('');

        if (!data || data.length == 0) {
            jQuery("#heading").html('Brak nadchodzących rezerwacji');
            return;
        }

        let date = data[0].starts_at_date;
        let reservationsByDate = [];
        for(var i in data){
            if(date == data[i].starts_at_date){
                reservationsByDate.push(data[i]);
                continue;
            }
            if(date != data[i].date){
                let html = app.html.getMyReservationsDateComponent(date, reservationsByDate);
                container.append(html);
                reservationsByDate = [];

                date = data[i].starts_at_date;
                reservationsByDate.push(data[i]);
            }
        }
        if(reservationsByDate.length > 0){
            let html = app.html.getMyReservationsDateComponent(date, reservationsByDate);
            container.append(html);
            reservationsByDate = [];
        }

        app.html.activateTooltip();
        $('button.removeReservation').on('click', function(){
            if(false == confirm('Czy na pewno chcesz usunąć rezerwację?')){
                return;
            }
            app.removeReservation(this.dataset.id);
        });
    });
};

jQuery('#myreservations').click(function(){
	app.action.myreservationsSite();
    jQuery('#navbarNav').find('.active').removeClass('active');
    jQuery(this).addClass('active');
});

