<?php

function envoie_email($email_from,$email_to,$subject,$message)
{
    $headers = 'From: '.$email_from. "\r\n" .
     'Reply-To: '.$email_from. "\r\n" .
	 'MIME-Version: 1.0\r\n'.
	 'Content-Type: text/plain; charset=utf-8\r\n'.
     'X-Mailer: PHP/' . phpversion();
    mail($email_to,$subject,$message, $headers);
	
	return true;
}

?>