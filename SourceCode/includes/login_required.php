<script>
    function addToCart(element) {
        var TB = document.querySelector('#notifacation_all');
                TB.style.bottom = "30px";
                TB.querySelector('h6').innerText = "Yêu cầu đăng nhập!";
                TB.querySelector('h6').style.color = "red";
                setTimeout(function () {
                    TB.style.bottom = "-50px";
                }, 2000);
    }
 </script>