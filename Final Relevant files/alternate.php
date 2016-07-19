<?php
$host = "localhost";
$user = "root";
$password = "himanshu1865";
$dbname = "scholarshield";

$con = mysqli_connect($host,$user,$password,$dbname);
$phone = $_POST["Phone"];


$queryofstart = "select NAME,PHONE_NUMBER,SMS_NUMBER from PARENT where PHONE_NUMBER like '".$phone."';";
$resultofstart = mysqli_query($con,$queryofstart);
$myresponse = array();
if(mysqli_num_rows($resultofstart)>0)
{
	
	$code = "login_true";
        $row = mysqli_fetch_array($resultofstart);
        $name=$row[0];
        $sms_number=$row[2];
        $secondquery = "select C_ID from PARENT_OF where P_ID like '".$phone."';";
        $secondresult = mysqli_query($con,$secondquery);
        $children=array();
     if(mysqli_num_rows($secondresult)>0)
     {
       $numberofchildren=mysqli_num_rows($secondresult);
      while($rowl = mysqli_fetch_array($secondresult))
      {
       $query2 = "select C_ID,NAME,SCHOOLBRANCH_ID,MORNING_STOPPAGE_ID,EVENING_STOPPAGE_ID,ROUTE_ID,CLASS,SECTION from CHILD where C_ID like '".$rowl[0]."';";
       $result2 = mysqli_query($con,$query2);
       $row2 = mysqli_fetch_array($result2);
       $query3 = "select NAME,SCHOOL_LAT,SCHOOL_LONGI,TRANSPORT_NUMBER from SCHOOL_BRANCH where SCHOOLBRANCH_ID like '".$row2[2]."';";
       $result3 = mysqli_query($con,$query3);
       $row3 = mysqli_fetch_array($result3);
       $schoolname = $row3[0];
       $schoollat = $row3[1];
       $schoollong = $row3[2];
       $schoolNumber= $row3[3];
       $query4 = "SELECT LATITUDE,LONGITUDE,STOPPAGE_NAME FROM STOPPAGE WHERE STOPPAGE_ID LIKE '".$row2[3]."';";
       $resu = mysqli_query($con,$query4);
       $row4 = mysqli_fetch_array($resu);
       $query6 = "SELECT LATITUDE,LONGITUDE,STOPPAGE_NAME FROM STOPPAGE WHERE STOPPAGE_ID LIKE '".$row2[4]."';";
       $res6 = mysqli_query($con,$query6);
       $row6 = mysqli_fetch_array($res6);
       $getroute= "select MORNING_ROUTE_ID,EVENING_ROUTE_ID from GET_ROUTE_ID where ROUTE_ID like '".$row2[5]."';";
       
       $resultroute = mysqli_query($con,$getroute);
       $finalroutes = mysqli_fetch_array($resultroute);
     
       $morningroute = array();
       $messi= "select BUS_ID,ROUTE_NAME,START_ROUTE_TIME,STOP_ROUTE_TIME from LINK where ROUTE_ID like '".$finalroutes[0]."';";
       $messiroute = mysqli_query($con,$messi);
       $messiroutes = mysqli_fetch_array($messiroute);
       $que = "select IMEI,BUS_NUMBER from BUS where BUS_ID like '".$messiroutes[0]."';";
       $res = mysqli_query($con,$que);
       $rt = mysqli_fetch_array($res);
    
       $queryofstarting = "select STOPPAGE_ID from PATH where ROUTE_ID like '".$finalroutes[0]."';";
       $resultofstarting = mysqli_query($con,$queryofstarting);
       $buses = array();
       if(mysqli_num_rows($resultofstarting)>0)
               {
                  while($rolls = mysqli_fetch_array($resultofstarting))
                  {
                 $queryfying = "SELECT STOPPAGE_ID,LATITUDE,LONGITUDE,STOPPAGE_NAME FROM STOPPAGE WHERE STOPPAGE_ID LIKE '".$rolls[0]."';";
                 $resultfying = mysqli_query($con,$queryfying);
                 $ronaldo = mysqli_fetch_array($resultfying);
                 array_push($buses,
                   array("Stoppage_id"=>$ronaldo[0],"Latitude"=>$ronaldo[1],"Longitude"=>$ronaldo[2],"Area"=>$ronaldo[3]));
                  }
               }
       array_push($morningroute,array("Bus_Number"=>$rt[1],"imei"=>$rt[0],"home_lat"=>$row4[0],"home_lon"=>$row4[1],"home_name"=>$row4[2],"route_name"=>$messiroutes[1],"start_time"=>$messiroutes[2],"stop_time"=>$messiroutes[3],"path"=>$buses));
       $eveningroute = array();
       $secondmessi= "select BUS_ID,ROUTE_NAME,START_ROUTE_TIME,STOP_ROUTE_TIME from LINK where ROUTE_ID like '".$finalroutes[1]."';";
       $secondmessiroute = mysqli_query($con,$secondmessi);
       $secondmessiroutes = mysqli_fetch_array($secondmessiroute);
       $queries = "select IMEI,BUS_NUMBER from BUS where BUS_ID like '".$secondmessiroutes[0]."';";
       $resies = mysqli_query($con,$queries);
       $rties = mysqli_fetch_array($resies);
       $starting = "select STOPPAGE_ID from PATH where ROUTE_ID like '".$finalroutes[1]."';";
       $resultstarting = mysqli_query($con,$starting);
       $buses2 = array();
       if(mysqli_num_rows($resultstarting)>0)
               { 

                  while($rlls = mysqli_fetch_array($resultstarting))
                  {
                 $querying = "SELECT STOPPAGE_ID,LATITUDE,LONGITUDE,STOPPAGE_NAME FROM STOPPAGE WHERE STOPPAGE_ID LIKE '".$rlls[0]."';";
                 $resuling = mysqli_query($con,$querying);
                 $ronadino = mysqli_fetch_array($resuling);
                 array_push($buses2,
                   array("Stoppage_id"=>$ronadino[0],"Latitude"=>$ronadino[1],"Longitude"=>$ronadino[2],"Area"=>$ronadino[3]));
                  }
               }
array_push($eveningroute,array("Bus_Number"=>$rties[1],"imei"=>$rties[0],"home_lat"=>$row6[0],"home_lon"=>$row6[1],"home_name"=>$row6[2],"route_name"=>$secondmessiroutes[1],"start_time"=>$secondmessiroutes[2],"stop_time"=>$secondmessiroutes[3],"path"=>$buses2));
       array_push($children,array('childid'=>$row2[0],'Name'=>$row2[1],'class'=>$row2[6],'section'=>$row2[7],'SchoolName'=>$schoolname,'Schoollat'=>$schoollat,'Schoollong'=>$schoollong,'SchoolNumber'=>$schoolNumber,'Morning_path'=>$morningroute,'Evening_path'=>$eveningroute));
         }
array_push($myresponse,array("code"=>$code,"quantity"=>$numberofchildren,"Name"=>$name,"Phone"=>$phone,"sms_number"=>$row[2],"children"=>$children));
	echo json_encode(array("server_responses"=>$myresponse));
         }
}
else
{
	$myresponse = array();
	$code = "login_false";
	array_push($myresponse,array("code"=>$code));
	echo json_encode(array("server_responses"=>$myresponse));
}

mysqli_close($con);

?>
