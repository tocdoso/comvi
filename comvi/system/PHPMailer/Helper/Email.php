<?php
namespace PHPMailer\Helper;

/**
 * Email helper class.
 *
 * @static
 * @package		PHPMailer
 * @subpackage	Helper
 */
class Email
{
	/**
	 * Send an Email
	 *
	 * @return  bool
	 */
	public static function send($params = array(), $type = 'maintainance')
	{
		$mail_config		= \Loader::getInstance('config', 'mail');

		if ($mail_config->get('driver') !== 'smtp') {
			return false;
		}

		//Override
		foreach ($mail_config->get($type) as $prop => $value) {
			$mail_config->set($prop, $value);
		}

		extract($params);
		$body = $mail_config->get('body');
		eval("\$body = \"$body\";");

		$mail				= new \PHPMailer();

		$mail->IsSMTP();									// telling the class to use SMTP
		$mail->Host			= $mail_config->get('smtp_host');// SMTP server
		//$mail->SMTPDebug	= 2;							// enables SMTP debug information (for testing)
															// 1 = errors and messages
															// 2 = messages only
		//$mail->SMTPAuth		= true;						// enable SMTP authentication
		$mail->Host			= $mail_config->get('smtp_host');// sets the SMTP server
		$mail->Port			= $mail_config->get('smtp_port');// set the SMTP port for the GMAIL server
		//$mail->Username	= $mail_config->get('smtp_user');// SMTP account username
		//$mail->Password	= $mail_config->get('smtp_pass');// SMTP account password

		$mail->SetFrom($mail_config->get('from'), $mail_config->get('from_name'));
		$mail->AddAddress($mail_config->get('to'), $mail_config->get('to_name'));
		//$mail->AddReplyTo($mail_config->get('to'), $mail_config->get('to_name'));

		$mail->CharSet		= $mail_config->get('charset');
		$mail->Subject		= $mail_config->get('subject');
		//$mail->AltBody	= "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		//$mail->MsgHTML($body);
		$mail->Body			= $body;
		//$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

		if(!$mail->Send()) {
			return false;
		}

		return true;
	}
}
?>