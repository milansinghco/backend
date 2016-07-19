<?php

/*



>Change the  key of duration api..two keys are used here ..one for all other one only for the duration api
>make a variable of every  value used in program
>make a variable of version of ssl

















*/
require 'connection.php';
//  error_reporting(0);
require'gcm_mainServer.php';

date_default_timezone_set('Asia/Kolkata');

//error_reporting(0);
/**
* convert all database colllumns to...pid,pimei..
*/
class allFunction
{
  public $noOfLatLon=5;
  
  
  public function previousLatLon($id,$imei,$conn){
    echo "previous lat";
   $noOfLatLon=1;
   $noOfLatLonCount=7;
         
    $latLons = array();
    $latLon=array();   
    $index=$id;
    $count=1;
    $tablename='LEVEL3_COLUMN_WISE_DATA';    //change the table name according to your database
    $minimumIndex=6590;          //minimum index number of the table ..basically it will be one
    if($index>=$noOfLatLon){
      while(($count<$noOfLatLonCount) AND ($index>$minimumIndex)){//if no data is till end of the view
        
        $sql="SELECT * FROM $tablename WHERE ID=:index";
        $run=$conn->prepare($sql);
        $run->bindParam(':index',$index);
        //$run->bindParam(':imei',$imei);
        $run->execute();
        $data= $run->fetch();
            
        if($imei==$data['IMEI']){
                  array_push($latLons,array('lat' => $data['LATITUDE'],'lon' => $data['LONGITUDE'] ));

          $count++;
          $index=$data['ID']-1;


        }else{
          $index=$data['ID']-1;
        }


      }

        echo "count=".$count;
      if($count<=3){ 
         array_push($latLons,array('lat' =>32.771461,'lon' =>74.83249 ));
          array_push($latLons,array('lat' =>32.771461,'lon' =>74.83249 ));
        array_push($latLons,array('lat' =>32.771461,'lon' =>74.83249 ));
         array_push($latLons,array('lat' =>32.771461,'lon' =>74.83249 ));
         array_push($latLons,array('lat' =>32.771461,'lon' =>74.83249 ));



       }
      $data=json_encode(array('latLon'=>$latLons));
      //print_r($data);
      return $data;

  

  } 
  
}

    public function fetchLastStoppageOfRoute($routeId,$conn){
    $sql3 ="SELECT * FROM PATH WHERE ROUTE_ID=:routeId ORDER BY ID DESC"; // change INDEX  coloumn name to id in path table
        $run2=$conn->prepare($sql3);
        $run2->bindParam(":routeId",$routeId);
        $run2->execute();
        $row=$run2->fetch();
        $lastStooppageId=$row['STOPPAGE_ID'];
        return $lastStooppageId;
       
       
    }


     public function fetchStoppageLatLon($stoppageId,$conn){
        $sql3 ="SELECT * FROM STOPPAGE WHERE STOPPAGE_ID=:stoppageId"; // change INDEX  coloumn name to id in path table
          $run2=$conn->prepare($sql3);
          $run2->bindParam(":stoppageId",$stoppageId);
          $run2->execute();
          $row=$run2->fetch();

          $lat=$row['LATITUDE'];
          $lon=$row['LONGITUDE'];
          $lastStoppageLatLon=array("lat"=>$lat,"lon"=>$lon);
          return $lastStoppageLatLon;
        }
      
     public function updateAllStoppagesZero($routeId,$conn){
      $value=0;
      $sql = "UPDATE PATH SET STOPPAGE_CHECK=:value WHERE ROUTE_ID=:routeId";
     $run=$conn->prepare($sql);
     $run->bindParam(":value",$value);
     $run->bindParam(":routeId",$routeId);
     $run->execute();
         }

