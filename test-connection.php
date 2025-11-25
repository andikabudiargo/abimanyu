<?php
$ip = '103.165.230.126';
$port = 4370;
$timeout = 5; // detik

$fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
if ($fp) {
    echo "Terhubung ke $ip:$port\n";
    fclose($fp);
} else {
    echo "Gagal koneksi ke $ip:$port ($errstr)\n";
}
?>
