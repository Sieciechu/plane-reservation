

app.action.makereservationSite = function(){
    jQuery('#section_1').html(`
<div class="container">
    <div class="row">
        
        <div class="col-lg-8 col-12 mx-auto">
            <h1 class="text-white text-center">Wybierz samolot</h1>

            <form method="get" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" role="search" action="#">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bi-search" id="basic-addon1">
                    </span>

                    <select id="planeList" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example"
                        style="box-shadow: none;border: 0; margin-bottom: 0 !important; text-align: center;" name="plane">
                        <option selected>--</option>
                    </select>
                </div>
                <h6 class="text-center text-white">wybierz datę</h6>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bi-search" id="basic-addon1">
                    </span>

                    <input id="date" name="date" type="date" class="form-control"
                        style="box-shadow: none;border: 0; text-indent: 1ex;margin-bottom: 0; text-align: center;" id="keyword"
                        value="" aria-label="Search">
                </div>
            </form>
        </div>

    </div>
</div>
`);
    jQuery('#section_2').html(`
    <section class="explore-section section-padding d-none" id="section_plane_reservation">
    <div class="container">
        <div class="col-12 text-center">
            <h2 id="reservationListHeading" class="mb-4"><!-- Tabela godzin SP-KYS 2023-11-06 --></h1>
        </div>
    </div>

    <div class="container">
        <div class="planesboard row mb-3">
        
            <div class="col-12 themed-grid-col">
                <div class="custom-block bg-white shadow-lg">
                    <div class="planeheader">
                        <h3 class="mb-2">Zarezerwowane</h3>
                    </div>
                    <div id="dailyReservations"><!-- here reservations will be loaded by js --></div>
                </div>
            
            </div>
        
        </div>

        <div class="row mb-3">
            <div class="col-md-4 themed-grid-col">
                <div class="custom-block bg-white shadow-lg">
                    <div class="d-flex">
                        <div>
                            <h5 class="mb-2">Słońce</h5>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p id="sunrise"></p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p id="sunset"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 themed-grid-col">
                <div class="custom-block bg-white shadow-lg">
                    <div class="d-flex">
                        <div>
                            <h5 class="mb-2">Rezerwuj</h5>
                            <span class="mb-2 utc-warning">podaj czas utc</span>
                        </div>
                    </div>

                    <form id="makeReservationForm" method="post" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" role="search" action="#">
                        <div class="input-group input-group-lg">
                            <input name="starts_at" type="time" class="form-control" 
                                style="text-indent: 1ex;"
                                id="starts_at" aria-label="Search"
                                title="UTC Time in format: HH:MM"
                                value=""
                            >
                        </div>
                        <div class="input-group input-group-lg">
                            <input name="ends_at" type="time" class="form-control" 
                                style="text-indent: 1ex;"
                                id="ends_at" aria-label="Search"
                                title="UTC Time in format: HH:MM"
                                value=""
                            >
                        </div>
                        <div class="input-group input-group-lg">
                            <input name="comment" type="input" class="form-control" 
                                style="text-indent: 1ex;"
                                id="comment" placeholder="opcjonalny komentarz" aria-label="Search"
                                value=""
                            >
                        </div>
                        <div class="input-group input-group-lg">
                            <input name="user2" type="input" class="form-control" 
                                style="text-indent: 1ex;"
                                id="user2" placeholder="opcjonalny drugi pilot" aria-label="Search"
                                value=""
                            >
                        </div>
                        <button type="submit" class="form-control">Rezerwuj</button>
                    </form>
                </div>
            </div>

        </div> <!-- end of row mb-3 -->
    </div>
</section>
`);
};

jQuery('#makereservation').click(function(){
	app.action.makereservationSite();
    jQuery('#navbarNav').find('.active').removeClass('active');
    jQuery(this).addClass('active');
    app.reservationInit();
    app.initSecondUserAutocomplete(jQuery('#user2'));

    $("#makeReservationForm").on("submit", function(event) {
        event.preventDefault();
        app.makeReservation(
            jQuery('#starts_at').val(),
            jQuery('#ends_at').val(),
            jQuery('#comment').val(),
            app.userNamesToIdsMap[jQuery('#user2').val()]
        ).success(function() {
            jQuery('#starts_at').val('');
            jQuery('#ends_at').val('');
            jQuery('#comment').val('');
            jQuery('#user2').val('');
        });
    });
});
