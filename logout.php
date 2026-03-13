<?php
// logout.php
require_once 'includes/functions.php';

session_unset();
session_destroy();

header('Location: index.php');
exit;
?>