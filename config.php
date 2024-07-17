<?php
$con=mysqli_connect("localhost","root","","sandi");
// untuk menghubungkan database 
if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
