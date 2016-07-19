<?php
require 'connection.php';


if(isset($_POST['phone']) && isset($_POST['issue']) && isset($_POST['message'])){

$phone=$_POST['phone'];
$issue=$_POST['issue'];
$message=$_POST['message'];
         try{
       	 $sql = "INSERT INTO SUPPORT(`P_ID`,`ISSUE`,`MESSAGE`) VALUES(:phone,:issue,:message)";
		 $run=$conn->prepare($sql);
		 $run->bindParam(":phone",$phone);
		 $run->bindParam(":issue",$issue);
		 $run->bindParam(":message",$message);
		 $run->execute();
                 echo "true";
		  }catch(PDOException $e){
                         echo "false";
                           die();
                       }


}
?>