   public function fetchWayPoints($routeId,$stoppageId,$conn){
       $value=0;
       $destStoppageIndex=$this->findIndexOfStoppageId($routeId,$stoppageId,$conn);
          
     $sql2="SELECT STOPPAGE.LATITUDE,STOPPAGE.LONGITUDE,STOPPAGE.STOPPAGE_ID FROM STOPPAGE INNER JOIN PATH ON STOPPAGE.STOPPAGE_ID=PATH.STOPPAGE_ID WHERE PATH.ROUTE_ID=:routeId AND PATH.ID<:id AND PATH.STOPPAGE_CHECK=:value ORDER BY PATH.ID";
     $run=$conn->prepare($sql2);
     $run->bindParam(":id",$destStoppageIndex);
     $run->bindParam(":value",$value);
     $run->bindParam(":routeId",$routeId);
     $run->execute();
     $path='';
     $count=0;
     while($row=$run->fetch()){
       $count=$count+1;
       $path=$path.$row['LATITUDE'].",".$row['LONGITUDE']."|";

     }
     $waypoint=rtrim($path,'|');
     $waypoints=array("waypoint"=>$waypoint,"count"=>$count);
     return $waypoints;
  }
    public function findIndexOfStoppageId($routeId,$stoppageId,$conn){
      $sql = "SELECT * FROM PATH WHERE  STOPPAGE_ID=:stoppageid AND ROUTE_ID=:routeId";
       $run=$conn->prepare($sql);
       $run->bindParam(":stoppageid",$stoppageId);
       $run->bindParam(":routeId",$routeId);
       $run->execute();
       $row=$run->fetch();
       $stoppageIndex=$row['ID'];
       return $stoppageIndex;
     }

