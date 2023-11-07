<!-- resources/views/tasks.blade.php -->
 
<x-nonLoggedLayout>
    <h1 class="text-white text-center">Zaloguj się</h1>

    <h6 class="text-center">i wygodnie rezerwuj loty w AO</h6>

    <form method="post" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" action="/login">
        <div class="input-group input-group-lg">
            
            <input name="keyword" type="search" class="form-control" 
                id="email" placeholder="email" aria-label="Search">
            
        </div>
        
        
        <div class="input-group input-group-lg">
            <input name="password" type="password" class="form-control loginvariant" 
                id="password" placeholder="hasło" aria-label="Search">
        </div>

        <button type="submit" class="form-control" style="margin: 0 auto;">Zaloguj</button>
    </form>

    <x-slot:customScript>
        <script type="text/javascript">
        $(document).ready(function() {
            $("form").on("submit", function(event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "/api/user/login",
                    dataType: 'json',
                    data: {
                        email: jQuery('#email').val(),
                        password: jQuery('#password').val(),
                    }
                }).success(function(data){
                    window.sessionStorage.token = data.auth_token;
                    alert("zalogowano");
                    window.location.href = '/dashboard';
                }).fail(function(data){
                    alert(data.responseJSON.error);
                });
            });
        });
        </script>
    </x-slot:customScript>
</x-nonLoggedLayout>
