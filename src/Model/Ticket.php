<?php
namespace App\Model;

class Ticket
{
    public $name;
    public $email;
    public $subject;
    public $message;
    public $imagePath;
    public $telefone;

    public function __construct($name, $email, $subject, $message, $imagePath = null, $telefone = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->imagePath = $imagePath;
        $this->telefone = $telefone;
    }
}
