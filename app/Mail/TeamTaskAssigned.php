<?php

namespace App\Mail;

use App\Models\TeamTask;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamTaskAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $teamTask;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param TeamTask $teamTask
     * @param User $user
     */
    public function __construct(TeamTask $teamTask, User $user)
    {
        $this->teamTask = $teamTask;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Team Task Assigned',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.team_task_assigned',
            with: ['teamTask' => $this->teamTask, 'user' => $this->user],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
