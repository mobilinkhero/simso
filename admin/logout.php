<?php
require_once('../includes/AdminAuth.php');

AdminAuth::logout();

header('Location: login.php?logged_out=1');
exit;
?>