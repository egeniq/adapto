<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage utils
 *
 * @copyright (c)2006 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * @internal includes
 */
include_once(Adapto_Config::getGlobal("atkroot") . 'atk/ext/phpmailer/class.phpmailer.php');

/**
 * ATK mailer class
 *
 * This class can be used to send HTML e-mails.
 *
 * This is basically an extension of the PHPMailer class, to override some
 * basic settings.
 *
 * This class also supports a $config_mail_enabled config setting, which can
 * be set to false to disable all outgoing emails. (useful for test
 * environments that shouldn't actually send the mails)
 *
 * @author petercv

 *
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_Mailer extends PHPMailer
{
    public $Mailer = "mail"; // defaulted to public
    public $WordWrap = 75; // defaulted to public

    /**
     * Constructor
     *
     */

    public function __construct()
    {
        $charset = strtoupper(atkGetCharset());
        $this->CharSet = ($charset ? $charset : $this->CharSet);
    }

    /**
     * Override error handler.
     *
     * @param string $msg error message
     */
    function error_handler($msg)
    {
        atkerror($msg);
    }

    /**
     * Send.
     */
    function Send()
    {
        if (Adapto_Config::getGlobal("mail_enabled", true)) {
            // make sure Sender is set so the Return-Path header will have a decent value
            if ($this->Sender == "")
                $this->Sender = $this->From;

            $mail_redirect = Adapto_Config::getGlobal("mail_redirect");
            if (!empty($mail_redirect)) {
                $n = (strpos(strtolower($this->ContentType), 'html') !== false ? "<br/>" : "\n");
                $bodyPrefix = "--" . $n . "To: " . $this->recipientFieldToString($this->to) . $n . "Cc: " . $this->recipientFieldToString($this->cc) . $n
                        . "Bcc: " . $this->recipientFieldToString($this->bcc) . $n . "--" . $n;
                $this->Body = $bodyPrefix . $this->Body;
                $this->ClearAllRecipients();
                $this->AddAddress($mail_redirect, 'mail_redirect');
            }

            return parent::Send();
        } else
            return true;
    }

    /**
     * Convert the recipient to a correct string
     *
     * @param string $field
     * @return string 
     */
    function recipientFieldToString($field)
    {
        $ishtml = (strpos(strtolower($this->ContentType), 'html') > 0);
        $str = '';
        foreach ($field as $i => $recipient) {
            if ($i > 0)
                $str .= ', ';
            $str .= ($ishtml ? htmlentities($recipient[0]) : $recipient[0]);
            if (Adapto_strlen($recipient['1']) > 0)
                $str .= ' (' . ($ishtml ? Adapto_htmlentities($recipient[1]) : $recipient[1]) . ')';
        }
        return $str;
    }
}

?>
