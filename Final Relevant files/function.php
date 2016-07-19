<?php
 require 'connection.php';
 date_default_timezone_set('Asia/Kolkata');
class allfunction {
	public function nextStoppage($routeId,$conn){
		$remainingStoppages=0;
        $stoppageCheck=0;
        $sql3 ="SELECT * FROM PATH WHERE ROUTE_ID=:routeId AND STOPPAGE_CHECK=:stoppageCheck ORDER BY ID ASC"; 
        $run2=$conn->prepare($sql3);
        $run2->bindParam(":routeId",$routeId);
        $run2->bindParam(":stoppageCheck",$stoppageCheck);
        $run2->execute();
        $row=$run2->fetch();
        $nextStoppageId=$row['STOPPAGE_ID'];
        while($row=$run2->fetch()){
        	$remainingStoppages=$remainingStoppages+1;
        }
        $stoppage = array('stoppageId'=>$nextStoppageId,'remainigStoppages'=>$remainingStoppages);
        return $stoppage;




	}
	public function nextToNextStoppage($routeId,$conn){
		$remainingStoppages=0;
        $stoppageCheck=0;
        $sql3 ="SELECT * FROM PATH WHERE ROUTE_ID=:routeId AND STOPPAGE_CHECK=:stoppageCheck ORDER BY ID ASC"; 
        $run2=$conn->prepare($sql3);
        $run2->bindParam(":routeId",$routeId);
        $run2->bindParam(":stoppageCheck",$stoppageCheck);
        $run2->execute();
        $row=$run2->fetch();
        $row2=$run2->fetch();
        $nextStoppageId=$row['STOPPAGE_ID'];
        $nextStoppageId=$row2['STOPPAGE_ID'];
        while($row=$run2->fetch()){
        	$remainingStoppages=$remainingStoppages+1;
        }
        $stoppage = array('stoppageId'=>$nextStoppageId,'remainigStoppages'=>$remainingStoppages);
        return $stoppage;




	}
	public function updateLastStoppageLink($stoppageId,$routeId,$conn){
		 $sql = "UPDATE LINK SET LAST_STOPPAGE=:stoppageId WHERE ROUTE_ID=:routeId";
         $run=$conn->prepare($sql);
		 $run->bindParam(":stoppageId",$stoppageId);
		 $run->bindParam(":routeId",$routeId);
		 $run->execute();
	}
	public function fetchRoute($imei,$time,$conn){
	    // $stTime = new DateTime($time);
	    // $stTime->add(new DateInterval('P0Y0M0DT5H30M0S'));
	    // $istTime= $stTime->format('H:i:s');
	    $indianTime=$this->convertAccrdIndTime($time);// convert time in IST(indian) format
	       
	    $sql1="SELECT * FROM BUS WHERE IMEI=:imei";
	    $run1=$conn->prepare($sql1);
	    $run1->bindParam(":imei",$imei);
	    $run1->execute();
	    $row1=$run1->fetch();
	    $busId=$row1['BUS_ID'];  
	    $sql ="SELECT * FROM LINK WHERE BUS_ID=:busId AND START_ROUTE_TIME<=:timee AND STOP_ROUTE_TIME>=:timeee";

	    $run=$conn->prepare($sql);
	    $run->bindParam(":busId",$busId);
	    $run->bindParam(":timee",$indianTime);
	    $run->bindParam(":timeee",$indianTime);
	    $run->execute();
	    $row=$run->fetch();
	    $routeId=$row['ROUTE_ID'];
        $tripCheck=$row['TRIP_CHECK'];
	    $stoppage=array();
		$route = array('routeId'=>$routeId,'tripCheck'=>$tripCheck);
		return $route;
		        
	}

