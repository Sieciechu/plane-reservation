// import './bootstrap.js';

window.app = {};
window.app.userNamesToIdsMap = {};
window.app.planeRegistration = '';
window.app.reservationDate = '';
app.storage = {};


app.storage.init = function(){
    window.localStorage.aeroklubostrowski = window.localStorage.aeroklubostrowski || '{}';
};
app.storage.clear = function(){
    window.localStorage.aeroklubostrowski = '{}';
};
app.getFlashMessages = function(){
    return app.storage.getItem('flashMsg');
};

app.initFlashMsg = function(){
    var flashMsg = app.storage.getItem('flashMsg');
    if(typeof(flashMsg) === "undefined" || flashMsg === null){
        flashMsg = {
            'success': [],
            'error': [],
        };
    }

    app.storage.storeItem('flashMsg', flashMsg);
};

app.clearFlashMsg = function(){
    app.storage.storeItem('flashMsg', {
        'success': [],
        'error': [],
    });
};

app.storage.storeItem = function(key, value){
    let storage = JSON.parse(window.localStorage.aeroklubostrowski);
    storage[key] = value;
    window.localStorage.aeroklubostrowski = JSON.stringify(storage);
};
app.storage.getItem = function(key){
    return JSON.parse(window.localStorage.aeroklubostrowski)[key];
};
app.storage.getAll = function(){
    return JSON.parse(window.localStorage.aeroklubostrowski);
}
app.storage.removeItem = function(key){
    let storage = app.storage.getAll();
    storage[key] = null;
    window.localStorage.aeroklubostrowski = JSON.stringify(storage);
};

app.storeToken = function(token){
    app.storage.storeItem('token', token);
};
app.getToken = function(){
    return app.storage.getItem('token');
};
app.removeToken = function(){
    app.storage.removeItem('token');
};

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
};

app.html.getDailyReservationComponent = function(item){
    let isConfirmed = app.html.getConfirmationTooltipComponent(item.is_confirmed);
    let canConfirm = item.can_confirm == true
        ? `<button type="button" class="btn btn-primary confirmReservation" data-id="${item.id}">potwierdź</button>`
        : '';
    let canRemove = item.can_remove == true
        ? `<button type="button" class="btn btn-danger removeReservation" data-id="${item.id}">usuń</button>`
        : '';
    
    let users = item.user_name;
    if(item.user2_name){
        users += ', ' + item.user2_name;
    }

    let component = `
<div class="reservation-entry-row">
<div class="col-1 col-md-1 col-sm-1 col-lg-1 col-xl-1 themed-grid-col">
    <p class="confirmation-tooltip">${isConfirmed}</p>
</div>
<div class="col-4 col-md-4 col-sm-4 col-lg-2 col-xl-2 themed-grid-col">
    <p>${item.starts_at} - ${item.ends_at}</p>
</div>
<div class="col-6 col-md-6 col-sm-6 col-lg-3 col-xl-3 themed-grid-col">
    <p>${users}</p>
</div>
<div class="col-12 col-md-12 col-sm-12 col-lg-8 col-xl-4 themed-grid-col">
    <p class="mb-0">${item.comment}</p>

</div>
<div style="text-align: left;" class="col-12 col-md-8 col-sm-12 col-lg-3 col-xl-2 themed-grid-col">
    <p class="mt-1">${canRemove} ${canConfirm}</p>
</div>
</div>
`;
    return component;
};

app.html.getConfirmationTooltipComponent = function(isConfirmed){
    let notConfirmedIcon = '<i class="bi bi-question-circle" style="color: var(--bs-danger);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja niepotwierdzona"></i>';
    let confirmedIcon = '<i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i>';
    return isConfirmed == true ? confirmedIcon : notConfirmedIcon;
}

