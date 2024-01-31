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
            <div id="sticky-wrapper" class="sticky-wrapper is-sticky" style="height: 126px;"><nav class="navbar navbar-expand-lg" style="width: 1680px; position: fixed; top: 0px;">
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
                                <a class="nav-link click-scroll active" href="#" onclick="app.logout()">Wyloguj się</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll inactive" href="/reservation">Rezerwacje</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll inactive" href="/dashboard">Tabele rezerwacji</a>
                            </li>
                        </ul>

                        <div class="d-none d-lg-block">
                            <a href="#top" class="navbar-icon bi-person smoothscroll"></a>
                        </div>
                    </div>
                </div>
            </nav></div>
        </div>


        <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
            <div class="container">
                <div class="row">
                    
                    <div class="col-lg-8 col-12 mx-auto">
                        <section id="section_flash_top" class="flash-messages d-none"><!-- here flash messages will be shown with js script --></section>
                    
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
        </section>

        <section class="explore-section section-padding" id="section_plane_reservation" style="padding-bottom: 5ex;">
            <div class="container">
                <div class="col-12 text-center">
                    <h2 id="reservationListHeading" class="mb-4">Tabele godzin 2024-01-16</h2>
                </div>
            </div>

            <div class="container-fluid">
                <section id="section_flash_bottom" class="flash-messages"><!-- here flash messages will be shown with js script --></section>
                    <div class="planesboard row mb-3" style="justify-content: center;" id="planesboard">
                    <div class="col-12 col-md-6 col-sm-12 col-xl-3 themed-grid-col">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="planeheader">
                                <h3 class="mb-2">SP-ARR</h3>
                            </div>
                            <div class="">
                                
                                <div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p class="">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/3 , trasa EPOM-AAAA-BBBB-CCCC-DDDD-EEEE-EPOM. Poczytaj o lotnisku BBBB, przygotuj coś tam, plus jakiekolwiek długie notatki żeby zobaczyć jak to się mieści na ekranie </p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;"></p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div><div class="col-12 col-md-6 col-sm-12 col-xl-3 themed-grid-col">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="d-flex" style="border-bottom: 1px solid;">
                                <h5 class="mb-2">SP-ARR</h5>
                            </div>
                            <div class="">
                                
                                <div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;"></p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div><div class="col-12 col-md-6 col-sm-12 col-xl-3 themed-grid-col">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="d-flex" style="border-bottom: 1px solid;">
                                <h5 class="mb-2">SP-ARR</h5>
                            </div>
                            <div class="">
                                
                                <div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/3 , trasa EPOM-AAAA-BBBB-CCCC-DDDD-EEEE-EPOM. Poczytaj o lotnisku BBBB, przygotuj coś tam, plus jakiekolwiek długie notatki żeby zobaczyć jak to się mieści na ekranie </p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;"></p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div><div class="col-12 col-md-6 col-sm-12 col-xl-3 themed-grid-col">
                        <div class="custom-block bg-white shadow-lg">
                            <div class="d-flex" style="border-bottom: 1px solid;">
                                <h5 class="mb-2">SP-ARR</h5>
                            </div>
                            <div class="">
                                
                                <div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/3 , trasa EPOM-AAAA-BBBB-CCCC-DDDD-EEEE-EPOM. Poczytaj o lotnisku BBBB, przygotuj coś tam, plus jakiekolwiek długie notatki żeby zobaczyć jak to się mieści na ekranie </p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;"></p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div><div class="reservation-entry-row">
                                    <div class="col-1 col-md-1 col-sm-1 col-xl-1 themed-grid-col">
                                        <p><i class="bi bi-check-circle-fill" style="color: var(--primary-color);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rezerwacja potwierdzona"></i></p>
                                    </div>
                                    <div class="col-4 col-md-4 col-sm-4 col-xl-4 themed-grid-col">
                                        <p style="/*! font-weight: bold; */">10:00 - 11:15</p>
                                    </div>
                                    <div class="col-6 col-md-6 col-sm-6 col-xl-6 themed-grid-col">
                                        <p>Krzysztof Kolberger</p>
                                    </div>
                                    <div style="/*! margin-bottom: 0px; */" class="col-12 col-md-12 col-sm-12 col-xl-12 themed-grid-col">
                                        
                                        <p style="margin-bottom: 0px;">zadanie A/16</p>
                                    </div>
                                    <div style="text-align: right;" class="col-12 col-md-8 col-sm-12 col-xl-12 themed-grid-col">
                                        <p style="margin-bottom: 0.5rem;"><button type="button" class="btn btn-primary confirmReservation" data-id="9b03e4cd-0be1-4ae0-b502-1007228da163">potwierdź</button>&nbsp;
<button type="button" class="btn btn-danger removeReservation" data-id="9b03e694-1a52-48da-a474-d529ee1c7ddd">anuluj</button> </p>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
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