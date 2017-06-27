<?php

namespace BoomCMS\Notifications;

use BoomCMS\Support\Facades\Settings;
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
        $sitename = Settings::get('site.name');
        $sitename = empty($sitename) ? 'BoomCMS' : $sitename;

        return (new MailMessage())
            ->subject(trans('boomcms::notifications.password.subject', [
                'site' => $sitename,
            ]))
            ->view($this->viewName, [
                'user'  => $notifiable,
                'token' => $this->token,
            ]);
    }
}