app.html.getAdminReservationComponent = function(item){
    let isConfirmed = app.html.getConfirmationTooltipComponent(item.is_confirmed);
    let canConfirm = item.can_confirm == true
        ? `<button type="button" class="btn btn-primary confirmReservation" data-id="${item.id}">potwierdź</button>`
        : '';
    let canRemove = item.can_remove == true
        ? `<button type="button" class="btn btn-danger removeReservation" data-id="${item.id}">usuń</button>`
        : '';

    let users = item.user_name;
    if(item.user2_name){
        users += ', ' + item.user2_name;
    }

    let component = `
<div class="reservation-entry-row">
<div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
    <p class="confirmation-tooltip">${isConfirmed}</p>
</div>
<div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
    <p>${item.starts_at} - ${item.ends_at}</p>
</div>
<div class="col-6 col-md-6 col-sm-6 col-xl-7 themed-grid-col">
    <p>${users}</p>
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
};

app.loadDailyPlaneReservations = function(planeRegistration, date, dailyReservationsView){
    return app.ajax("GET", "/api/plane/" + planeRegistration + "/reservation/" + date, {}).success(function(data){
        let dailyReservations = data;
        dailyReservationsView.html('');

        dailyReservations.forEach(function(item){
            let row = app.html.getDailyReservationComponent(item);
            $('#dailyReservations').append(row);
        });

        app.html.activateTooltip();

        $('button.removeReservation').on('click', function(){
            if(false == confirm('Czy na pewno chcesz usunąć rezerwację?')){
                return;
            }
            app.removeReservation(this.dataset.id);
        });
        $('button.confirmReservation').on('click', function(){
            if(false == confirm('Czy na pewno chcesz potwierdzić rezerwację?')){
                return;
            }
            app.confirmReservation(this.dataset.id);
        });
    }).fail(app.ajaxFail);
};

app.planeSelectionInit = function(){
    app.reservationDate = app.storage.getItem('reservationDate') || new Date().toISOString().split('T')[0];
    app.storage.storeItem('reservationDate', app.reservationDate);
    let selectedDateField = $('#date');
    selectedDateField.val(app.reservationDate);

    let planeSelectField = $('#planeList');
    if(planeSelectField.length != 0){
        app.loadPlanes(planeSelectField);
    }
};

app.reservationInit = function(){

    app.planeSelectionInit();

    let selectedDateField = $('#date');
    let sectionPlaneReservation = $('#section_plane_reservation');
    let planeSelectField = $('#planeList');

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

        app.storage.storeItem('reservationDate', app.reservationDate);
    };

    planeSelectField.on('change', changedFieldsHandler);
    selectedDateField.on('change', function(){
        $('html, body').animate({
            scrollTop: sectionPlaneReservation.offset().top
        }, 300);
        changedFieldsHandler();
    });
};

app.dashboardInit = function(){
    app.planeSelectionInit();

    let selectedDateField = $('#date');
    let sectionPlaneReservation = $('#section_plane_reservation');
    let planeSelectField = $('#planeList');

    app.getAllReservationsForDate(app.reservationDate, jQuery('#adminPlanesboard'));

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

        app.getAllReservationsForDate(app.reservationDate, jQuery('#adminPlanesboard'));

        app.storage.storeItem('reservationDate', app.reservationDate);
    };

    planeSelectField.on('change', changedFieldsHandler);
    selectedDateField.on('change', function(){
        $('html, body').animate({
            scrollTop: sectionPlaneReservation.offset().top
        }, 300);
        changedFieldsHandler();
    });
};

app.makeReservation = function(starts_at_value, ends_at_value, comment, user2_id){
    return app.ajax(
        "POST",
        "/api/plane/" + app.planeRegistration + "/reservation/" + app.reservationDate,
        {
            starts_at: app.reservationDate + ' ' + starts_at_value + ':00',
            ends_at: app.reservationDate + ' ' + ends_at_value + ':00',
            comment: comment,
            user2_id: user2_id
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
        jQuery(`button.removeReservation[data-id="${reservationId}"]`).closest('div.reservation-entry-row').remove(); 
        app.addFlashMsg('success', "rezerwacja została usunięta");
        app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());
    }).fail(app.ajaxFail);
};

app.confirmReservation = function(reservationId){
    return app.ajax(
        "PATCH",
        `/api/plane/reservation/${reservationId}/confirm`
    ).success(function(){
        let reservationRow = jQuery(`button.removeReservation[data-id="${reservationId}"]`)
            .closest('div.reservation-entry-row');
        reservationRow.find('p.confirmation-tooltip').html(app.html.getConfirmationTooltipComponent(true));
        reservationRow.find('button.confirmReservation').remove();

        app.html.activateTooltip();
            
        app.addFlashMsg('success', "rezerwacja została potwierdzona");
        app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());
    }).fail(app.ajaxFail);
};

app.logout = function(){
    return app.ajax("GET", "/api/user/logout", {}).success(function(){
        app.storage.clear();
        app.initFlashMsg();
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
        window.location.href = '/reservation';
    }).fail(app.ajaxFail);
};
app.getUsers = function(){
    return app.ajax("GET", "/api/user", {}).fail(app.ajaxFail);
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
        $('button.removeReservation').on('click', function(){
            if(false == confirm('Czy na pewno chcesz usunąć rezerwację?')){
                return;
            }
            app.removeReservation(this.dataset.id);
        });
        $('button.confirmReservation').on('click', function(){
            if(false == confirm('Czy na pewno chcesz potwierdzić rezerwację?')){
                return;
            }
            app.confirmReservation(this.dataset.id);
        });
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

        if('/login' != window.location.pathname){
            window.location.href = '/login';
            return;
        }
    }
    app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());
};

app.flashMsgGetFirstVisibleContainer = function(){
    return $('section.flash-messages:visible:first');
};

app.addFlashMsg = function(level, msg){
    let flashMsg = app.getFlashMessages();
    flashMsg[level].push(msg);
    app.storage.storeItem('flashMsg', flashMsg);
};

app.showFlashMessages = function(container){
    let flashMsg = app.getFlashMessages();

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
     
     app.clearFlashMsg();
};

app.initSecondUserAutocomplete = function(inputElement){
    app.getUsers().success(function(data){
        let models = data.data;
        let userNames = [];
        let userNamesToIdsMap = {};
        models.forEach(function(model){
            userNames.push(model.name);
            userNamesToIdsMap[model.name] = model.id;
        });

        app.userNamesToIdsMap = userNamesToIdsMap;

        inputElement.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
          },
          {
            name: 'userNames',
            source: substringMatcher(userNames)
          });

    });

};

var substringMatcher = function (strs) {
    return function findMatches(q, cb) {
        var matches, substringRegex;

        // an array that will be populated with substring matches
        matches = [];

        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, 'i');

        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function (i, str) {
            if (substrRegex.test(str)) {
                matches.push(str);
            }
        });

        cb(matches);
    };
};