<?php
session_start();
echo isset($_SESSION['user']) ? '1' : '0';
?>
