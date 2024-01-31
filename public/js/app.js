// import './bootstrap.js';


window.app = {};
window.app.planeRegistration = '';
window.app.reservationDate = '';

app.storage = {};
app.storage.init = function(){
    window.localStorage.aeroklubostrowski = window.localStorage.aeroklubostrowski || '';
    app.storage.internal = window.localStorage.aeroklubostrowski || '';

    app.storage
}

app.initFlashMsg = function(){
    let flashMsg = app.storage.getItem('flashMsg') || JSON.stringify({
        'success': [],
        'error': [],
    });
    app.storage.storeItem('flashMsg', flashMsg);
}

app.storage.storeItem = function(key, value){
    app.storage.internal = JSON.stringify({[key]: value});
}
app.storage.getItem = function(key){
    return JSON.parse(app.storage.internal)[key];
}
app.storage.removeItem = function(key){
    app.storage.internal = JSON.stringify({[key]: null});
}

app.storeToken = function(token){
    window.localStorage.aeroklubostrowski = JSON.stringify({token: token});
}
app.getToken = function(){
    return JSON.parse(window.localStorage.aeroklubostrowski).token;
}
app.removeToken = function(){
    window.localStorage.aeroklubostrowski = JSON.stringify({token: null});
}

app.loadPlanes = function(planeSelectField){
    return app.ajax("GET", "/api/plane", {}).success(function(data){
        let planes = data;
        planes.forEach(function(plane){
            planeSelectField.append(`<option value="${plane.id}">${plane.registration}</option>`);
        });
    }).fail(app.ajaxFail);
};
 
app.loadDailySunriseSunset = function(date, sunriseView, sunsetView){
    return app.ajax("GET", "/api/suntimes/" + date, {}).success(function(data){
        let sunrise = data.sunrise;
        let sunset = data.sunset;
        sunriseView.html(`Wschód słońca: ${sunrise} <span class="utc-warning">UTC</span>`);
        sunsetView.html(`Zachód słońca: ${sunset} <span class="utc-warning">UTC</span>`);
    }).fail(app.ajaxFail);
};

app.html = {};

app.html.activateTooltip = function(){
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
}

app.html.getDailyReservationComponent = function(item){
    let notConfirmedIcon = '<i class="bi bi-question-circle" style="color: var(--bs-danger);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja niepotwierdzona"></i>';
    let confirmedIcon = '<i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i>';
    let isConfirmed = item.is_confirmed == true ? confirmedIcon : notConfirmedIcon;
    let canConfirm = item.can_confirm == true
        ? `<button type="button" class="btn btn-primary confirmReservation" data-id="${item.id}">potwierdź</button>`
        : '';
    let canRemove = item.can_remove == true
        ? `<button type="button" class="btn btn-danger removeReservation" data-id="${item.id}">usuń</button>`
        : '';
    let component = `
<div class="reservation-entry-row">
<div class="col-1 col-md-1 col-sm-1 col-lg-1 col-xl-1 themed-grid-col">
    <p>${isConfirmed}</p>
</div>
<div class="col-4 col-md-4 col-sm-4 col-lg-2 col-xl-2 themed-grid-col">
    <p>${item.starts_at} - ${item.ends_at}</p>
</div>
<div class="col-6 col-md-6 col-sm-6 col-lg-2 col-xl-2 themed-grid-col">
    <p>${item.user_name}</p>
</div>
<div class="col-12 col-md-12 col-sm-12 col-lg-8 col-xl-5 themed-grid-col">
    <p class="mb-0">${item.comment}</p>

</div>
<div style="text-align: left;" class="col-12 col-md-8 col-sm-12 col-lg-3 col-xl-2 themed-grid-col">
    <p class="mt-1">${canRemove} ${canConfirm}</p>
</div>
</div>
`;
    return component;
};

