<?php
// memulai session
session_start();
error_reporting(0);
if (isset($_SESSION['akses']))
{
// jika level admin
if ($_SESSION['akses'] == "owner")
 {
 header('location:http://localhost/PW1/petugas.php');
 }
 // jika kondisi level user maka akan diarahkan ke halaman lain
 else if ($_SESSION['akses'] == "admin")
 {
 header('location:admin.php');
 }
}
?>