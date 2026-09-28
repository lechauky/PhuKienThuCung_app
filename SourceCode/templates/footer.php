</div>
<div class="footer">
    <div class="footter_row">
        <h1>Shop</h1>
        <ul>
            <li>
                Danh Cho Chó
            </li>
            <li>
                Danh Cho Mèo
            </li>
            <li>
                <a href=" <?php echo $rootPath . "/pages/brand.php"; ?>">Thương hiệu</a>
            </li>
        </ul>
    </div>
    <div class="footter_row">
        <h1>Paddy Pet Shop</h1>
        <ul>
            <li>
                Điều Khoản Sử Dụng
            </li>
            <li>
                Tuyển Dụng
            </li>
        </ul>
    </div>
    <div class="footter_row">
        <h1>Hỗ Trợ Khách Hàng</h1>
        <ul>
            <li>
                Chính Sách Đổi Trả Hàng
            </li>
            <li>
                Phương Thức Vận Chuyển
            </li>
            <li>
                Chính Sách Bảo Mật
            </li>
            <li>
                Phương Thức Thanh Toán
            </li>
            <li>
                Chính Sách Hoàn Tiền
            </li>
        </ul>
    </div>
    <div class="footter_row">
        <h1>Liên Hệ</h1>
        <ul>
            <li>
                CÔNG TY CỔ PHẦN THUƠNG MẠI & DỊCH VỤ PADDY
            </li>
            <li>
                116 Nguyễn Văn Thủ, Phường Đa Kao, Quận 1, Thành phố Hồ Chí Minh, Việt Nam
            </li>
            <li>
                <i class="fa-solid fa-phone"></i> Hotline: 0347693333
            </li>
            <li>
                <i class="fa-solid fa-envelope"></i> Email: marketing@paddy.vn
            </li>
        </ul>
    </div>
</div>
</div>
<div class="mangXaHoi">
    <a href="https://www.facebook.com/" target="_blank">
        <div class="mangXaHoi_item Facebook">
            <div>
                <i class="fa-brands fa-facebook" style="color: #0b84ee;"></i>
            </div>
            <span>Facebook</span>
        </div>
    </a>
    <a href="https://www.messenger.com/" target="_blank">
        <div class="mangXaHoi_item Messenger">
            <div>
                <i class="fa-regular fa-message" style="color: #e34aaa;"></i>

            </div>
            <span>Messenger</span>
        </div>
    </a>
    <a href="https://www.youtube.com/" target="_blank">
        <div class="mangXaHoi_item Youtube">
            <div>
                <i class="fa-brands fa-youtube" style="color: #FF0000;"></i>

            </div>
            <span>Youtube</span>
        </div>
    </a>
    <a href="javascript:void(0)">
        <div class="mangXaHoi_item Top" onclick="window.scrollTo(0, 0)">
            <div>
                <i class="fa-solid fa-arrow-up" style="color: black;"></i>
            </div>
            <span>Top</span>
        </div>
    </a>

</div>
<!-- nếu đã login -->
<div class="login_flex" id="login_flex">
    <div class="login_flex_right" id="login_flex_right">
        <div class="login_flex_right_title" style="margin: 10px 0 30px 0;">
            <h6>Thông tin tài khoản</h6>
            <div id="exit_login_flex">
                <i class="fa-solid fa-x"></i>
            </div>
        </div>
        <div style="display: flex; justify-content: center;align-items: center; border-radius: 50%;">

            <img src="<?php
            if (empty($_SESSION["taiKhoan"]["avatar"]) || $_SESSION["taiKhoan"]["avatar"] == null)
                echo $rootPath . '/assets/img/banner/Default_pfp.svg.png';
            else
                echo $rootPath . "/assets/img/khach_hang/" . $_SESSION["taiKhoan"]["avatar"] ?>" alt=""
                    style="width: 100px; height: 100px; text-align: center;">
            </div>
            <div class="thonTinKhac">
                <h5><span>Tên: </span>
                <?php echo $_SESSION["taiKhoan"]["hoKhachHang"] . ' ' . $_SESSION["taiKhoan"]["tenKhachHang"] ?>
            </h5>
        </div>
        <div class="thonTinKhac">
            <h5><span>Số điện thoại: </span>
                <?php echo $_SESSION["taiKhoan"]["dienThoai"] ?>
            </h5>
        </div>
        <div class="thonTinKhac">
            <h5><span>Địa chỉ: </span>
                <?php
                // SELECT xa.tenXa, huyen.tenHuyen, tinh.tenTinh FROM xa JOIN huyen ON xa.maHuyen = huyen.maHuyen JOIN tinh ON huyen.maTinh=tinh.maTinh WHERE xa.maXa = "X04036"
                $get_diachi = "SELECT xa.tenXa, huyen.tenHuyen, tinh.tenTinh FROM xa JOIN huyen ON xa.maHuyen = huyen.maHuyen JOIN tinh ON huyen.maTinh=tinh.maTinh WHERE xa.maXa = '{$_SESSION["taiKhoan"]["maXa"]}'";
                $statement = $dbh->prepare($get_diachi);
                $statement->execute();
                $diachis = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($diachis as $diachi)
                    echo $_SESSION["taiKhoan"]["diaChiCuThe"] . ', ' . $diachi['tenXa'] . ', ' . $diachi['tenHuyen'] . ', ' . $diachi['tenTinh']; ?>
            </h5>
        </div>
        <div class="thonTinKhac" style="margin: 0 0 40px 0;">
            <h5><span>Email: </span>
                <?php echo $_SESSION["taiKhoan"]["email"] ?>
            </h5>
        </div>
        <form action="<?php echo $rootPath . "/pages/edit_info.php" ?>" method="post">
            <input hidden type="text" name="editInfo" value="1">
            <input type="submit" value="Chỉnh sửa thông tin" class="button_add_admin" />
        </form>

        <a href="<?php echo $rootPath . "/pages/change_password.php" ?>">
            <input type="button" value="Đổi mật khẩu" class="button_add_admin" /></a>
        <a href="<?php echo $rootPath . "/pages/buy_history_page.php" ?>">
            <input type="button" value="Lịch sử mua hàng" class="button_add_admin" /></a>
        <form action="<?php echo $rootPath . "/includes/logout.php" ?>">
            <input type="submit" value="Đăng xuất" class="button_add_admin" />
        </form>
    </div>
</div>
<?php if (!empty($_SESSION["taiKhoan"]) && !isset($_POST['editInfo'])) {
    echo '
    <script>
        const $ = document.querySelector.bind(document);
        const login_user = $("#profile_user");
        const login_flex = $("#login_flex");
        const exit_login_flex = $("#exit_login_flex");
        const login_flex_right = $("#login_flex_right");
        var isLogin_flex = false;
        login_user.onclick = () => {
            isLogin_flex = true;
            login_flex.style.display = "block";
            login_flex_right.style.right = "0";

        };
        exit_login_flex.onclick = () => {
            isLogin_flex = false;
            login_flex.style.display = "none";
            login_flex_right.style.right = "-360px";
        };
    </script>';
} ?>



<script src="<?php echo $rootPath ?>/assets/js/app.js"></script>