app.html.getAdminReservationComponent = function(item){
    let notConfirmedIcon = '<i class="bi bi-question-circle" style="color: var(--bs-danger);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja niepotwierdzona"></i>';
    let confirmedIcon = '<i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i>';
    let isConfirmed = item.is_confirmed == true ? confirmedIcon : notConfirmedIcon;
    let canConfirm = item.can_confirm == true
        ? `<button type="button" class="btn btn-primary confirmReservation" data-id="${item.id}">potwierdź</button>`
        : '';
    let canRemove = item.can_remove == true
        ? `<button type="button" class="btn btn-danger removeReservation" data-id="${item.id}">usuń</button>`
        : '';
    let component = `
<div class="reservation-entry-row">
<div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
    <p>${isConfirmed}</p>
</div>
<div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
    <p>${item.starts_at} - ${item.ends_at}</p>
</div>
<div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
    <p>${item.user_name}</p>
</div>
<div class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
    <p class="mb-0">${item.comment}</p>

</div>
<div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
    <p class="mt-1">${canRemove} ${canConfirm}</p>
</div>
</div>
`;
    return component;
};


app.html.getReservationAdminColumnComponent = function(planeRegistration, reservations){
    let header = `
<div class="col-12 col-md-6 col-sm-12 col-xl-3 themed-grid-col">
    <div class="custom-block bg-white shadow-lg">
        <div class="planeheader">
            <h3 class="mb-2">${planeRegistration}</h3>
        </div>
        <div class="">
            [reservationList]
        </div>
    </div>
</div>
`;
    let reservationList = '';
    reservations.forEach(function(item){
        reservationList += app.html.getAdminReservationComponent(item);
    });
    return header.replace('[reservationList]', reservationList);
}

app.loadDailyPlaneReservations = function(planeRegistration, date, dailyReservationsView){
    return app.ajax("GET", "/api/plane/" + planeRegistration + "/reservation/" + date, {}).success(function(data){
        let dailyReservations = data;
        dailyReservationsView.html('');
        let notConfirmedIcon = '<i class="bi bi-question-circle" style="color: var(--bs-danger);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja niepotwierdzona"></i>';
        let confirmedIcon = '<i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i>';

        dailyReservations.forEach(function(item){
            let row = app.html.getDailyReservationComponent(item);
            $('#dailyReservations').append(row);
        });

        app.html.activateTooltip();

        let isPlaneReservationSectionVisible = false == $('#section_plane_reservation').hasClass('d-none')
        
        if(isPlaneReservationSectionVisible) {
            $('#section_flash_top').addClass('d-none');
            $('#section_flash_bottom').removeClass('d-none');
        } else {
            $('#section_flash_top').removeClass('d-none');
            $('#section_flash_bottom').addClass('d-none');
        }

        $('button.removeReservation').on('click', function(){
            app.removeReservation(this.dataset.id);
        });
        $('button.confirmReservation').on('click', function(){
            app.confirmReservation(this.dataset.id);
        });
    }).fail(app.ajaxFail);
};

app.dashboardInit = function(){
    app.reservationDate = new Date().toISOString().split('T')[0];
    let selectedDateField = $('#date');
    selectedDateField.val(app.reservationDate);

    let planeSelectField = $('#planeList');

    app.loadPlanes(planeSelectField);

    let sectionPlaneReservation = $('#section_plane_reservation');

    let changedFieldsHandler = function(){
        app.planeRegistration = planeSelectField.find("option:selected" ).text();
        app.reservationDate = selectedDateField.val();

        $('#reservationListHeading').html(`Tabela godzin ${app.planeRegistration} ${app.reservationDate}`);

        if(app.planeRegistration == '--'){
            sectionPlaneReservation.addClass('d-none');
            return;
        }

        app.loadDailySunriseSunset(app.reservationDate, $('#sunrise'), $('#sunset'));

        sectionPlaneReservation.removeClass('d-none');

        app.loadDailyPlaneReservations(
            app.planeRegistration, 
            app.reservationDate, 
            jQuery('#dailyReservations')
        );
    };

    planeSelectField.on('change', changedFieldsHandler);
    selectedDateField.on('change', function(){
        $('html, body').animate({
            scrollTop: sectionPlaneReservation.offset().top
        }, 300);
        changedFieldsHandler();
    });
};

