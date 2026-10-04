<?php
require 'includes/auth_check.php';
csrf_verify();

session_destroy();
header("Location: login.php");
exit;