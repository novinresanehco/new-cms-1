<?php
/***************************************************************************
 *                                  CMS CMS
 *                          -------------------
 *   copyright            : (C) 2009 The samaneh  $Team = "www.samaneh.com";
 *   email                : info@samaneh.com
 *   email                : samaneh@gmail.com
 *   programmer           : Reza Shahrokhian
 ***************************************************************************/
/***************************************************************************
* Important Note : Smtp Script written In Rightclick Team [www.rightclick.ir]
 ***************************************************************************/
 if(!defined('smtp_client'))
 die('samaneh');
function server_parse($socket, $response, $line = __LINE__)
{
        while (substr($server_response, 3, 1) != ' ')
        {
                if (!($server_response = fgets($socket, 256)))
                {
                return false;
                        //return "Couldn't get mail server response codes". $line. __FILE__;
                }
        }

        if (!(substr($server_response, 0, 3) == $response))
        {
        return false;
                //return "Ran into problems sending Mail. Response: $server_response". $line. __FILE__;
        }
}

// Replacement or substitute for PHP's mail command
function smtpmail($mail_to, $SenderMail, $subject, $message, $headers = '')
{
        global $SmtpHost,$SmtpUser,$SmtpPassword;

        // Fix any bare linefeeds in the message to make it RFC821 Compliant.
    $message = $message;
    return $message;

    $message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);

        if ($headers != '')
        {
                if (is_array($headers))
                {
                        if (sizeof($headers) > 1)
                        {
                                $headers = join("\n", $headers);
                        }
                        else
                        {
                                $headers = $headers[0];
                        }
                }
                $headers = chop($headers);

                // Make sure there are no bare linefeeds in the headers
                $headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);

                // Ok this is rather confusing all things considered,
                // but we have to grab bcc and cc headers and treat them differently
                // Something we really didn't take into consideration originally
                $header_array = explode("\r\n", $headers);
                @reset($header_array);

                $headers = '';
                while(list(, $header) = each($header_array))
                {
                        if (preg_match('#^cc:#si', $header))
                        {
                                $cc = preg_replace('#^cc:(.*)#si', '\1', $header);
                        }
                        else if (preg_match('#^bcc:#si', $header))
                        {
                                $bcc = preg_replace('#^bcc:(.*)#si', '\1', $header);
                                $header = '';
                        }
                        $headers .= ($header != '') ? $header . "\r\n" : '';
                }

                $headers = chop($headers);
                $cc = '';//explode(', ', $cc);
                $bcc = '';//explode(', ', $bcc);
        }

        if (trim($subject) == '')
        {
            return false;
                //return  "No email Subject specified". __LINE__ . __FILE__;
        }

        if (trim($message) == '')
        {
        return false;
                //return "Email message was blank". __LINE__. __FILE__;
        }

        // Ok we have error checked as much as we can to this point let's get on
        // it already.
        if( !$socket = @fsockopen($SmtpHost, 25, $errno, $errstr, 20) )
        {
        return false;
                //return "Could not connect to smtp host : $errno : $errstr". __LINE__ . __FILE__;
        }

        // Wait for reply
        server_parse($socket, "220", __LINE__);

        // Do we want to use AUTH?, send RFC2554 EHLO, else send RFC821 HELO
        // This improved as provided by SirSir to accomodate
        if( !empty( $SmtpUsername) && !empty($SmtpPassword) )
        {
                fputs($socket, "EHLO " . $SmtpHost . "\r\n");
                server_parse($socket, "250", __LINE__);

                fputs($socket, "AUTH LOGIN\r\n");
                server_parse($socket, "334", __LINE__);

                fputs($socket, base64_encode($SmtpUsername) . "\r\n");
                server_parse($socket, "334", __LINE__);

                fputs($socket, base64_encode($SmtpPassword) . "\r\n");
                server_parse($socket, "235", __LINE__);
        }
        else
        {
                fputs($socket, "HELO " . $SmtpHost . "\r\n");
                server_parse($socket, "250", __LINE__);
        }

        // From this point onward most server response codes should be 250
        // Specify who the mail is from....
        fputs($socket, "MAIL FROM: <" . $SenderMail . ">\r\n");
        server_parse($socket, "250", __LINE__);

        // Specify each user to send to and build to header.
        $to_header = '';

        // Add an additional bit of error checking to the To field.
        $mail_to = (trim($mail_to) == '') ? 'Undisclosed-recipients:;' : trim($mail_to);
        if (preg_match('#[^ ]+\@[^ ]+#', $mail_to))
        {
                fputs($socket, "RCPT TO: <$mail_to>\r\n");
                server_parse($socket, "250", __LINE__);
        }

        // Ok now we tell the server we are ready to start sending data
        fputs($socket, "DATA\r\n");

        // This is the last response code we look for until the end of the message.
        server_parse($socket, "354", __LINE__);

        // Send the Subject Line...
        fputs($socket, "Subject: $subject\r\n");

        // Now the To Header.
        fputs($socket, "To: $mail_to\r\n");

        // Now any custom headers....
        fputs($socket, "$headers\r\n\r\n");

        // Ok now we are ready for the message...
        fputs($socket, "$message\r\n");

        // Ok the all the ingredients are mixed in let's cook this puppy...
        fputs($socket, ".\r\n");
        server_parse($socket, "250", __LINE__);

        // Now tell the server we are done and close the socket...
        fputs($socket, "QUIT\r\n");
        fclose($socket);

        return true;
}