    public  function  calculateEta($source,$destination,$routeId,$stoppageId,$key,$conn){

        $waypoints=$this->fetchWayPoints($routeId,$stoppageId,$conn);
        $count=$waypoints['count'];
        $waypoint=$waypoints['waypoint'];
         if($waypoint==''||empty($waypoint)){
            $eta=$this->distancematrixApi($source,$destination,$key);
         }else{
                $eta=$this->durationApi($source,$destination,$waypoint,$count,$key);
         }
        return $eta;
        }

        
    public function durationApi($source,$destination,$waypoints,$count,$key){

       $key="AIzaSyCNSints8lLF9SoyeYO8TnmyTweuGIK9q8";
     echo $address="https://maps.googleapis.com/maps/api/directions/json?origin=$source&destination=$destination&waypoints=$waypoints&key=$key";
         $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($curl, CURLOPT_SSLVERSION,1.3); 
        curl_setopt_array($curl, array( 
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $address, 
            CURLOPT_USERAGENT => 'PRASHANT VERMA' 
        )); 
        $resp = curl_exec($curl); 
         if(!curl_exec($curl)){ 
            die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl)); 
         } 
        curl_close($curl); 
        //print_r($resp);
        $data = json_decode($resp, true); 
       // print_r($data);
      
       $duration=0;
        for ($i=0; $i <=$count; $i++) { 
          # code...
        echo "\nduration".$data["routes"][0]["legs"][$i]["duration"]["value"];
        $duration=$duration+$data["routes"][0]["legs"][$i]["duration"]["value"];
    }
    return $duration;
    }

    public  function distancematrixApi($source,$destination,$key) {
      $key="AIzaSyCNSints8lLF9SoyeYO8TnmyTweuGIK9q8";
        $address ="https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$source&destinations=$destination&KEY=$key";
       
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($curl, CURLOPT_SSLVERSION,1.3); 
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
        $duration=$data["rows"][0]["elements"][0]["duration"]["value"];
          return $duration;
        


  }

  public  function calculateEtaa($source,$destination,$key) { 
        $address ="https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$source&destinations=$destination&KEY=$key";
       
             $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($curl, CURLOPT_SSLVERSION,1.3); 
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
        echo"\n aarray of previously fetched latlons=";
        print_r($data);
          return $data;
        //$distance=$arr["rows"][0]["elements"][0]["distance"]["value"];
        //$duration=$arr["rows"][0]["elements"][0]["duration"]["value"];
              
     //   foreach ($arr as $ar) {
      //       echo $ar, "\n";
          // }
        //echo $arr;
       //var_dump($arr);
           //echo $resp->rows;



  }


     public   function snapToRoad($jsonOfPOints,$key){
              $latLons=$jsonOfPOints;
             $latLon=json_decode($latLons);
         //print_r($latLon);
        $path='';
      for($i=0;$i<5;$i++){
        $lat= $this->convertLat($latLon->latLon[$i]->lat);
        $lon=$this->convertLong($latLon->latLon[$i]->lon);
        $path=$lat.','.$lon.'|'.$path;

        }
         $path=ltrim($path,',|');
         $path=rtrim($path,'|');
         $snapped=array();
         echo "\nsnapto road = ".$address = "https://roads.googleapis.com/v1/snapToRoads?path=$path&interpolate=true&key=$key";
         //$address=  "https://roads.googleapis.com/v1/snapToRoads?path=28.622009,77.248134|28.621569,77.248086|28.621857,77.248010|28.622041,77.247940|28.622399,77.247838&interpolate=true&key=AIzaSyCNSints8lLF9SoyeYO8TnmyTweuGIK9q8";
              $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($curl, CURLOPT_SSLVERSION,1.3); 
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
        $len = count($data["snappedPoints"]);
           print_r($data);
            $snappedLat=$data["snappedPoints"][$len-1]["location"]["latitude"];
            $snappedLon=$data["snappedPoints"][$len-1]["location"]["longitude"];

            
            $snapped = array('lat' =>$snappedLat ,'lon'=>$snappedLon);
            return $snapped;
        // $distance=$arr["rows"][0]["elements"][0]["distance"]["value"];
        // $duration=$arr["rows"][0]["elements"][0]["duration"]["value"];
              




       }

       public function geoFence($sourceLat,$sourceLon,$destLat,$destLon){
                                          echo"st".$sourceLat;
                                        echo"st".$sourceLon;echo"st".$destLat;echo"st".$destLon;
                    $diffLat=$sourceLat-$destLat;
                    $diffLon=$sourceLon-$destLon;
                     $r=(111*111);
                    $value=($r*$diffLat*$diffLat)+($r*$diffLon*$diffLon);
                     return $value;




       }

   public function nextStoppage($imei,$time,$conn){
    // $stTime = new DateTime($time);
    // $stTime->add(new DateInterval('P0Y0M0DT5H30M0S'));
    // $istTime= $stTime->format('H:i:s');
      echo $time;
      $tim=explode(":",$time);
      echo $nm=$tim[1]+30;
         $nhr=$tim[0]+5;
                $s=$tim[2];
              if($nm>=60){
                 $nhr=$nhr+1;
                  $nm=$nm-60;
            }
            if($nhr>23){
              $nhr=$nhr-24;
                }
// echo $nhr;
// echo $nm;
// echo $s;


     echo"ist". $indianTime="$nhr:$nm:$s";
         $indianTime= date('H:i:s',strtotime($indianTime));
    $sql1="SELECT * FROM BUS WHERE IMEI=:imei";
    $run1=$conn->prepare($sql1);
    $run1->bindParam(":imei",$imei);
    $run1->execute();
    $row1=$run1->fetch();
    echo"busId". $busId=$row1['BUS_ID'];  
    $sql ="SELECT * FROM LINK WHERE BUS_ID=:busId AND START_ROUTE_TIME<=:timee AND STOP_ROUTE_TIME>=:timeee";
    
    $run=$conn->prepare($sql);
    $run->bindParam(":busId",$busId);
    $run->bindParam(":timee",$indianTime);
    $run->bindParam(":timeee",$indianTime);
    $run->execute();
    $row=$run->fetch();
   echo " routeid= ".$routeId=$row['ROUTE_ID'];

    $lastStoppage=$row['LAST_STOPPAGE'];
    $sql2 ="SELECT * FROM PATH WHERE STOPPAGE_ID=:stopgId AND ROUTE_ID=:routeid";
    $run2=$conn->prepare($sql2);
    $run2->bindParam(":stopgId",$lastStoppage);
    $run2->bindParam(":routeid",$routeId);

    $run2->execute();
    $row=$run2->fetch();
    $lastStoppageIndex=$row['ID'];
    if(is_null($lastStoppageIndex) OR empty($lastStoppageIndex)){
      $sql3 ="SELECT * FROM PATH  WHERE  ROUTE_ID=:routeId  ORDER BY ID ASC" ;  //add orser by
        $run2=$conn->prepare($sql3);
      $run2->bindParam(":routeId",$routeId);
      $run2->execute();
      $row=$run2->fetch();
       }else{
            $sql3 ="SELECT * FROM PATH WHERE ROUTE_ID=:routeId AND ID >:index ORDER BY ID ASC"; // change INDEX  coloumn name to id in path table
            $run2=$conn->prepare($sql3);
        $run2->bindParam(":routeId",$routeId);
        $run2->bindParam(":index",$lastStoppageIndex);
        $run2->execute();
        $row=$run2->fetch();
       }

       
    
   echo "stoppageId= ". $stoppageId=$row['STOPPAGE_ID'];
    //die();
    $sql2 ="SELECT * FROM STOPPAGE WHERE STOPPAGE_ID=:stoppageId ";
    $run2=$conn->prepare($sql2);
    $run2->bindParam(":stoppageId",$stoppageId);
    $run2->execute();
    $row=$run2->fetch();
    $stoppageLat=$row['LATITUDE'];
    $stoppageLon=$row['LONGITUDE'];
    $stoppage=array();
        $stoppage = array('lat' =>$stoppageLat ,'lon'=>$stoppageLon,'routeId'=>$routeId,'stoppageId'=>$stoppageId);
        
        
          return $stoppage;
        
  }

 function updateLastStoppage($routeId,$stoppageId,$imei,$time,$conn){
 $remainingStoppagesCount=0;
 echo $time;
 $tim=explode(":",$time);
 echo $nm=$tim[1]+30;
 $nhr=$tim[0]+5;
 $s=$tim[2];
 if($nm>=60){
 $nhr=$nhr+1;
 $nm=$nm-60;
 }
 if($nhr>23){
       $nhr=$nhr-24;
       }
 echo "R".$routeId;
 echo"S".$stoppageId;
 echo"ist". $indianTime="$nhr:$nm:$s";
         $indianTime=date('H:i:s',strtotime($indianTime));
 $sql ="SELECT * FROM LINK WHERE ROUTE_ID=:routeid";
 $run=$conn->prepare($sql);
 $run->bindParam(":routeid",$routeId);
 $run->execute();
 $row=$run->fetch();
 $startTime=$row['START_ROUTE_TIME'];
 echo"st".$stopTime=$row['STOP_ROUTE_TIME'];
 echo"noti".$notificationCheck=$row['NOTIFICATION_CHECK'];
 echo"LAST".$lastStoppage=$stoppageId;
 $sql2 ="SELECT * FROM PATH WHERE STOPPAGE_ID=:stopgId AND ROUTE_ID=:routeid";
    $run2=$conn->prepare($sql2);
    $run2->bindParam(":stopgId",$lastStoppage);
    $run2->bindParam(":routeid",$routeId);
    $run2->execute();
    $row=$run2->fetch();
    echo "  ll=".$lastStoppageIndex=$row['ID'];
 $sql3 ="SELECT * FROM PATH WHERE ROUTE_ID=:routeId AND ID >:index ORDER BY ID ASC";// change INDEX coloumn name to id in path table
 $run2=$conn->prepare($sql3);
 $run2->bindParam(":routeId",$routeId);
 $run2->bindParam(":index",$lastStoppageIndex);
 $run2->execute();
 //print_r($run2);
 while($row=$run2->fetch()){
  echo"remainstoppage".$row['STOPPAGE_ID'];
 $remainingStoppagesCount++;
 echo "countremain=".$remainingStoppagesCount;

 }
 if($remainingStoppagesCount==0){
 //if($notificationCheck==1){
 $v=null;

 
 // $sql = "UPDATE LINK SET LAST_STOPPAGE=:stoppageId WHERE ROUTE_ID=:routeId";
 // $run=$conn->prepare($sql);
 // $run->bindParam(":stoppageId",$stoppageId);
 // $run->bindParam(":routeId",$routeId);
 // $run->execute();
 $sql = "UPDATE LINK SET LAST_STOPPAGE=:stoppageId,NOTIFICATION_CHECK=:notification WHERE ROUTE_ID=:routeId";
 $setNotification=2;
 $run=$conn->prepare($sql);
 $run->bindParam(":stoppageId",$v);
 $run->bindParam(":notification",$setNotification);
 $run->bindParam(":routeId",$routeId);
 $run->execute();

 }else{
 $sql = "UPDATE LINK SET LAST_STOPPAGE=:stoppageId WHERE ROUTE_ID=:routeId";
 $run=$conn->prepare($sql);
 $run->bindParam(":stoppageId",$stoppageId);
 $run->bindParam(":routeId",$routeId);
 $run->execute();


 }
//}
 
 }
 



  public   function convertLat($lati){
    if(isset($lati) && !empty($lati)){
     //$latinew=str_split($lati);
    
     //echo $lati[0];
     //$lat1=$latinew[0];
     //$lat2=$latinew[1];
     //$latL=$lat1.$lat2;
     //$latR=$latinew[2].$latinew[3].$latinew[4].$latinew[5].$latinew[6].$latinew[7].$latinew[8];
     //$latRR=$latR/60;

     //$lati=$latL+$latRR;
     
     return $lati;
   }
}