app.makeReservation = function(starts_at_value, ends_at_value, comment){
    return app.ajax(
        "POST",
        "/api/plane/" + app.planeRegistration + "/reservation/" + app.reservationDate,
        {
            starts_at: app.reservationDate + ' ' + starts_at_value + ':00',
            ends_at: app.reservationDate + ' ' + ends_at_value + ':00',
            comment: comment
        }
    ).success(function(){
        app.loadDailyPlaneReservations(
            app.planeRegistration, 
            app.reservationDate, 
            jQuery('#dailyReservations')
        );
        app.addFlashMsg('success', "Rezerwacja została dodana. Oczekuje na potwierdzenie.");
        app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());
    }).fail(app.ajaxFail);
};

app.removeReservation = function(reservationId){
    return app.ajax("DELETE", `/api/plane/reservation/${reservationId}`).success(function(){
        app.loadDailyPlaneReservations(
            app.planeRegistration, 
            app.reservationDate, 
            jQuery('#dailyReservations')
        );
        app.addFlashMsg('success', "rezerwacja została usunięta");
        app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());
    }).fail(app.ajaxFail);
};

app.confirmReservation = function(reservationId){
    return app.ajax(
        "PATCH",
        `/api/plane/reservation/${reservationId}/confirm`
    ).success(function(){
        app.loadDailyPlaneReservations(
            app.planeRegistration, 
            app.reservationDate, 
            jQuery('#dailyReservations')
        );
            
        app.addFlashMsg('success', "rezerwacja została potwierdzona");
        app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());
    }).fail(app.ajaxFail);
};

app.logout = function(){
    return app.ajax("GET", "/api/user/logout", {}).success(function(){
        localStorage.removeItem('token');
        app.addFlashMsg('success', "wylogowano pomyślnie");
        window.location.href = '/login';
    }).fail(app.ajaxFail);
};

app.login = function(email, password){
    return app.ajax(
        "POST",
        "/api/user/login",
        {
            email: email,
            password: password,
        }
    ).success(function(data){
        app.storeToken(data.auth_token);
        app.addFlashMsg('success', "zalogowano");
        window.location.href = '/dashboard';
    }).fail(app.ajaxFail);
};
app.registerUser = function(name, email, phone, password, password_confirmation){
    return app.ajax(
        "POST",
        "/api/user",
        {
            name: name,
            email: email,
            phone: phone,
            password: password,
            password_confirmation: password_confirmation
        }
    ).success(function(){
        app.addFlashMsg('success',"rejestracja udana. Zaloguj się.");
        window.location.href = '/login';
    }).fail(app.ajaxFail);
};

app.getAllReservationsForDate = function(date, container){
    return app.ajax("GET", "/api/plane/reservation/date/" + date, {}).success(function(data){
        container.html('');
        for(var planeRegistration in data){
            let component = app.html.getReservationAdminColumnComponent(planeRegistration, data[planeRegistration])
            container.append(component);
        }

        app.html.activateTooltip();
    }).fail(app.ajaxFail);
};

app.ajax = function(method, url, data){
    return $.ajax({
        type: method,
        url: url,
        headers: {"Authorization": 'Bearer ' + app.getToken()},
        dataType: 'json',
        data: data
    });
};

app.ajaxFail = function(response){
    let msg = response.responseJSON.error || response.responseJSON.message || 'undefined error';
    app.addFlashMsg('error', msg);
    if(401 == response.status){
        app.removeToken();
        window.location = '/login';
        return;
    }
    app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());
};

app.flashMsgGetFirstVisibleContainer = function(){
    return $('section.flash-messages:visible:first');
};

app.addFlashMsg = function(level, msg){
    let flashMsg = JSON.parse(localStorage.flashMsg);
    flashMsg[level].push(msg);
    localStorage.flashMsg = JSON.stringify(flashMsg);
};

app.showFlashMessages = function(container){
    let flashMsg = JSON.parse(localStorage.flashMsg);

    flashMsg.success.forEach(function(message){
        container.append(`<div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow" role="alert">
            <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`);
    });
    flashMsg.error.forEach(function(message){
        container.append(`<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow" role="alert">
            <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`);
    });
    setTimeout(function(){
        container.find('button.btn-close').click();
     },1500);
     
    localStorage.flashMsg = JSON.stringify({
        'success': [],
        'error': [],
    });
};

app.storage.init();
app.initFlashMsg();
