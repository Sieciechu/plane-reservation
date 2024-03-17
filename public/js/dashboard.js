
app.action.dashboardSite = function(){
    jQuery('#section_1').html(`
<div class="container">
    <div class="row">
        
        <div class="col-lg-8 col-12 mx-auto">
            <h1 class="text-white text-center">Wybierz datę</h1>

            <form method="get" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" role="search" action="#">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bi-search" id="basic-addon1">
                    </span>

                    <input id="date" name="date" type="date" class="form-control" style="box-shadow: none;border: 0; text-indent: 1ex;margin-bottom: 0; text-align: center;" value="" aria-label="Search">
                </div>
            </form>
        </div>

    </div>
</div>
`);
    jQuery('#section_2').html(`
<section class="explore-section section-padding" id="section_plane_reservation" style="padding-bottom: 5ex;">
    <div class="container">
        <div class="col-12 text-center">
            <h2 id="reservationListHeading" class="mb-4"><!-- will be filled by js --></h2>
        </div>
    </div>

    <div class="container-fluid">
        <div class="adminPlanesboard planesboard row mb-3" style="justify-content: center;" id="adminPlanesboard">
            <!-- here reservations will be loaded by js -->
        </div>
    </div>
</section>

<section id="lower-section" class="contact-section section-padding section-bg">
    <div class="container-fluid">

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
                            <p id="sunrise">Wschód słońca: 06:52 <span class="utc-warning">UTC</span></p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div>
                            <p id="sunset">Zachód słońca: 14:54 <span class="utc-warning">UTC</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
`);
};

jQuery('#dashboard').click(function(){
	app.action.dashboardSite();
    app.dashboardInit();
    jQuery('#navbarNav').find('.active').removeClass('active');
    jQuery(this).addClass('active');
});

