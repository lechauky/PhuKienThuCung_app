<?php include '../templates/header.php' ?>
<?php
if (empty($_SESSION['taiKhoan']))
    echo '<script>
            window.location.href = "../pages/login.php";
        </script>';
$maKhachHang = $_SESSION['taiKhoan']['maKhachHang']; ?>
<?php
$statement = $dbh->prepare(
    "SELECT 
        gio_hang.soLuong, 
        san_pham.maSanPham, 
        san_pham.tenSanPham, 
        san_pham.donGiaBan, 
        san_pham.hinhAnh, 
        thuong_hieu.tenThuongHieu, 
        IFNULL(giam_gia.maLoai, 0) AS maLoai, 
        IFNULL(
            CASE 
                WHEN giam_gia.ngayBatDau <= CURDATE() AND giam_gia.ngayKetThuc >= CURDATE() THEN giam_gia.giaTriGiam 
                ELSE 0 
            END, 
            0
        ) AS giaTriGiam 
    FROM gio_hang 
    JOIN san_pham ON gio_hang.maSanPham = san_pham.maSanPham 
    JOIN thuong_hieu ON thuong_hieu.maThuongHieu = san_pham.maThuongHieu 
    LEFT JOIN giam_gia ON san_pham.maSanPham = giam_gia.maSanPham 
    WHERE gio_hang.maKhachHang = '" . $maKhachHang . "'"
);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    function botSanPham(element) {

        var maSanPham = element.getAttribute("maSanPham");
        var quantityElement = document.querySelector('.soLuong' + maSanPham);
        var currentQuantity = parseInt(quantityElement.textContent);

        if (currentQuantity > 1) {
            updateProductQuantity(maSanPham, -1); // Giảm số lượng sản phẩm dùng ajax
            quantityElement.textContent = currentQuantity - 1;  // Cập nhật trên giao diện
            updateThanhTien(maSanPham); // Cập nhật thành tiền
        } else {
            var confirmation = confirm("Bạn có chắc muốn xóa?");// Nếu số lượng sản phẩm về 0, hỏi xem có muốn xóa không 
            if (confirmation)
                delete_Product(maSanPham); // Xóa
        }
    }

    function themSanPham(element) {
        var maSanPham = element.getAttribute("maSanPham");
        updateProductQuantity(maSanPham, 1); // Tăng số lượng sản phẩm dùng ajax
    }


    // Hàm tính lại Thành tiền cho một sản phẩm cụ thể
    function updateThanhTien(maSanPham) {
        const donGiaBanElement = document.querySelector('.donGiaBan' + maSanPham);
        const soLuongElement = document.querySelector('.soLuong' + maSanPham);
        const giamGiaElement = document.querySelector('.giamGia' + maSanPham);
        const thanhTienElement = document.querySelector('.thanhTien' + maSanPham);
        const donGiaBan = parseFloat(donGiaBanElement.textContent);
        const soLuong = parseInt(soLuongElement.textContent);
        const giamGia = giamGiaElement.textContent;

        let thanhTien = donGiaBan * soLuong; // Tính giá trị Thành tiền dựa trên số lượng, đơn giá và giảm giá
        if (giamGia.endsWith('%')) {
            const giamGiaPhanTram = parseFloat(giamGia);
            thanhTien -= (thanhTien * (giamGiaPhanTram / 100));
        } else {
            const giamGiaSoTien = parseFloat(giamGia);
            thanhTien -= giamGiaSoTien * soLuong;
        }

        // Hiển thị giá trị Thành tiền mới
        thanhTienElement.textContent = thanhTien.toFixed(0); // Định dạng Thành tiền về số nguyên
        updateTotalPrice();
    }

    // Thực hiện Ajax request để cập nhật số lượng sản phẩm
    function updateProductQuantity(maSanPham, quantityChange) {
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo $rootPath . "/includes/update_product_quantity.php"; ?>', // Đường dẫn đến file xử lý cập nhật số lượng sản phẩm
            data: { maSanPham: maSanPham, maKhachHang: '<?php echo $maKhachHang; ?>', quantityChange: quantityChange },
            success: function (response) {
                var quantityElement = document.querySelector('.soLuong' + maSanPham);
                var currentQuantity = parseInt(quantityElement.textContent);
                if (response == 1) {
                        quantityElement.textContent = currentQuantity + 1;  // Cập nhật trên giao diện
                        updateThanhTien(maSanPham); // Cập nhật tổng tiền
                } else if (response == 2) {
                        updateThanhTien(maSanPham);
                } else {
                    alert('Số lượng tối đa cho sản phẩm này là '+ currentQuantity );
                }
            },
        });
    }

    function deleteProduct(element) {
        var maSanPham = element.getAttribute("maSanPham");
        var confirmation = confirm("Bạn có chắc muốn xóa?");
        if (confirmation)
            delete_Product(maSanPham); // xóa sản phẩm khỏi giỏ
        updateCountCart();
    }

    // Thực hiện Ajax request để xóa sản phẩm khỏi giỏ hàng
    function delete_Product(maSanPham) {
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo $rootPath . "/includes/delete_product.php"; ?>', // Đường dẫn đến file xử lý xóa sản phẩm
            data: { maSanPham: maSanPham, maKhachHang: '<?php echo $maKhachHang; ?>' },
            success: function (response) {
                // Xóa sản phẩm khỏi giao diện sau khi thành công
                var productElement = document.querySelector('.soLuong' + maSanPham).closest('.body_table_item');
                productElement.remove();
                updateTotalPrice();// Cập nhật tổng tiền sau khi xóa sản phẩm
                updateCountCart();
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Lặp qua tất cả sản phẩm để tính và hiển thị Thành tiền khi trang được load
        const maSanPhamElements = document.querySelectorAll('.body_table_item');
        maSanPhamElements.forEach(function (element) {
            const maSanPham = element.querySelector('.soLuong').getAttribute("maSanPham");
            updateThanhTien(maSanPham);
        });
        updateTotalPrice();// Gọi hàm tính tổng tiền ban đầu
    });

    function updateTotalPrice() {
        const maSanPhamElements = document.querySelectorAll('.body_table_item');
        let totalPrice = 0;

        maSanPhamElements.forEach(function (element) {
            const maSanPham = element.querySelector('.soLuong').getAttribute("maSanPham");
            const thanhTienElement = document.querySelector('.thanhTien' + maSanPham);
            const thanhTien = parseFloat(thanhTienElement.textContent.replace(" VNĐ", "").replace(/\./g, ""));
            totalPrice += thanhTien;
        });

        // Cập nhật tổng tiền lên giao diện
        const totalPriceElement = document.getElementById('totalPrice');
        totalPriceElement.textContent = totalPrice;

        // Tìm thẻ span bằng ID
        var thanhTienInput = document.getElementById("thanhTienInput");
        thanhTienInput.value = totalPrice;
    }
