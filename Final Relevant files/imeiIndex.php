<?php
require 'connection.php';
//error_reporting(0);
?>

<?php
 $myresponse=array();
 if(isset($_POST['imei']) && !empty($_POST['imei'])&&isset($_POST['index']) && !empty($_POST['index'])){
     $value=$_POST['imei'];
     $index=$_POST['index'];
     //$imei=355801021693033;
     //$index=14;
     $tableName='LEVEL4VIEW';
     $sql = "SELECT * FROM $tableName WHERE ID>=:index AND IMEI=:value";

     $run = $conn->prepare($sql);
     $run->bindParam(":value",$value);
     $run->bindParam(":index",$index);

     $run->execute();
     //$dataa=array();
      while($data=$run->fetch()){
           //print_r($data);
            $id=$data['ID'];
            $imei=$data['IMEI'];
            $speed=$data['SPEED'];
            $time=$data['TIMING'];
            $dat=$data['DATES'];
     
         $lat= convertLat($data['LATITUDE']);
         $lon= convertLong($data['LONGITUDE']);
   
     array_push($myresponse, array('id'=>$id,'imei'=>$imei,'lat' =>$lat ,'lon'=>$lon,'speed'=>$speed,'dat'=>$dat));
 }

       
       
 


echo  json_encode(array("server_responses"=>$myresponse));
}
function convertLat($lati){

$latinew=str_split($lati);
    
     //echo $lati[0];
     $lat1=$latinew[0];
     $lat2=$latinew[1];
     $latL=$lat1.$lat2;
     $latR=$latinew[2].$latinew[3].$latinew[4].$latinew[5].$latinew[6].$latinew[7].$latinew[8];
     $latRR=$latR/60;

     $lati=$latL+$latRR;
     return $lati;
}

 function convertLong($longi){
if(isset($longi) && !empty($longi)){
$longi=ltrim($longi,'0');
$longinew=str_split($longi);
     $long1=$longinew[0];
     $long2=$longinew[1];
     $long3=$longinew[2];
     $longL=$longinew[0].$longinew[1];
     $longR=$longinew[2].$longinew[3].$longinew[4].$longinew[5].$longinew[6].$longinew[7].$longinew[8];
     $longiRR=$longR/60;
     $longi=$longL+$longiRR;
     return $longi;
   }
}
function convertTime($time){
    $addition=53000;
    $time=$time+$addition;
    return $time;
}

?>
