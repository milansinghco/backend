 <?php
 require 'connection.php';//connection to the database of scholarshield
include'function.php';
 /* variables value   you should change*/
  $index=14066;// change the index value according to your database table  LIKE for  this ex table name is LEVEL3_COLUMN_WISE_DATA
  $busStartTime=80000;
  $busSpeed=15/1.852;
  $table="LEVEL3_COLUMN_WISE_DATA";
  $obj = new allfunction;
  $flagCheckDatabase=1;
  $echoOnlyOneTime=0;
  $valueStoppageCheck=1;
  $valueTripCheck=0;
  $conditionEntryTime=0;
  $firstGeo=0.0225;//square of 0.5 km 
  $secondGeo=1.119;//square of 1.5km
   ?>

 <?php
           
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
                    $speed=$row1['SPEED'];
                    $time=$row1['TIMINGS'];
                    echo "slat".$row1['LATITUDE'];
                    echo "slong".$row1['LONGITUDE'];
                    $sourceLat=$row1['LATITUDE'];
                    $sourceLon=$row1['LONGITUDE'];
                    $id=$row1['ID'];
                    $imei=$row1['IMEI'];
                    $route=$obj->fetchRoute($imei,$time,$conn);
                     echo "\nroute".$routeId=$route['routeId'];
                    $tripCheck=$route['tripCheck'];
                    echo "\ntripCheck=".  $tripCheck;
                    if($tripCheck==1){
                               $stoppage=$obj->nextStoppage($routeId,$conn);
                               echo"\nstoppageid".$stoppageId=$stoppage['stoppageId'];
                               echo"\nremainstoppageid".$remainStoppageId=$stoppage['remainigStoppages'];
                               $stoppageLL=$obj->fetchStoppageLatLon($stoppageId,$conn);
                               $destLat=$stoppageLL['lat'];
                               $destLon=$stoppageLL['lon'];//stoppage laTlons will be in google format
                               echo $geo=$obj->geoFence($sourceLat,$sourceLon,$destLat,$destLon);
                              echo "\nfirstStoppage=".$firstStoppaage=$obj->fetchFirstStoppageOfRoute($routeId,$conn);
                              if($geo<$firstGeo){// if  radius is 150 metr
                                 echo "1st geo is true";
				 $obj->updateStoppage($routeId,$stoppageId,$valueStoppageCheck,$conn);// run  it for every stoppage  and route id
                                  if($remainStoppageId==0){
                                      echo "trip check updated";
                                     $obj->updateAllStoppagesZero($routeId,$conn);
                                     $obj->updateTripCheck($routeId,$valueTripCheck,$conn);
                                    }

                              }elseif($stoppageId!=$firstStoppaage && $remainStoppageId!=0){ //checking for first and last stoppages
                                     echo "miss check";
				     $nextStoppage=$obj->nextToNextStoppage($routeId,$conn);
                                     echo"\nnextstoppageid".$nextStoppageId=$nextStoppage['stoppageId'];
                                     echo"\nremainstoppageid".$remainNextStoppageId=$nextStoppage['remainigStoppages'];
                                     $nextStoppageLL=$obj->fetchStoppageLatLon($nextStoppageId,$conn);
                                     $destLat=$nextStoppageLL['lat'];
                                     $destLon=$nextStoppageLL['lon'];//stoppage laTlons will be in google format
                                     echo "\nnextgeo".$geo=$obj->geoFence($sourceLat,$sourceLon,$destLat,$destLon);
                                     if($geo<$firstGeo){
                                         echo "link upadate..................................last stoppage";
                                         $obj->updateStoppage($routeId,$stoppageId,$valueStoppageCheck,$conn);
                                         $obj->updateStoppage($routeId,$nextStoppageId,$valueStoppageCheck,$conn);
                                         $obj->updateLastStoppageLink($nextStoppageId,$routeId,$conn);
                                        
                                    
                                      }
                                  }
                                   
                          
                    }
                             $flag=1;       /*continue checking for  next entry in  the data  table*/
                              while($flag){ // or wait till next data entry
                                      $indx=$index;
                                      $indx++;
                                      $sql1="SELECT count(ID) FROM  $table WHERE ID>=:index";
                                      $run1=$conn->prepare($sql1);
                                      $run1->bindParam(":index",$indx);
                                      $run1->execute();
                                      $row1=$run1->fetch();
                                      if($row1[0]>0){        
                                              $index++;
                                              $flag=0;
                                      }
                              }

            }



?>/*V312,GA,355801021135993,1,260616,090521,2839.1483,N,07716.6599,E,2.90,52.01,205.6,07,404,10,0000,701d,00055942,255,0000,13,4023,00000,EG
*/

<?php
  
//prob.....
// it will continue checking the packets comingmafter setting the 



?>