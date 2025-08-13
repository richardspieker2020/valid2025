<?php
if (!isset($_GET['email'])) {
    die("No email provided");
}

$email = $_GET['email'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

list($user, $domain) = explode('@', $email);

// 1. MX record check
if (!getmxrr($domain, $mxhosts)) {
    die("Domain has no MX records - cannot receive email.");
}

// 2. Try SMTP connection
$connect = @fsockopen($mxhosts[0], 25, $errno, $errstr, 10);
if (!$connect) {
    die("Could not connect to mail server: $errstr");
}

fputs($connect, "HELO test.com\r\n");
fputs($connect, "MAIL FROM:<test@test.com>\r\n");
fputs($connect, "RCPT TO:<$email>\r\n");
$data = fgets($connect, 1024);
fputs($connect, "QUIT\r\n");
fclose($connect);

echo "Server response: " . $data;
?>