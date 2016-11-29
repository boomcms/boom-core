<?php

namespace BoomCMS\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    protected $viewName = 'boomcms::email.password';

    /**
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(trans('boomcms:notifications.password.subject'))
            ->view($this->viewName, [
                'user'  => $notifiable,
                'token' => $this->token,
            ]);
    }
}