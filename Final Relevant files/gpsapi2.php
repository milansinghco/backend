

<?php
//93624df79b64abc2683cde964385d74b1fd251d6
//1467715372

require "connection.php";


$vehicles= array("6","7");
fetchData($vehicles,$conn);
function fetchData($vehicles,$conn){

while (true) {
  foreach ($vehicles as $vehicle) {
     gpsApi($vehicle,$conn);
  }
  sleep(30);
}





}
function gpsApi($vehicle,$conn){
$speed=22.2;
$imei=$vehicle;

$headers = array(
        "Authorization: Token 73119c60f13bf16885a353e257d0f98422adcfd1",
    );

       
   echo $address="http://core-dev.inloop.co.in/api/v1/vehicles/$vehicle/GPS_OBD_data/?current=TRUE";
         $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSLVERSION,3); 
        // Set some options - we are passing in a useragent too here 
        curl_setopt_array($curl, array( 
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $address, 
            CURLOPT_USERAGENT => 'PRASHANT VERMA' 
        )); 
        // Send the request & save response to $resp 
         $resp = curl_exec($curl); 
         if(!curl_exec($curl)){ 
            die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl)); 
         } 
         // Close request to clear up some resources 
        curl_close($curl); 
        $data = json_decode($resp, true); 
        print_r($data);
        $lat= $data["latitude"];
        $lon= $data["longitude"];
        $timestamp=$data['timestamp'];
        $yo=explode('T', $timestamp);
        $date=$yo[0];
        $yo2=explode('+', $yo[1]);
        $time=$yo2[0];
        
        echo $lat;
           if($time!=''){
            $istTime=$time;
              $sql2="Insert INTO LEVEL3_COLUMN_WISE_DATA(IMEI,TIMINGS,DATES,SPEED,LATITUDE,LONGITUDE) VALUES(:imei,:timing,:date,:speed,:lat,:lon)";
              $sql=$conn->prepare($sql2);
               $sql->bindParam(":imei",$imei);
               $sql->bindParam(":timing",$istTime);
               $sql->bindParam(":date",$date);
               $sql->bindParam(":speed",$speed);
               $sql->bindParam(":lat",$lat);
               $sql->bindParam(":lon",$lon);
               echo "first";
		$sql->execute();
		echo "second";
             }

           }
?>
<?php

 function convertAccrdIndTime($time){
            $tim=explode(":",$time);
            $nm=$tim[1]+30;
            $nhr=$tim[0]+5;
            $s=$tim[2];
            if($nm>=60){
                    $nhr=$nhr+1;
                    $nm=$nm-60;
                }
            if($nhr>23){
             $nhr=$nhr-24;
             }
         $indianTime="$nhr:$nm:$s";
        $indianTime1=date('H:i:s',strtotime($indianTime));
        return $indianTime1;

    }

?>