</script>

<div class="cart_title">GIỎ HÀNG CỦA BẠN</div>
<br> 
<div class="cart">
    <div class="cart_table">
        <div class="header_table">
            <div class="header_table_title" style="width: 40%; padding-left: 20px;">
                SẢN PHẨM
            </div>
            <div class="header_table_title" style="width: 15%">
                GIÁ
            </div>
            <div class="header_table_title" style="width: 15%">
                SỐ LƯỢNG
            </div>
            <div class="header_table_title" style="width: 15%">
                GIẢM GIÁ
            </div>
            <div class="header_table_title" style="width: 10%">
                THÀNH TIỀN
            </div>
            <div class="header_table_title" style="width: 5%">

            </div>
        </div>
        <div class="body_table">
            <?php
            while ($row = $statement->fetch()) {
                echo '<div class="body_table_item">
                        <div class="body_table_title body_table_title_sanpham" style="width: 37%;">
                            <img src="' . $rootPath . '/assets/img/sanpham/' . trim($row->hinhAnh) . '" alt="" style="height: 120px; width: 90px;">
                            <div class="decription_product">
                                <div>
                                    <a href="" class="title_product_cart" style="color: black">' . $row->tenSanPham . '</a>   
                                </div>
                                <div style="margin-top: 10px;">
                                    <a href="" class="th_product_cart" style="color: #0b84ee; font-weight: 700; ">' . $row->tenThuongHieu . '</a>
                                </div>
                            </div>
                        </div>
                        <div class="body_table_title donGiaBan' . $row->maSanPham . '" style="width: 15%; font-weight: 700;">' . $row->donGiaBan . '</div>
                        <div class="body_table_title" style="width: 15%">
                            <div class="body_table_title_soluong">
                                <div onclick="botSanPham(this)" maSanPham="' . $row->maSanPham . '">
                                    <i class="fa-solid fa-minus"></i>
                                </div>
                                <div class="soLuong soLuong' . $row->maSanPham . '" maSanPham="' . $row->maSanPham . '">' . $row->soLuong . '</div>
                                <div onclick="themSanPham(this)" maSanPham="' . $row->maSanPham . '">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            </div>
                        </div>
                        <div class="body_table_title giamGia' . $row->maSanPham . '" style="width: 15%">' . $row->giaTriGiam . (($row->maLoai == 0) ? (" %") : (" VNĐ")) . '</div>
                        <div class="body_table_title thanhTien' . $row->maSanPham . '" style="width: 10%; font-weight: 700; "> </div>
                        <div class="body_table_title" style="min-width: 5%;">
                            <i class="fa-solid fa-x" maSanPham=' . $row->maSanPham . ' onclick="deleteProduct(this)"></i>
                        </div>
                    </div>';
            } ?>
        </div>
    </div>
    <div class="cart_checkcout" style="width: 350px;">
        <form action="cart_order.php" method="post">
            <h6 style="margin-top: 0">TỔNG SỐ TIỀN</h6>
            <div class="cart_checkcout_title">
                <div class="total-price">
                    <span id="totalPrice">0</span> VNĐ
                </div>
            </div>
            <div hidden>
                <input type="text" id="thanhTienInput" name="thanhTienInput" value="0">
            </div>
            <button type="submit">Đặt Hàng</button>
        </form>
        <a href="<?php echo $rootPath . "/pages/product_page.php"; ?> "><button>Tiếp Tục Mua Sắm</button></a>
    </div>
</div>
<?php include '../templates/footer.php' ?>