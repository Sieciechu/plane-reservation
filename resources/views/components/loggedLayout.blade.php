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
    <link href="css/app.css" rel="stylesheet">
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

        <nav class="navbar navbar-expand-lg" style="">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <img src="/images/ao_logo_2017.svg" alt="Aeroklub Ostrowski Logo">
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
                            <a class="nav-link click-scroll" id="myreservations" href="#myreservations">Moje rezerwacje</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" id="makereservation" href="#makereservation">Rezerwuj</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" id="dashboard" href="#dashboard">Tabele rezerwacji</a>
                        </li>
                    </ul>

                    <div class="d-none d-lg-block">
                        <a href="#top" class="navbar-icon bi-person smoothscroll"></a>
                    </div>
                </div>
            </div>
        </nav>

        <section id="section_flash_top" class="flash-messages">
            <!-- here flash messages will be shown with js script -->
        </section>

        <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
            {{ $section1 ?? '' }}
        </section>

        <section id="section_2">
            {{ $section2 ?? '' }}
        </section>
    </main>

    <!-- JAVASCRIPT FILES -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/typeahead.bundle.js"></script>
    <script src="js/custom.js"></script>
    <script src="js/app.js?ver=1.0"></script>
    <script src="js/myreservations.js?ver=1.0"></script>
    <script src="js/makereservation.js?ver=1.0"></script>
    <script src="js/dashboard.js?ver=1.0"></script>
    <script type="text/javascript">
        let app = window.app;
        app.storage.init();
        app.initFlashMsg();
        app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());

        jQuery(document).ready(function(){
            let jsAction = window.location.hash;
            if (window.location.hash == '') {
                jsAction = '#myreservations';
            }
            if (jsAction) {
                setTimeout(function(){jQuery(jsAction).click();}, 10); // if I remove timeout custom.js lib returns error
            }
        });
    </script>
    {{ $customScript ?? ''}}

</body>

</html>