<?php

     /*                       function sendsms($contactnumbs,$msg)
                             {
                              $sid= "SIDEMO";
                              $key='Aebc89fa4996fdb36b2f579be150d19a0';
                              
				$chk = explode(" ",$msg);
				$output='';
				for($i=0;$i<count($chk);$i++)
				
				{
				 $output.=$chk[$i].'%20';
				}

			      $address='api-alerts.solutionsinfini.com/v3/?method=sms&api_key='.$key.'&to='.$contactnumbs.'&sender='.$sid.'&message='.$output;
                             
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
                              if(!$resp)
			       {
                               die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
                               }
                              curl_close($curl);   
                               }

				
                               foreach ($contactIdchunk as $contactIds)
                                {

                                  sendsms($contactIds['SMS_NUMBER'],$pushMessage);
                                }
  */                          
                            
/**/

?>