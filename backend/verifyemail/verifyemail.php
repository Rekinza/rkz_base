<?php

//------------ SETTINGS ------------

$helo = $_SERVER['HTTP_HOST'];
$from = "info@mail.com";           // Specify an existing email address. If email servers rejet it, change it to another existing email address (it's advisable with another domain)
$scriptpass = "E35DCBD20CC0";      // Script password

//------------ END SETTINGS ------------

$email = $_GET["email"];
$password = $_GET["password"];

if ($password != $scriptpass)
{
  echo "<check>96DA8A550749</check><server>Verify Script</server><message>603 Wrong Password</message><log></log>";
  return;
}

$result = VerifyEmail($email, $from, $helo);
echo "<check>96DA8A550749</check><server>".$result[1]."</server><message>".$result[0]."</message><log>".$result[2]."</log>";


// Function result results in an array:
// $result[0] - SMTP Server Replay
// $result[1] - SMTP Server Host
// $result[2] - SMTP Server Log

function VerifyEmail($Email, $From, $Helo)
{
    $result = array();

    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $Email))
    {
        $result[0] = "500 Bad Syntax";
        return $result;
    }

    list ( $Username, $Domain ) = split ("@",$Email);
    if ( checkdnsrr ( $Domain, "MX" ) )
    {
        $log .= "MX record about {$Domain} exists:\r";
        if ( getmxrr ($Domain, $MXHost))
        {
              //for ( $i = 0,$j = 1; $i < count ( $MXHost ); $i++,$j++ )
              //{
              //    $log .= "$MXHost[$i]\r";
              //}
        }
        
        $ConnectAddress = $MXHost[0];
        $log .= $ConnectAddress."\r";

    }
    else
    {
        $ConnectAddress = $Domain;
        $log .= "MX record about {$Domain} does not exist.\r";
    }
    
    $Connect = fsockopen ( $ConnectAddress, 25 );
       $result[1] = $ConnectAddress;

    // Success in socket connection
    if ($Connect)
    {
         $log .= "Connection succeeded to {$ConnectAddress} SMTP.\r";

        $reply = fgets ( $Connect, 1024 );
        if ( ereg ( "^220",  $reply) )
        {
            $log .= $reply."\r";
            // Inform client's reaching to server who connect.
            fputs ( $Connect, "HELO $Helo\r\n" );
                $log .=  "> HELO $Helo\r";
            $reply = fgets ( $Connect, 1024 ); // Receive server's answering cord.
                $log .= $reply."\r";

            // Inform sender's address to server.
            fputs ( $Connect, "MAIL FROM: <{$From}>\r\n" );
                $log .=  "> MAIL FROM: <{$From}>\r";
            $reply = fgets ( $Connect, 1024 ); // Receive server's answering cord.
                $log .= "=".$reply."\r";
            // Inform listener's address to server.
            fputs ( $Connect, "RCPT TO: <{$Email}>\r\n" );
                $log .= "> RCPT TO: <{$Email}>\r";
            $reply = fgets ( $Connect, 1024 ); // Receive server's answering cord.
                $log .= "=".$reply."\r";
            if (stripos($Email, "@yahoo.") !== false)
            {
                fputs ( $Connect, "DATA\r\n" );
                    $log .= "> DATA\r\n";
                $reply = fgets ( $Connect, 1024 ); // Receive server's answering cord.
                    $log .= "=".$reply."\r\n";
                fputs ( $Connect, "\r\n.\r\n" );
                    $log .= "> ...\r\n";
                $reply = fgets ( $Connect, 1024 ); // Receive server's answering cord.
                    $log .= "=".$reply."\r\n";
            }  

            // Finish connection.
            fputs ( $Connect, "QUIT\r\n");
                $log .=  "> QUIT\r"; 
        }
        else
        {
        	$log .= "=".$reply."\r\n";
        }
        
          fclose($Connect);
    }
    // Failure in socket connection
    else
    {
        $result[0]="500 Can not connect E-Mail server: ({$ConnectAddress}).";
        $result[2]=$log;
        return $result;
    }
    $result[0]=$reply;
    $result[2]=$log;
    return $result;
}

?>
