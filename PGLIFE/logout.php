<?php
  session_start();
  session_destroy();
  header("location: /PGLIFE/index.php");
  exit();
?>
