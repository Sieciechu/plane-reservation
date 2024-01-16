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
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="check-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </symbol>
        <symbol id="info-fill" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
        </symbol>
        <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </symbol>
    </svg>

    <main>

        <div id="sticky-wrapper" class="sticky-wrapper" style="height: 78px;">
            <nav class="navbar navbar-expand-lg" style="">
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
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="/dashboard">Rezerwacje</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="/dashboard2">Tabele rezerwacji</a>
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
                        <section id="section_flash_top" class="flash-messages"><!-- here flash messages will be shown with js script --></section>
                    
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

        <section class="explore-section section-padding d-none" id="section_plane_reservation">
            <div class="container">
                <div class="col-12 text-center">
                    <h2 id="reservationListHeading" class="mb-4"><!-- Tabela godzin SP-KYS 2023-11-06 --></h1>
                </div>
            </div>

            <div class="container">
                <section id="section_flash_bottom" class="flash-messages d-none"><!-- here flash messages will be shown with js script --></section>
                <div class="row mb-3">
                    
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
                                            <th scope="col">czas <span class="utc-warning">UTC(!)</span></th>
                                            <th scope="col">imię, naziwsko</th>
                                            <th scope="col">komentarz</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="dailyReservations" class="text-start">
                                    </tbody>
                                </table>
                            </div>
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
                                    <input name="starts_at" type="search" class="form-control" 
                                    style="text-indent: 1ex;"
                                    id="starts_at" placeholder="HH:MM" aria-label="Search"
                                    maxlength="5"
                                    pattern="\d\d:\d\d" title="UTC Time in format: HH:MM"
                                    value=""
                                    >
                                </div>
                                <div class="input-group input-group-lg">
                                    <input name="ends_at" type="input" class="form-control" 
                                    style="text-indent: 1ex;"
                                    id="ends_at" placeholder="HH:MM" aria-label="Search"
                                    maxlength="5"
                                    pattern="\d\d:\d\d" title="UTC Time in format: HH:MM"
                                    value=""
                                >
                                </div><div class="input-group input-group-lg">
                                    <input name="comment" type="input" class="form-control" 
                                    style="text-indent: 1ex;"
                                    id="comment" placeholder="opcjonalny komentarz" aria-label="Search"
                                    value=""
                                >
                                </div>
                                <button type="submit" class="form-control">Rezerwuj</button>
                            </form>
                        </div>
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
            app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());

            app.dashboardInit();

            $("#makeReservationForm").on("submit", function(event) {
                event.preventDefault();
                app.makeReservation(
                    jQuery('#starts_at').val(),
                    jQuery('#ends_at').val(),
                    jQuery('#comment').val()
                );
            });
        });
    </script>

</body>

</html>