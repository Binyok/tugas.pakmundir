<?php
error_reporting(0);
session_start();
if (isset($_SESSION['akses']))
{
 if ($_SESSION['akses'] == "owner")
 {
 include 'location:petugas.php';
 }
 else if ($_SESSION['akses'] == "admin")
 {
 header('location:transaksi.php');
 }
}
?>