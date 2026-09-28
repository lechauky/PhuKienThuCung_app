<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function addToCart(element) {
        var maSanPham = element.getAttribute("productid");
        <?php
        if (empty($_SESSION["taiKhoan"])) {
            echo "var userID = null;";
        } else {
            echo "var userID = '" . $_SESSION["taiKhoan"]['maKhachHang'] . "';";
        }
        ?>
        excuteToCart(maSanPham, userID);
    }
    function excuteToCart(maSanPham, nguoiDung) {
        jQuery.ajax({
            url: '<?php echo $rootPath . "/includes/update_cart.php"; ?>', // Đường dẫn đến tệp xử lý PHP trên máy chủ
            type: 'POST',
            data: { maSanPham: maSanPham, nguoiDung: nguoiDung }, // Gửi ID sản phẩm lên máy chủ
            dataType: 'json',
            success: function (response) {
                console.log(response);
                if (response == 1) {
                    updateGioHang(maSanPham, nguoiDung);
                    var TB = document.querySelector('#notifacation_all');
                    TB.style.bottom = "30px";
                    TB.querySelector('h6').style.color = "green";
                    TB.querySelector('h6').innerText = "Đã thêm vào giỏ";
                    setTimeout(function () {
                        TB.style.bottom = "-50px";
                    }, 2000);
                    console.log(response);
                    
                } else {
                    var TB = document.querySelector('#notifacation_all');
                    TB.style.bottom = "30px";
                    TB.querySelector('h6').innerText = "Đã đạt số lượng tối đa";
                    TB.querySelector('h6').style.color = "red";
                    setTimeout(function () {
                        TB.style.bottom = "-50px";
                    }, 2000);
                }
            },
            error: function (xhr, status, error) {
                // Xử lý lỗi (nếu có)
                console.log(error);
            }
        });
    }
    function updateGioHang(maSanPham, nguoiDung) {
        jQuery.ajax({
            url: '<?php echo $rootPath . "/includes/count_gio_hang.php"; ?>', // Đường dẫn đến tệp xử lý PHP trên máy chủ
            type: 'POST',
            data: { maSanPham: maSanPham, nguoiDung: nguoiDung }, // Gửi ID sản phẩm lên máy chủ
            dataType: 'json',
            success: function (response) {
                var count = document.querySelector('#count_cart');
                count.textContent = response['soLuongTG'];
                console.log(response);
            },
            error: function (xhr, status, error) {
                // Xử lý lỗi (nếu có)
                console.log(error);
            }
        });
    }
</script>