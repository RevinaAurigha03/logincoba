<?php
session_start();
include_once('config.php');          //mengimport file konfigurasi           
$msg = '';
if (isset($_POST['submit'])) {      
    $time = time() - 1800;          // menghitung waktu 30 menit yang lalu
    $ip_address = getIpAddr();
    // Mendapatkan alamat IP pengguna
    $query = mysqli_query($con, "select count(*) as total_count from loginlogs where TryTime > $time and IpAddress='$ip_address'");
    $check_login_row = mysqli_fetch_assoc($query);
    $total_count = $check_login_row['total_count'];
    //Memeriksa apakah percobaannya 3 kali 
    if ($total_count == 3) {    //Memeriksa jika sudah ada upaya gagal
        $msg = "To many failed login attempts. Please login after 30 minute";
    } else {
        //Getting Post Values
        $username = $_POST['username'];
        $password = md5($_POST['password']);    // Mengenkripsikan kata sandi dengan type MD5
        // Coding for login
        $res = mysqli_query($con, "select * from user where username='$username' and password='$password'");
        if (mysqli_num_rows($res)) {
            $_SESSION['IS_LOGIN'] = 'yes';
            mysqli_query($con, "delete from loginlogs where IpAddress='$ip_address'");
            echo "<script>window.location.href='dashboard.php';</script>";
        } else {        // Jika krendensialnya salah
            $total_count++;
            $rem_attm = 3 - $total_count;
            if ($rem_attm == 0) {
                $msg = "To many failed login attempts. Please login after 30 minute";
            } else {
                $msg = "Please enter valid login details.<br/>$rem_attm attempts remaining";
            }
            $try_time = time();
            mysqli_query($con, "insert into loginlogs(IpAddress,TryTime) values('$ip_address','$try_time')");
        }
    }
}
// Fungsi untuk mendapatkan alamat ip pengguna
function getIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipAddr = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ipAddr = $_SERVER['REMOTE_ADDR'];
    }
    return $ipAddr;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <title>Login Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">
        body {
        margin: 0;
        padding: 0;
        background-image: url('background.jpg'); 
        background-size: cover; /* Untuk mengisi seluruh latar belakang */
        background-repeat: no-repeat; 
        background-attachment: fixed; 
        height: 100vh;
    
        }

        #login .container #login-row #login-column #login-box {
            margin-top: 60px;
            max-width: 600px;
            height: 320px;
            border: 1px solid #9C9C9C;
            background-color: #EAEAEA;
        }

        #login .container #login-row #login-column #login-box #login-form {
            padding: 40px;
        }

        #result {
            color: orange;
        }
    </style>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
</head>

<body>

    <body>
        <div id="login">
            <h3 class="text-center text-white pt-5">Login form</h3>
            <div class="container">
                <div id="login-row" class="row justify-content-center align-items-center">
                    <div id="login-column" class="col-md-6">
                        <div id="login-box" class="col-md-12">
                            <form id="login-form" class="form" method="post">
                                <div class="form-group">
                                    <label for="username" class="text-info">Username:</label><br>
                                    <input type="text" name="username" id="username" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="text-info">Password:</label><br>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="submit" class="btn btn-info btn-md" value="Submit">
                                </div>
                                <div id="result">
                                    <?php echo $msg ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>