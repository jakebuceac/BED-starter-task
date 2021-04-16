<?php
namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoggedInNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $ip;

    public function __construct(User $user, $ip)
    {
        $this->user = $user;
        $this->ip = $ip;
    }
    public function build()
    {
        return $this->view('mail.mymail_template')
            ->with([
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'userIp' => $this->ip,
            ]);
    }
}
