<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>Aeroklub Ostrowski</title>

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap"
        rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">

    <link href="css/templatemo-topic-listing.css" rel="stylesheet">
    <!--

TemplateMo 590 topic listing

https://templatemo.com/tm-590-topic-listing

-->
</head>

<body id="top">

    <main>

    <div id="sticky-wrapper" class="sticky-wrapper" style="height: 78px;"><nav class="navbar navbar-expand-lg" style="">
                <div class="container">
                    <a class="navbar-brand" href="/">
                        <i class="bi-back"></i>
                        <span>Aeroklub Ostrowski</span>
                    </a>

                    <div class="d-lg-none ms-auto me-4">
                        <a href="#top" class="navbar-icon bi-person smoothscroll"></a>
                    </div>
    
                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
    
                    <div class="navbar-collapse collapse" id="navbarNav" style="">
                        <ul class="navbar-nav ms-lg-5 me-lg-auto">
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="#" onclick="app.logout()">Wyloguj się</a>
                            </li>
                        </ul>

                        <div class="d-none d-lg-block">
                            <a href="#top" class="navbar-icon bi-person smoothscroll"></a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>


        <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
            <div class="container">
                <div class="row">

                    <div class="col-lg-8 col-12 mx-auto">
                    
                    <section id="section_flash">
                    </section>

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
        </section>

        <section class="explore-section section-padding d-none" id="section_2">
            <div class="container">

                <div class="col-12 text-center">
                    <h2 id="reservationListHeading" class="mb-4"></h1>
                </div>

            </div>
            </div>

            <div class="container">
                <div class="row mb-3 text-center">
                    
                    <div class="col-md-8 themed-grid-col">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="d-flex">
                                <h5 class="mb-2">Zarezerwowane</h5>
                            </div>
                            <div class="d-flex">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">czas</th>
                                            <th scope="col">imię, naziwsko</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="dailyReservations">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 themed-grid-col">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="d-flex">
                                <div>
                                    <h5 class="mb-2">Rezerwuj</h5>
                                </div>
                            </div>
                            <form id="makeReservationForm" method="post" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" role="search" action="#">
                                <div class="input-group input-group-lg">
                                    <input name="starts_at" type="search" class="form-control" 
                                    style="text-indent: 1ex;"
                                    id="starts_at" placeholder="HH:MM" aria-label="Search"
                                    pattern="\d\d:\d\d" title="Time in format: HH:MM"
                                    value=""
                                    >
                                </div>
                                <div class="input-group input-group-lg">
                                    <input name="ends_at" type="input" class="form-control" 
                                    style="text-indent: 1ex;"
                                    id="ends_at" placeholder="HH:MM" aria-label="Search"
                                    pattern="\d\d:\d\d" title="Time in format: HH:MM"
                                    value=""
                                >
                                </div>
                                <button type="submit" class="form-control">Rezerwuj</button>
                            </form>
                        </div>
                    </div>
                    
                </div>
               
            </div>
        </section>



    </main>

    <!-- JAVASCRIPT FILES -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/click-scroll.js"></script>
    <script src="js/custom.js"></script>
    <script src="js/app.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            let app = window.app;
            app.showFlashMessages('section_flash');

            app.dashboardInit();

            $("#makeReservationForm").on("submit", function(event) {
                event.preventDefault();
                app.makeReservation(jQuery('#starts_at').val(), jQuery('#ends_at').val());
            });
        });
    </script>

</body>

</html>