<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_AddOn_Message_Models_Setting extends JobsExperts_Framework_OptionModel
{
    public function storage_name()
    {
        return "job_message";
    }

    public $global_receipt;
    public $user_receipt;
    public $inbox_page;
    public $message_per_page = 10;

    public $email_new_message_subject;
    public $email_new_message_content;

    public $email_read_message_subject;
    public $email_read_message_content;

    public function __construct()
    {
        $this->email_new_message_subject = "You've received a new message from FROM_NAME on SITE_NAME";
        $this->email_new_message_content = "FROM_NAME has sent you a message on SITE_NAME<br/><br/>

        FROM_MESSAGE
        <br/><br/>
        Check your messages here <a href='POST_LINK'>POST_LINK</a>
        ";

        $this->email_read_message_subject = "Your email sent to TO_NAME on SITE_NAME has read";
        $this->email_read_message_content = "Dear FROM_NAME <br/><br/>
        Your email sent to TO_NAME on SITE_NAME has read
";
    }
}