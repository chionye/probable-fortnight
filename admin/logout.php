<?php
require_once '../includes/functions.php';

session_destroy();
redirect(ADMIN_URL . '/login.php');