public function convertLong($longi){
if(isset($longi) && !empty($longi)){
//$longi=ltrim($longi,'0');
//$longinew=str_split($longi);
     //$long1=$longinew[0];
     //$long2=$longinew[1];
     //$long3=$longinew[2];
     //$longL=$longinew[0].$longinew[1];
     //$longR=$longinew[2].$longinew[3].$longinew[4].$longinew[5].$longinew[6].$longinew[7].$longinew[8];
     //$longiRR=$longR/60;
     //$longi=$longL+$longiRR;
     return $longi;
   }
}

public function sendNotification(){

   $check=1;
   echo "notifiaction sent";
   return 1;
}


 


}
$key="AIzaSyDsZqRbqR-a_wm31LeHP643bqZOvn9N9T0";
$distanceLimit=16;
$timeLimit=600;

// $previousLatLon=new allFunction;
// $sLat='28.622399';
// $sLon='77.247838';
// $source=$sLat.','.$sLon;
// $dLat='24.622041';
// $dLon='77.247940'; 
// $destination=$dLat.','.$dLon;
// $eta=$previousLatLon->calculateEta($source,$destination,$key);

// print_r($eta);

// echo " \n ";

// $latLons=$previousLatLon->previousLatLon('23','355801021693033',$conn);
// $snappedPoints=$previousLatLon->snapToRoad($latLons,$key);
// echo $snappedLat=$snappedPoints['lat'];
// echo $snappedLon=$snappedPoints['lon'];
// $source=$snappedLat.','.$snappedLon;
 $index=13990;// change the index value according to your database
