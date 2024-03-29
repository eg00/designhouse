<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvitationToJoinTeam extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Invitation $invitation, public bool $user_exists)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->user_exists) {
            $url = config('app.client_url').'/settings/teams';

            return $this->markdown('emails.invitations.invite-existing-user')
                ->subject('Invitation to join team '.$this->invitation->team?->name)
                ->with(['invitation' => $this->invitation, 'url' => $url]);
        }
        $url = config('app.client_url').'/register?invitation='.$this->invitation->recipient_email;

        return $this->markdown('emails.invitations.invite-new-user')
            ->subject('Invitation to join team '.$this->invitation->team?->name)
            ->with(['invitation' => $this->invitation, 'url' => $url]);
    }
}