    public function convertAccrdIndTime($time){
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
	public   function convertLat($lati){
	    if(isset($lati) && !empty($lati)){
		    $latinew=str_split($lati);
		    $lat1=$latinew[0];
		    $lat2=$latinew[1];
		    $latL=$lat1.$lat2;
		    $latR=$latinew[2].$latinew[3].$latinew[4].$latinew[5].$latinew[6].$latinew[7].$latinew[8];
		    $latRR=$latR/60;
       	     $lati=$latL+$latRR;
		     return $lati;
	    }
    }

	public function convertLong($longi){
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

	public function updateStoppage($routeId,$stoppageId,$valueStoppageCheck,$conn){
        
     $sql = "UPDATE PATH SET STOPPAGE_CHECK=:value WHERE ROUTE_ID=:routeId AND STOPPAGE_ID=:stoppageid";
     $run=$conn->prepare($sql);
     $run->bindParam(":stoppageid",$stoppageId);
     $run->bindParam(":value",$valueStoppageCheck);
     $run->bindParam(":routeId",$routeId);
     $run->execute();
     echo "stop id updated to".$stoppageId;
    
        

	}
	public function  updateTripCheck($routeId,$valueTripCheck,$conn){

	 $sql = "UPDATE LINK SET TRIP_CHECK=:value WHERE ROUTE_ID=:routeId";
     $run=$conn->prepare($sql);
     $run->bindParam(":value",$valueTripCheck);
     $run->bindParam(":routeId",$routeId);
     $run->execute();
   

	}
	public function checkForLastStoppage($routeId,$stoppageId,$conn){
	    $remainingStoppagesCount=0;
		$sql ="SELECT * FROM LINK WHERE ROUTE_ID=:routeid";
		$run=$conn->prepare($sql);
		$run->bindParam(":routeid",$routeId);
		$run->execute();
		$row=$run->fetch();
		$startTime=$row['START_ROUTE_TIME'];
		$notificationCheck=$row['NOTIFICATION_CHECK'];
		$lastStoppage=$stoppageId;
		$sql2 ="SELECT * FROM PATH WHERE STOPPAGE_ID=:stopgId AND ROUTE_ID=:routeid";
	    $run2=$conn->prepare($sql2);
	    $run2->bindParam(":stopgId",$lastStoppage);
	    $run2->bindParam(":routeid",$routeId);
	    $run2->execute();
	    $row=$run2->fetch();
	    $lastStoppageIndex=$row['ID'];
		$sql3 ="SELECT * FROM PATH WHERE ROUTE_ID=:routeId AND ID >=:index ORDER BY ID ASC";// change INDEX coloumn name to id in path table
		$run2=$conn->prepare($sql3);
		$run2->bindParam(":routeId",$routeId);
		$run2->bindParam(":index",$lastStoppageIndex);
		$run2->execute();
	 //print_r($run2);
		while($row=$run2->fetch()){
			   $row['STOPPAGE_ID'];
			    $remainingStoppagesCount++;
			   
	           

	    }
          return  $remainingStoppagesCount=1;

     
     
		
	}
	public function geoFence($sourceLat,$sourceLon,$destLat,$destLon){
                     
                    $diffLat=$sourceLat-$destLat;
                    $diffLon=$sourceLon-$destLon;
                     $r=(111*111);
                    $value=($r*$diffLat*$diffLat)+($r*$diffLon*$diffLon);
                     return $value;




    }
   public function updateLastStoppage($routeId,$stoppageId,$conn){
		 $sql = "UPDATE LINK SET LAST_STOPPAGE=:stoppageId WHERE ROUTE_ID=:routeId";
		 $run=$conn->prepare($sql);
		 $run->bindParam(":stoppageId",$stoppageId);
		 $run->bindParam(":routeId",$routeId);
		 $run->execute();
		
		 }



    public function fetchFirstStoppageOfRoute($routeId,$conn){
		$sql3 ="SELECT * FROM PATH WHERE ROUTE_ID=:routeId ORDER BY ID ASC"; // change INDEX  coloumn name to id in path table
        $run2=$conn->prepare($sql3);
        $run2->bindParam(":routeId",$routeId);
        $run2->execute();
        $row=$run2->fetch();
        $lastStooppageId=$row['STOPPAGE_ID'];
        return $lastStooppageId;
       
       
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
        curl_setopt($curl, CURLOPT_SSLVERSION,3); 
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
        $duration=$data["rows"][0]["elements"][0]["duration"]["value"];
          return $duration;
        


  }


 
}

?>