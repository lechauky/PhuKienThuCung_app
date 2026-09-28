<!--Thương hiệu-->
<?php include '../templates/header.php';
require_once('../includes/config.php');
$sql = "SELECT * FROM thuong_hieu";
$stmt = $dbh->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class='thuongHieu_page' style='margin-left: 40px; min-height: 600px;'>
    <h6 style='font-size: 24px; text-align: center;'>Danh sách thương hiệu</h6>
    <div class='grid'>
        <div class='row'>
            <?php foreach ($result as $row) {
                echo "<div class='thuongHieu' style='margin: 10px;'>
                <a href='product_search_page.php?TH[]=".$row['maThuongHieu']."'  method='GET'>
                    <img src='../assets/img/thuong_hieu/".$row['logo']."' alt='' style='width: 100%; height: 80px;'>
                    <h6>
                    ".$row['tenThuongHieu']."
                    </h6>
                </a>
            </div>";
                }?>
                
        </div>
    </div>
</div>


    <?php include '../templates/footer.php' ?>