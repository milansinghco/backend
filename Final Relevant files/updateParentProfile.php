<?php

require 'connection.php';
require'info.php';

if(isset($_POST['name']) && isset($_POST['smsNum'])){

$name=$_POST['name'];
$smsNum=$_POST['smsNum'];
$phone=$_POST['phone'];
             try{
     	 $sql = "UPDATE PARENT SET NAME=:name,SMS_NUMBER=:smsNum WHERE PHONE_NUMBER=:phone";
		 $run=$conn->prepare($sql);
               
		 $run->bindParam(":name",$name);
		 $run->bindParam(":smsNum",$smsNum);
		 $run->bindParam(":phone",$phone);
		 $run->execute();
		 $info = info($phone,$con);
		 echo $info;

		 }catch(PDOException $e){
                        $myresponse = array();
                          $code = "login_false";
                         array_push($myresponse,array("code"=>$code));
                          echo json_encode(array("server_responses"=>$myresponse));
                          die();
                       }


}
?>
