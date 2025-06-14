<?php
namespace App\Model;

class Ticket
{
    public $user;
    public $produto;
    public $subject;
    public $message;
    public $imagePath;
    public $created_by;

    public function __construct($user, $produto, $subject, $message, $imagePath = null, $created_by = null)
    {
        $this->user = $user;
        $this->produto = $produto;
        $this->subject = $subject;
        $this->message = $message;
        $this->imagePath = $imagePath;
        $this->created_by = $created_by;
    }
}