$busStartTime=80000;
$busSpeed=15/1.852;
$table="LEVEL3_COLUMN_WISE_DATA";
$obj = new allFunction;
$flagCheckDatabase=1;
$echoOnlyOneTime=0;
while($flagCheckDatabase){ //continue checking if database dont have any row
$sql="select count(*) from $table ";
  $run=$conn->prepare($sql);
 $run->execute();
 $row1=$run->fetch();
     $flagg=$row1[0];
     if($flagg==0 && $echoOnlyOneTime==0){
       $echoOnlyOneTime=1;
      echo" \n NO DATA IN TABLE $table";
     }
     if($flagg>=1){
      $flagCheckDatabase=0;
     }
   }

while($flagg){
   
   $sql1="SELECT * FROM  $table WHERE ID=:index";
    $run1=$conn->prepare($sql1);
    $run1->bindParam(":index",$index);
    $run1->execute();
    $row1=$run1->fetch();
              $date= date("h:i:s");
              echo "scanning time=". $date;

             $speed=$row1['SPEED'];
            echo  $time=$row1['TIMINGS'];
             echo"slat= ". $sourceLat=$obj->convertLat($row1['LATITUDE']);
              echo"slon=". $sourceLon=$obj->convertLong($row1['LONGITUDE']);
           
             $id=$row1['ID'];
             $imei=$row1['IMEI'];
               
             echo"t".$time;
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
            
            echo " time=".$indianTime="$nhr:$nm:$s";

                echo"tim=  ". $tim= date('H:i:s',strtotime($indianTime));
               // echo $tim="";
             //die();         strtotime(
            $sql1="SELECT * FROM BUS WHERE IMEI=:imei";
             $run1=$conn->prepare($sql1);
            $run1->bindParam(":imei",$imei);
            $run1->execute();
            $row1=$run1->fetch();
            echo"busId". $busId=$row1['BUS_ID'];  
             $sql ="SELECT * FROM LINK WHERE BUS_ID=:busId AND START_ROUTE_TIME<=:timee AND STOP_ROUTE_TIME>=:timeee";
            $run=$conn->prepare($sql);
            $run->bindParam(":busId",$busId);
            $run->bindParam(":timee",$tim);
            $run->bindParam(":timeee",$tim);
            $run->execute();
            $row=$run->fetch();
           $busStartTime=$row['START_ROUTE_TIME'];
                              $stopTime=$row['STOP_ROUTE_TIME'];
                              $notificationCheck=$row['NOTIFICATION_CHECK'];
                       echo " routeid= ".$routeId=$row['ROUTE_ID'];
                             
           if(($tim>$stopTime)||($tim<$busStartTime)){
             $sql = "UPDATE LINK SET LAST_STOPPAGE=:stoppageId,NOTIFICATION_CHECK=:notification WHERE ROUTE_ID=:routeId";
             $setNotification=0;
             $run=$conn->prepare($sql);
              $run->bindParam(":stoppageId",$v);
              $run->bindParam(":notification",$setNotification);
               $run->bindParam(":routeId",$routeId);
             $run->execute();
                 }


           if(($tim>$busStartTime)&&($tim<$stopTime)&&($notificationCheck==0))
           {
             $notificationCheck=1;
             $sql = "UPDATE LINK SET NOTIFICATION_CHECK=:notificationCheck WHERE ROUTE_ID=:routeId";
             $run=$conn->prepare($sql);
             $run->bindParam(":notificationCheck",$notificationCheck);
             $run->bindParam(":routeId",$routeId);
             $run->execute();
           }
             
            if($notificationCheck==1){
              $nextStoppage=$obj->nextStoppage($imei,$time,$conn);
             echo $destinationLat=$nextStoppage['lat'];
             echo "  ".$destinationLon=$nextStoppage['lon'];//stoppage laTlons will be in google format
             $routeId=$nextStoppage['routeId'];
           echo  $stoppageId=$nextStoppage['stoppageId'];
           if(!is_null($routeId)|| !empty($routeId)){// checking for route id
            //  if($check[$imei]!=1){
            //    if($speed>=($busSpeed)){   
                 //echo '>';  
                 /*checking ...speed of the bus is greater than 15 or not*/
               //  $check[$imei]=1;
              //   $sql = "UPDATE LINK SET TRIP_CHECK=1 WHERE ROUTE_ID=:routeId";
               //  $run=$conn->prepare($sql);
              //   $run->bindParam(':routeId',$routeId);
                // $run->execute();
                  
           // }

         // }
               
            echo "journey started";
       
             
             echo "\ng1=".$geoFence=$obj->geoFence($sourceLat,$sourceLon,$destinationLat,$destinationLon);
             if($geoFence<$distanceLimit){ // both  are in km badically for 4km
                echo "\n first geo is true";
                echo "\nSource1".$source=$sourceLat.','.$sourceLon;
                echo "\nDESTi=".$destination=$destinationLat.','.$destinationLon;
                      $data=$obj->calculateEta($source,$destination,$routeId,$stoppageId,$key,$conn);
                      //print_r($data);
                      
                    // $distance=$data["rows"][0]["elements"][0]["distance"]["value"];
                      echo "\neta1=".$duration=$data;
                      //die();
                  if($duration<$timeLimit|| $geoFence<0.25){ //duration is in secs and $geo in KM
                            echo "\n secnd geo is true";
                             echo "idimei ".$id;
                            $latLons=$obj->previousLatLon($id,$imei,$conn);
                           $snappedPoints=$obj->snapToRoad($latLons,$key);
                           print_r($snappedPoints);
                            $sourceLat=$snappedLat=$snappedPoints['lat'];
                           $sourceLon=$snappedLon=$snappedPoints['lon'];
                           echo "\nsource2=". $source=$sourceLat.','.$sourceLon;
                           echo "\ng2=".$geoFence=$obj->geoFence($sourceLat,$sourceLon,$destinationLat,$destinationLon);
                            $dataEta = $obj->calculateEta($source,$destination,$routeId,$stoppageId,$key,$conn); 
                            //print_r($dataEta);
                             
                          // $data=json_decode($data);

                          //$distance=$data["rows"][0]["elements"][0]["distance"]["value"];
                            echo "\neta2=".$duration=$dataEta;
                           //echo $geoFence;
                               //echo "eta2=".$duration=200;
                            if($duration<$timeLimit|| $geoFence<0.25){ //duration is in secs and $geo in KM
                               $date= date("h:i:s");
                               echo "eta2 time=". $date;
                               echo "packet time=".$indianTime;
                              echo "\n snapped eta is true for notification";
                              $regIds= registrationId($stoppageId,$conn);
                              //print_r($regIds);
                              //$regIds=json_encode($regIds);
                              //print_r($regIds);
                               $nottype='default';  //........secs
                              $durationMins=round($duration/60,0);
                              $pushMessage="The bus will arrive at your stop in $durationMins mins";
                              $pushMessage = html_entity_decode($pushMessage);
                              $message = array($nottype => $pushMessage);
                              $messgae=json_encode($message);
                              $regIdChunk = array_chunk($regIds,1000);
                                 
                              foreach ($regIdChunk as $registration_ids) {
                                echo "\n notification function called";
                                echo "msgto=".$registration_ids;
                                //echo"msg sent";
                                $pushStatus=sendPushNotification($registration_ids,$message);// send notification
                                echo"\n not status=". $pushStatus;

                            }
                            
                              $contactIdchunk = contactIds($stoppageId,$conn);
			      require 'sendmessage.php';


                              //check what is $notify and update last stoppage
                                echo "stop".$stoppageId;
                                $date= date("h:i:s");
                                echo "notification time=". $date;

                                $obj->updateLastStoppage($routeId,$stoppageId,$imei,$time,$conn);


                               


                            }  else{
                              echo"not sent";
                            }                                           
                  } 

                   //


              }

            
      }
    }
//if($imei)
    $flag=1;
    while($flag){ // if theeree is no  row prsent then dont increase the loop
    $indx=$index;
    $indx++;
    $sql1="SELECT count(ID) FROM  $table WHERE ID>=:index";
    $run1=$conn->prepare($sql1);
    $run1->bindParam(":index",$indx);
    $run1->execute();
    $row1=$run1->fetch();
    if($row1[0]>=1) {        
    $index++;
    $flag=0;
 }
}


}

 function contactIds($stoppageId,$conn)
  {
        $run1=$conn->prepare("Select PARENT.SMS_NUMBER from CHILD join PARENT_OF on CHILD.C_ID=PARENT_OF.C_ID join PARENT on PARENT_OF.P_ID=PARENT.PHONE_NUMBER WHERE CHILD.MORNING_STOPPAGE_ID = :stop OR CHILD.EVENING_STOPPAGE_ID =:stop  GROUP BY PARENT_OF.P_ID");
        $run1->bindParam(":stop",$stoppageId);
        $run1->execute();
        $runres = $run1->fetchAll();
        return $runres;
  }


