<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotiUserLogout extends Notification
{
    use Queueable;

    public $user ;
    public $action ;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($action, $user)
    {
        // print_r($user);
        $this->user = $user ;
        $this->action = $action ;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Notiy User login')
                    ->line('Thank you for using MWZ!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'action'=> $this->action 
        ];
    }
}
