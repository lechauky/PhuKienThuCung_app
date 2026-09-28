<style>
    .menu-wrapper {

        height: auto;
        width: 100%;
    }

    .menu {
        margin: 0;
        padding: 0 0 0 20px;
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

    .menu li {
        display: inline-block;
        margin: 5px;
    }

    .pagination a:hover:not(.active) {
        background-color: #ddd;
    }

    ul {
        list-style-type: none;
    }
</style>
<?php
echo "<li>
        <a href=\"?id=" . $id . "&page=1\">&laquo;</a>
    </li>";
for ($i = 1; $i <= $totalPages; $i++) {
    if ($i != $currentPage) {
        echo "<li><a href=\"?id=" . $id . "&page=" . $i . "\">" . $i . "</a></li>";
    } else {
        echo "<li><a class=\"active\" href=\"?id=" . $id . "&page=" . $i . "\">" . $i . "</a></li>";
    }

}
echo "<li>
        <a href=\"?id=" . $id . "&page=$totalPages\">&raquo;</a>
    </li>";
?>