<?php
 require 'connection.php';
require'info.php';

if(isset($_POST['name']) && isset($_POST['class']) && isset($_POST['sec']) && isset($_POST['cId'])){

$name=$_POST['name'];
$class=$_POST['class'];
$sec=$_POST['sec'];
$cId=$_POST['cId'];
$phone='';

            try{
     	 $sql = "UPDATE CHILD SET NAME=:name,CLASS=:class,SECTION=:section WHERE C_ID=:cId";
		 $run=$conn->prepare($sql);
		 $run->bindParam(":name",$name);
		 $run->bindParam(":class",$class);
		 $run->bindParam(":section",$sec);
		 $run->bindParam(":cId",$cId);
		 $run->execute();
                  $sql = "SELECT P_ID FROM PARENT_OF WHERE C_ID=:cId";
		 $run=$conn->prepare($sql);
		 $run->bindParam(":cId",$cId);
		 $run->execute();
                 $row=$run->fetch();
                 $phone=$row["P_ID"];
                 $info = info($phone,$con);
		 echo $info;
		 
               }catch(PDOException $e){
                         $myresponse = array();
                          $code = "login_false";
                          array_push($myresponse,array("code"=>$code));
                           echo json_encode(array("server_responses"=>$myresponse));
                           die();
                       }
  



?>

