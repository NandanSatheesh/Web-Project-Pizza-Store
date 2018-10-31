<?php

    require_once '../php_utils/db_connection.php';

    if(session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if(!isset($_SESSION["admin"])) {
        header("location: ../users/userlogin.php");
        die();
    }

    $conn = connect();

    if(isset($_POST["cancel"])){
      $delete= $conn->prepare("delete from admin_notification where email=?");
      $delete->bind_param("s", $_SESSION["admin"]);
      $delete->execute();
      $delete->close();
      $_POST= array();
    }


    $stmt = $conn->prepare("SELECT * FROM admin_notification WHERE email=? ORDER BY time_stamp DESC");
    $email = $_SESSION["admin"];
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

 ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="author" content="PS">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="description" content="Online Pizza Delivery">
        <meta name="keywords" content="online,pizza,pasta">
        <link rel="icon" href="../favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../styles/customBgJumbo.css">
        <link rel="stylesheet" type="text/css" href="../styles/home.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
        <script src="../scripts/smoothScrolling.js"></script>
        <link rel="stylesheet" type="text/css" href="../styles/account.css">
        <link rel="stylesheet" type="text/css" href="../styles/notifiche.css">
        <!--<script src="../scripts/notifications.js"></script> -->

        <title>Notifications</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a href="#top" class="navbar-brand">Pizza ASAP!</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse justify-content-stretch" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="adminWelcome.php">Home  <span class="fas fa-home"></span></a>
                    </li>
                  <li>
                    <a class="nav-link" href="settings.php">Settings  <span class="fas fa-wrench"></span></a>
                </li>
                <li class="nav-item">
                    <input type="hidden" id="mail-field" value="<?= $_SESSION["admin"]?>">
                    <input type="hidden" id="is-admin" value="true">
                    <a class="nav-link active" href="#top">Notifications  <span class="fas fa-bell" id="bell-icon"></span><span class="hidden badge badge-pill badge-danger" id="bell-count">0</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../php_utils/logout.php">Logout  <span class="fas fa-sign-out-alt"></span></a>
                </li>
                </ul>
            </div>
        </nav>
        <div id="wrapper">
            <div id="header">
                <div class="bg">
                    <div class="jumbotron jumbotron-fluid">
                        <h1 class="display-1" id="top">Notifications</h1>
                    </div>
                </div>
            </div>
            <div class="content container">
                <div class="container-fluid bg-grey rounded">
                 <form action= "notifications.php" method= "post">
                    <h2>Notifications</h2>
                    <hr class="mb-4">
                        <div class="notifications-container">
                        <?php
                            if($res->num_rows == 0) {
                                echo '
                                <div class="row text-center">
                                    <div class="col-md-12 mb-4">
                                        <span class="text-muted">You have no notifications :(</span>
                                    </div>
                                </div>';
                            }
                            else {
                                while($row = $res->fetch_assoc()) {
                                    $query = "UPDATE admin_notification SET new=false WHERE ID=".$row["ID"];
                                    $conn->query($query);
                                    echo
                                    '<div class="row bg-lightblue notification-row">
                                        <div class="col-md-1 align-self-center icon-col">
                                            <span class="fas fa-exclamation"></span>
                                        </div>
                                        <div class="col-md-9 align-self-center">
                                            <span class="notification-text">'.$row["description"].'</span>
                                        </div>
                                        <div class="col-md-2 align-self-center">
                                            <span class="notification-date">'.$row["time_stamp"].'</span>
                                        </div>
                                    </div>';
                                }
                            }
                            $conn->close();
                        ?>
                        <div class= "row text-center">
                          <div class= "col-md-12 align-self-center">
                            <input type= "submit" value= "Clear All Notifications" class= "btn btn-danger">
                          </div>
                        </div>
                    </div>
                    <input type= "hidden" id= "cancel" value= "cancel" name= "cancel">
                  </form>
                </div>
            </div>
            <div id="footer">
                <footer class="container-fluid text-center">
                    <div class="row align-item-center footer-row">
                        <div class="col-md-12 align-self-center">
                            <a href="#top" title="To Top">
                                <span class="fas fa-chevron-up" style="color:white"></span>
                            </a>
                        </div>
                    </div>
                    <div class="row align-item-center">
                        <div class="col-md-12 align-self-center">
                            <p class="copyright">
                                © PizzaASAP! Inc., 2018
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
