<?php
session_start();
echo json_encode($_SESSION['column_format_info'], JSON_PRETTY_PRINT);
