// import './bootstrap.js';


window.app = {};

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
// 
app.loadDailyPlaneReservations = function(planeRegistration, date, dailyReservationsView){
    $.ajax({
        type: "GET",
        url: "/api/plane/" + planeRegistration + "/reservation/" + date,
        headers: {"Authorization": 'Bearer ' + sessionStorage.getItem('token')},
        dataType: 'json'
    }).success(function(data){
        let dailyReservations = data.data;
        dailyReservationsView.html('');
        let notConfirmed = '<i class="bi bi-question-circle"></i>';
        let confirmed = '<i class="bi bi-check-circle-fill">';
        
        dailyReservations.forEach(function(item){
            let isConfirmed = item.confirmed_at == null ? notConfirmed : confirmed;    
            let start = item.starts_at.split(' ')[1].substring(0,5);
            let end = item.ends_at.split(' ')[1].substring(0,5);
            let name = 'xxxx';
            let row = `<tr>
                    <th>${isConfirmed}</th>
                    <th scope="row">${start} - ${end}</th>
                    <td>${name}</td>
                </tr>`;
            
            jQuery('#dailyReservations').append(row);
        });
    }).fail(function(data){
        alert(data.responseJSON.message);
    });
};