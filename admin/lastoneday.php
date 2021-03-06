<?php
session_start();
ob_start();

include_once('heading.html');
include('jdf.php');

date_default_timezone_set("Asia/Tehran");

include_once('dbconn.php');

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
mysqli_set_charset($conn, 'utf8');
$sum_price_day = 0;
if (!$conn) {
    die("Connection failed : " . mysqli_error());
}
function getDayData($day_for_function, $month_for_function, $year_for_function)
{
    include("dbconn.php");
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    mysqli_set_charset($conn, 'utf8');
    if (!$conn) {
        die("Connection failed : " . mysqli_error());
    }

    $query = "SELECT * FROM `all_users` WHERE `day` = $day_for_function AND `month` = $month_for_function AND `year` = $year_for_function";
    $result = mysqli_query($conn, $query);

    return $result;
}



?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>آخرین کاربران روز</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css"/>

    <script src="js/jquery-2.1.1.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <style>

        body {
            font-family: "Iranian Sans";
        }

        table th {
            text-align: right;
        }

        table td {
            text-align: right;
        }

        .jumbotron {
            box-shadow: 0 8px 17px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 10px;
            margin-top: 5%;
        }


    </style>
</head>
<body>

<div class="container">
    <div style="padding: 1%" class="jumbotron">
        <h2 style="text-align: center">گزارش آماری در یک روز خاص</h2>
        <br>
    </div>
    <form method="get" action="">
        <div class="control-group">
            <p style="direction: rtl;" class="control-label" for="datepicker1">مشخص کردن یک روز خاص:</p>
            <div class="controls">
                <div class="input-append">
                    <input name="date" style="height: 34px" id="datepicker1" class="form-control" type="text">
                    <button id="datepicker1btn" class="btn" type="button"><i class="glyphicon glyphicon-calendar"></i></button>
                </div>
            </div>
        </div>
        <button name="btn-search1" style="width: 100%" class="btn btn-info" type="submit">جست و جو</button>
    </form>

    <br>
    <?php if (isset($_GET['btn-search1'])) { ?>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>وضعیت پرداخت</th>
                <th>ساعت خروج</th>
                <th>ساعت ورود</th>
                <th>شماره کامپیوتر</th>
                <th>هزینه</th>
                <th>شماره تلفن</th>
                <th>کد ملی</th>
                <th>نام خانوادگی</th>
                <th>نام</th>
                <th>پرداخت</th>
                <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] == '2'){ echo "<th>حذف</th>";} ?>
            </tr>
            </thead>
            <tbody>

            <?php
            if (isset($_GET['btn-search1'])){
                $date = $_GET['date'];
                if (isset($date) && !empty($date)){
                    $dater= explode('/', $date);
                    $day = $dater[0];
                    $month = $dater[1];
                    $year = $dater[2];
                }
            }
            $result = getDayData($day, $month, $year);
            $count = mysqli_num_rows($result);
            if ($count != 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $sum_price_day += $row['price'];
                    $pc_ip = $row['ip'];
                    $query2 = "SELECT `id` FROM `all_users` WHERE `ip` = '$pc_ip'";
                    $res = mysqli_query($conn, $query2);
                    $id = mysqli_fetch_assoc($res);
                    $des_ip = $row['ip'];
                    $q = "SELECT `id` FROM `ready_users` WHERE `ip` = '$des_ip'";
                    $r = mysqli_query($conn, $q);
                    $des_num = mysqli_fetch_assoc($r);
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo($row['pay'] == 1 ? '<i class="glyphicon glyphicon-check" style="color:green"></i>' : '<i class="glyphicon glyphicon-remove" style="color:red"></i>'); ?></td>
                        <td style="font-family: 'B Yekan';font-size: large"><?php echo $row['end_time']; ?></td>
                        <td style="font-family: 'B Yekan';font-size: large"><?php echo $row['start_time']; ?></td>
                        <td style="font-family: 'B Yekan';font-size: large"><?php echo $des_num['id']; ?></td>
                        <td style="font-family: 'B Yekan';font-size: large"><?php echo $row['price']; ?></td>
                        <td style="font-family: 'B Yekan';font-size: large"><?php echo $row['phone']; ?></td>
                        <td style="font-family: 'B Yekan';font-size: large"><?php echo $row['melli']; ?></td>
                        <td><?php echo $row['l_name']; ?></td>
                        <td><?php echo $row['f_name']; ?></td>
                        <td style="text-align: center"><a href="autopay.php?user_id=<?php echo $row['id']; ?>"
                                                          class="glyphicon glyphicon-shopping-cart"></a></td>
                    <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] == '2'){ ?>
                        <td style="text-align: center"><a href="autoremove.php?user_id=<?php echo $row['id']; ?>"
                                                          class="glyphicon glyphicon-remove"></a></td>
                    <?php } ?>
                    </tr>
                    <?php

                }
            }

            $result_for_is_day_exist = mysqli_query($conn, "SELECT `id` FROM `price` WHERE `day` = $day AND `month` = $month AND `year` = $year");
            $is_day_exist = mysqli_num_rows($result_for_is_day_exist);


            if ($is_day_exist == 0) {
                $result_for_insert_into_price = mysqli_query($conn, "INSERT INTO `price`(`day`, `month`, `year`, `price`) VALUES ('$day', '$month', '$year', $sum_price_day)");
            } else {
                $id_for_day = mysqli_fetch_array($result_for_is_day_exist)['id'];
                $result_for_update_price_table = mysqli_query($conn, "UPDATE `price` SET `price`= $sum_price_day WHERE `id` = $id_for_day");
            }

            ?>


            </tbody>
        </table>
        <h3 style="font-family: 'numsfont';direction: rtl;text-align: center">مجموع پرداختی امروز تا
            کنون: <?php echo $sum_price_day; ?></h3>
        <h3 style="font-family: 'numsfont';direction: rtl;text-align: center;margin-bottom: 5%">مجموع افراد امروز تا
            کنون: <?php echo $count; ?></h3>
        <?php
        if ($count == 0) {
            echo "<p style='text-align: right'>.گزارشی موجود نمی باشد</p>";
        }
    }
    ?>
</div>

<?php
include_once("footer.html");
?>
<script src="js/jquery-2.1.1.js"></script>

<script src="js/bootstrap-datepicker.js"></script>
<script src="js/bootstrap-datepicker.fa.js"></script>
<script>
    $(document).ready(function () {
        $("#datepicker1").datepicker();
        $("#datepicker2").datepicker();
        $("#datepicker3").datepicker();
        $("#datepicker1btn").click(function (event) {
            event.preventDefault();
            $("#datepicker1").focus();
        })
        $("#datepicker2btn").click(function (event) {
            event.preventDefault();
            $("#datepicker2").focus();
        })
        $("#datepicker3btn").click(function (event) {
            event.preventDefault();
            $("#datepicker3").focus();
        })


    });
</script>
</body>

</html>


</body>
</html>