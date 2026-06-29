<?php
header('Content-Type: text/plain');
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . (isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown') . "\n";
?>