function registrationId($stoppageId,$conn){


        $sql1="SELECT * FROM CHILD WHERE MORNING_STOPPAGE_ID=:stoppageId";
        $run1=$conn->prepare($sql1);
        $run1->bindParam(":stoppageId",$stoppageId);
        $run1->execute();
        $regIds=array();
        while($row1=$run1->fetch()){
              $id=$row1['C_ID'];
              $sql2="SELECT * FROM PARENT_OF WHERE C_ID=:id";
              $run2=$conn->prepare($sql2);
              $run2->bindParam(":id",$id);
              $run2->execute();
              $row2=$run2->fetch();
              $phoneNum=$row2['P_ID'];
              $sql ="SELECT * FROM NOTIFICATION_USERS WHERE phone=:phoneNum";
              $run=$conn->prepare($sql);
              $run->bindParam(":phoneNum",$phoneNum);
              $run->execute();
              while($row=$run->fetch()){
                array_push($regIds,$row['gcm_regid']);
              }
      }
      $sql1="SELECT * FROM CHILD WHERE EVENING_STOPPAGE_ID=:stoppageId";
        $run1=$conn->prepare($sql1);
        $run1->bindParam(":stoppageId",$stoppageId);
        $run1->execute();
        while($row1=$run1->fetch()){
              $id=$row1['C_ID'];
              $sql2="SELECT * FROM PARENT_OF WHERE C_ID=:id";
              $run2=$conn->prepare($sql2);
              $run2->bindParam(":id",$id);
              $run2->execute();
              $row2=$run2->fetch();
              $phoneNum=$row2['P_ID'];
              $sql ="SELECT * FROM NOTIFICATION_USERS WHERE phone=:phoneNum";
              $run=$conn->prepare($sql);
              $run->bindParam(":phoneNum",$phoneNum);
              $run->execute();
              while($row=$run->fetch()){
              array_push($regIds,$row['gcm_regid']);
      }

        }
        return $regIds;
         




}

// $curLatLons=$obj->nextStoppage('355801021693033','7000',$conn);//format of time is hhmmss
// echo $curLatLons['lat'];
//  echo     $curLatLons['lon'];
?>

