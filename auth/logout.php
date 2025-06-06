<?php
require 'config.php';
session_destroy();
echo json_encode(['message' => 'Logged out']);
