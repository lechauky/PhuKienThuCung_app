<?php
include '../templates/nav_admin1.php';
include '../includes/get_new_id_entry.php';
?>
<style>
    input,
    select,
    textarea {
        font-size: 16px;
    }

    label {
        font-size: 20px;
    }

    table {
        border-collapse: collapse;
    }
</style>
<div class="body" style="margin-top: 15px">
    <h1 class="Title_Admin_create_form">Phiếu thêm sản phẩm vào kho hàng</h1>
    <p class="Notification_create_form">Vui lòng điền thông tin bên dưới</p>
    <form action="" method="post">
        <div class="form_field" style="width: 50%; float: left;">
            <label for="" class="name_form_field">Mã phiếu nhập kho : </label>
            <input type="text" class="textfile" readonly value="<?php echo $maPhieu; ?>" id="MAPHIEUNK"
                name="MAPHIEUNK">
        </div>
        <div class="form_field" style="width: 50%; float: right;">
            <label for="" class="name_form_field">Ngày nhập kho: </label>
            <input type="date" class="textfile" id="NGAYNK" name="NGAYNK" value="<?php echo date('Y-m-d'); ?>">
            <span class="error_message"></span>
        </div>
        <table id="productTable" width="100%">
            <thead>
                <tr>
                    <th>Sản Phẩm</th>
                    <th>Số Lượng</th>
                    <th>Giá Nhập</th>
                </tr>
            </thead>
            <tbody>
                <tr id="templateRow">
                    <td>
                        <select class="textfile invalid" name="maSanPham" id="sanpham">
                            <option value="">Chọn sản phẩm</option>
                            <?php include '../includes/show_product_in_option.php' ?>
                    </td>
                    <td><input type="number" name="soLuong"></td>
                    <td><input type="number" name="donGia"> đ</td>
                    <td><button type="button" class="removeRow">Xóa</button></td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top:10px"><span id="error" style="font-size:20px; color: red;"></span></div>
        <table align="center">
            <tr>
                <td colspan="2"><button type="button" id="addRow">Thêm Dòng</button></td>
                <td colspan="2"><input type="submit" id="save" name="save" value="Lưu Phiếu Nhập"></td>
                <td colspan="2"><a href="entry_index.php"><input type="button" value="Quay lại"
                            class="button_add_admin" /></a></td>
            </tr>

        </table>

    </form>
</div>

<script src="../../assets/js/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Template row
        var templateRow = $("#templateRow").html();

        // Add row button click event
        $("#addRow").click(function () {
            // Clone the template row
            var newRow = $("<tr>" + templateRow + "</tr>");

            // Display the new row
            newRow.css("display", "table-row");
            $("#productTable tbody").append(newRow);
        });

        // Remove row button click event
        $("#productTable").on("click", ".removeRow", function () {
            $(this).closest("tr").remove();
        });
        // Save button click event
        $("#save").click(function () {

            var valid = true;
            // Get all tr
            var trs = $("#productTable tbody tr");

            // Create three arrays to store the values
            var maSanPhams = [];
            var soLuongs = [];
            var donGias = [];

            // Get the value of the mã phiếu nhập kho field
            var maPhieuNK = $("#MAPHIEUNK").val();

            // Get the value of the ngày nhập kho field
            var ngayNK = $("#NGAYNK").val();

            // Loop through the trs
            for (var i = 0; i < trs.length; i++) {
                // Get the product ID
                var maSanPham = $(trs[i]).find("select").val();

                // Get the soLuong
                var soLuong = $(trs[i]).find("input[name='soLuong']").val();

                // Get the donGia
                var donGia = $(trs[i]).find("input[name='donGia']").val();

                if (maSanPham == "") {
                    $('#error').text("Vui lòng điền đầy đủ sản phẩm");
                    valid = false;
                    break;
                } else {
                    $('#error').text("");
                }
                if (soLuong == "") {
                    $('#error').text("Vui lòng điền đầy đủ số lượng");
                    valid = false;
                    break;
                } else {
                    $('#error').text("");
                }
                if (donGia == "") {
                    $('#error').text("Vui lòng điền đầy đủ đơn giá nhập");
                    valid = false;
                    break;
                } else {
                    $('#error').text("");
                }

                // Add the values to the corresponding arrays
                maSanPhams.push(maSanPham);
                soLuongs.push(soLuong);
                donGias.push(donGia);
            }

            if (valid == true) {
                // Create a new array to store the values
                var values = { maPhieuNK, ngayNK, maSanPhams, soLuongs, donGias };
                // Prevent the page from reloading
                event.preventDefault();

                // Create an AJAX request
                $.ajax({
                    url: "../includes/entry_product.php",
                    type: "POST",
                    data: { maPhieuNK: maPhieuNK, ngayNK: ngayNK, maSanPhams: maSanPhams, soLuongs: soLuongs, donGias: donGias },
                    success: function (response) {
                        // Do something with the response
                        console.log(response);
                        alert("Nhập sản phẩm thành công");
                        window.location.href = "../pages/entry_index.php";
                    },
                    error: function (error) {
                        // Handle the error
                        console.log(error);
                        // Throw an error to the AJAX function
                        throw new Error('Có lỗi khi tạo phiếu nhập hàng');
                    },
                });
            } else { event.preventDefault(); }
        });
    });
</script>
<?php include '../templates/nav_admin2.php' ?>