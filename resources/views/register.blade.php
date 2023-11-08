<x-nonLoggedLayout>
    <section id="section_flash" class="flash-messages"><!-- here flash messages will be shown with js script --></section>

    <h1 class="text-white text-center">Zarejestruj się</h1>

    <h6 class="text-center">i wygodnie rezerwuj loty w AO</h6>

    <form method="post" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" action="/register">
        <div class="input-group input-group-lg">
            
            <input name="email" type="email" class="form-control" 
                id="email" placeholder="email" aria-label="Search">
            
        </div>

        <div class="input-group input-group-lg">
            
            <input name="name" type="search" class="form-control" 
                id="name" placeholder="imię i nazwisko" aria-label="Search">
            
        </div>
        
        
        <div class="input-group input-group-lg">
            <input name="password" type="password" class="form-control loginvariant" 
            
                id="password" placeholder="hasło" aria-label="Search">

        </div>

        <div class="input-group input-group-lg">
            <input name="password_confirmation" type="password" class="form-control loginvariant" 
                id="password_confirmation" placeholder="powtórz hasło" aria-label="Search">

        </div>

        <button type="submit" class="form-control" style="margin: 0 auto;">Rejestruj</button>
    </form>

    <x-slot:customScript>
        <script src="js/app.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            app.showFlashMessages(app.flashMsgGetFirstVisibleContainer());

            $("form").on("submit", function(event) {
                event.preventDefault();
                app.registerUser(
                    jQuery('#name').val(),
                    jQuery('#email').val(),
                    jQuery('#password').val(),
                    jQuery('#password_confirmation').val()
                );
            });

            $('#email').focus();
        });
        </script>

    </x-slot>
</x-nonLoggedLayout>
