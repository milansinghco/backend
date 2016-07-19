<?php
error_reporting(E_ALL);
$port=9000;
$ip="127.0.0.1";
$len=1024;

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

if ($socket === false) {
    echo "socket_create() failed: reason: " . 
         socket_strerror(socket_last_error()) . "\n";
 }

if(!socket_bind($socket,$ip, $port)){
	echo "bind not ok";
}
echo "we are using this port".$port;
 while(1){

 if(socket_recvfrom($socket, $buff, 102400, 0, $ip, $port)){

  $myfile = fopen("data.txt", "a+") or die("Unable to open file!");
          fwrite($myfile, $buff);
          echo "mesg received";

 }

 
}

socket_close($socket);
?>