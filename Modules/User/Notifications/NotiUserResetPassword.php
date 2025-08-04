<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\User\Emails\EmailUserResetPassword;

class NotiUserResetPassword extends Notification
{
    use Queueable;
    public $token;
    public $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
         $this->token = $token ;

          $order = [
            0=>['1','เสือ','500','1','500'],
            1=>['2','กางเกง','250','1','250']
        ];

        $this->order = $order;

        // $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
                    ->line('คุณสามารถเปลี่ยนรหัสผ่านได้จากลิงค์ด้านล่าง')
                    ->action('Reset Password', 'http://dev.mwz.local/admin/reset-password/'.$this->token)
                    ->line('ลิงค์สำหรับเปลี่ยนรหัสผ่าน : http://dev.mwz.local/admin/reset-password/'.$this->token)
                    ->line('Thank you for using our application!')
                    ->markdown('emails.user.forget_password',['order'=>$this->order]);
         
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
            //
        ];
    }
}
