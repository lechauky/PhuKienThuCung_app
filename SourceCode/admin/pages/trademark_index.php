<?php include '../templates/nav_admin1.php' ?>
<div class="body" style="margin-top: 15px">
    <div class="table_header">
        <div class="add_admin">
            <a href="trademark_admin_create.php">
                <i class="fa-solid fa-user-plus"></i>
                <div class="add_title">
                    Thêm thương hiệu
                </div>
            </a>
        </div>
    </div>
    <table class="table_dsadmin">
        <thead>
            <tr>
                <th style="width: 65px;">Mã thương hiệu</th>
                <th style="width: 120px;">Tên thương hiệu</th>
                <th style="width: 150px;">Logo</th>
                <th style="width: 80px;">Hiển thị</th>
                <th style="width: 80px;">Chức năng</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    TH002
                </td>
                <td>
                    Nexgard
                </td>
                <td>
                    <img src="https://webkit.org/demos/srcset/image-src.png" alt=""
                        style="width: 231px; height: 74px; ">
                </td>
                <td>
                    Logo hiển thị
                </td>
                <td>
                    <a href="trademark_edit.php"><i class="fa-solid fa-pen-to-square edit"></i></a>
                    <a href="trademark_delete.php"> <i class="fa-solid fa-xmark remove"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
    <style>
        .pagination {
            display: inline-block;
            margin: auto;

        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
            border: 1px solid #ddd;
            font-size: 22px;
        }

        .pagination a.active {
            background-color: #CC3333;
            color: white;
            border: 1px solid #CC3333;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
    <div align="center">
        <ul class="page pagination">
            <a href="#">&laquo;</a>
            <a href="#">1</a>
            <a href="#" class="active">2</a>
            <a href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
            <a href="#">6</a>
            <a href="#">7</a>
            <a href="#">&raquo;</a>
        </ul>
    </div>
</div>
<?php include '../templates/nav_admin2.php' ?>