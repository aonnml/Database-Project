<?php
session_start();
session_unset();
session_destroy();

// กลับไปหน้า home
header("Location: ../home.html");
exit();
?>
