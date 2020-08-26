<?php

    date_default_timezone_set('Etc/UTC');

    /*  Include file here   */

    include"newfunction.php";

    include"settings.php";
    
    require 'PHPMailerAutoload.php';
    
    require 'class.phpmailer.php';

    /*  End     */

echo "\r\n";                                                                                            
echo "     /___/\_                    \r\n";                   
echo "    _\   \/_/\__                \r\n";             
echo "  __\       \/_/\               \r\n";          
echo "  \   __    __ \ \              \r\n";      
echo " __\  \_\   \_\ \ \   __        \r\n";   
echo "/_/\\   __   __  \ \_/_/\       \r\n"; 
echo "\_\/_\__\/\__\/\__\/_\_\/       \r\n";    
echo "   \_\/_/\       /_\_\/         \r\n";    
echo "      \_\/       \_\/           \r\n";




                                                     

      
         
echo "\r\n";      
    $smtp = new SMTP;
    //$smtp->do_debug = 4;

    
    try {
        //Connect to an SMTP server
        if (!$smtp->connect($smtpserver, $smtpport)) {
            throw new Exception('Connect failed');
        }
        //Say hello
        if (!$smtp->hello(gethostname())) {
            throw new Exception('EHLO failed: ' . $smtp->getError()['error']);
        }
        //Get the list of ESMTP services the server offers
        $e = $smtp->getServerExtList();
        //If server can do TLS encryption, use it
        if (array_key_exists('STARTTLS', $e)) {
            $tlsok = $smtp->startTLS();
            if (!$tlsok) {
                throw new Exception('Failed to start encryption: ' . $smtp->getError()['error']);
            }
            //Repeat EHLO after STARTTLS
            if (!$smtp->hello(gethostname())) {
                throw new Exception('EHLO (2) failed: ' . $smtp->getError()['error']);
            }
            //Get new capabilities list, which will usually now include AUTH if it didn't before
            $e = $smtp->getServerExtList();
        }
        //If server supports authentication, do it (even if no encryption)
        if (array_key_exists('AUTH', $e)) {
            if ($smtp->authenticate($smtpuser, $smtppass)) {

                echo"\x1b[1;33;32m{===[ Evil Sender ( •̀ᴗ•́ )و ̑̑ ]===}";
                echo "\r\n";  
                echo"\n";
                $mail = new PHPMailer;
			$mail->Encoding = 'quoted-printable'; // 8bit base64 multipart/alternative
            $mail->CharSet = 'UTF-8';
                /*  Smtp connect    */

                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->Host = $smtpserver;
                $mail->Port = $smtpport;
                $mail->Priority = $priority;
                $mail->Username = $smtpuser;
                $mail->Password = $smtppass;
                //$mail->XMailer = 'ZuckMail [version 1.00]';

                /*  End     */


                $file = file_get_contents($mailist);
                if($file){
	                $ext = explode("\r\n",$file);
	                foreach($ext as $num => $email){
	                	$nm = $num+1;
			            $stop = $nm+10;
			            $count = count($ext);
		                /*  Mail settings   */

		                if($userandom==1){
		                	$rand = rand(1,50);
		                	$fromname = randName($rand);
		                	$frommail = randMail($rand);
		                	$subject = randSubject($rand);
		                }

                        $asu = RandString1(8);
						$asu1 = RandString(5);
						$asu2 = RandString1(5);
						$nmbr = RandNumber(5);
						$fromnames =str_replace('##randstring##', $asu1, $fromname);
                        $frommails = str_replace('##randstring##', $asu, $frommail);
						$subjects = str_replace('##randstring##', $asu2, $subject);
						
                        $mail->setFrom($frommails, $fromnames);

		                $mail->addAddress($email);
						

		                $mail->Subject = $subjects;

		                if($replacement==1){
		                	$msg = lettering($msgfile,$email,$frommail,$fromname,$randurl,$subject);
		                }else{
		                	$msg = file_get_contents($msgfile);
		                }
		                
		                $mail->msgHTML($msg);


		                /*	Time to sending your mail ^_^  */

		        if (!$mail->send()) {
		           //     $limit = $mail->ErrorInfo;
		             //   if ($limit = "Daily SMTP relay limit exceeded for customer.") {
		                	echo "SMTP Error : ".$mail->ErrorInfo;
							exit();
                } else {
			            echo "[$nm/$count] -> \x1b[1;32;40m{$email}  \n";
			        }
			       
			        sleep($sleeptime);
			        $mail->clearAddresses();

			    }
}
	if ($userremoveline == 1) {
		$remove = Removeline($mailist,$email);
	}


            } else {
                throw new Exception('Authentication failed: ' . $smtp->getError()['error']);
            }
        }
    } catch (Exception $e) {
        echo 'SMTP error: ' . $e->getMessage(), "\n";
    }
    //Whatever happened, close the connection.
    $smtp->quit(true); 
