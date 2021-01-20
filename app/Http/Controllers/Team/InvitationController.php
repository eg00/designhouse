<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class InvitationController extends Controller
{

    public function __construct(
        protected InvitationInterface $invitations,
        protected TeamInterface $teams,
        protected UserInterface $users
    ) {
    }

    public function invite(Request $request, $teamId): JsonResponse
    {
        $team = $this->teams->find($teamId);

        try {
            $this->validate($request, [
                'email' => ['required', 'email'],
            ]);
        } catch (ValidationException) {
        }

        $user = auth()->user();

        // check if the user owns the team
        if (!optional($user)->isOwnerOfTeam($team)) {
            return response()->json([
                'email' => 'You are not the team owner'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // check if the email has a pending invitation
        if ($team->hasPendingInvite($request->input('email'))) {
            return response()->json([
                'email' => 'Email already has a pending invite'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // get the recipient by email
        $recipient = $this->users->findByEmail($request->input('email'));

        // if the recipient does not exist, send invitation to join the team
        if (!$recipient) {
            $this->createInvitation(false, $team, $request->input('email'));

            return response()->json(['message' => 'Invitation sent to user']);
        }

        // check if the team already has the user
        if ($team->hasUser($recipient)) {
            return response()->json([
                'email' => 'This user already a team member'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // send the invitation to the user
        $this->createInvitation(true, $team, $request->input('email'));
        return response()->json(['message' => 'Invitation sent to user']);
    }

    protected function createInvitation(bool $user_exists, Team $team, string $email): void
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => Str::random(32),
        ]);

        Mail::to($email)
            ->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }

    public function resend(): void
    {
    }

    public function respond(): void
    {
    }

    public function destroy(): void
    {
    }
}
