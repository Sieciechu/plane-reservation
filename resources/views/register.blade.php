<x-layout>
    <h1 class="text-white text-center">Zarejestruj się</h1>

    <h6 class="text-center">i wygodnie rezerwuj loty w AO</h6>

    <form method="post" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" action="/register">
        <div class="input-group input-group-lg">
            
            <input name="email" type="search" class="form-control" 
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
        <script type="text/javascript">
        $(document).ready(function() {
            $("form").on("submit", function(event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "/api/user",
                    dataType: 'json',
                    data: {
                        name: jQuery('#name').val(),
                        email: jQuery('#email').val(),
                        password: jQuery('#password').val(),
                        password_confirmation: jQuery('#password_confirmation').val()
                    }
                }).success(function(data){
                    alert("rejestracja udana. Zaloguj się.");
                    window.location.href = '/login';
                }).fail(function(data){
                    alert(data.responseJSON.message);
                });
            });

            $('#email').focus();
        });
        </script>

    </x-slot>
</x-layout>
