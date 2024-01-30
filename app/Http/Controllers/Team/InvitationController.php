<?php

declare(strict_types=1);

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
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

    public function invite(Request $request, int $teamId): JsonResponse
    {
        /** @var Team $team */
        $team = $this->teams->find($teamId);

        try {
            $this->validate($request, [
                'email' => ['required', 'email'],
            ]);
        } catch (ValidationException) {
        }

        $user = auth()->user();

        // check if the user owns the team
        if (! optional($user)->isOwnerOfTeam($team)) {
            return response()->json([
                'email' => 'You are not the team owner',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // check if the email has a pending invitation
        if ($team->hasPendingInvite($request->input('email'))) {
            return response()->json([
                'email' => 'Email already has a pending invite',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // get the recipient by email
        $recipient = $this->users->findByEmail($request->input('email'));

        // if the recipient does not exist, send invitation to join the team
        if (! $recipient) {
            $this->createInvitation(false, $team, $request->input('email'));

            return response()->json(['message' => 'Invitation sent to user']);
        }

        // check if the team already has the user
        if ($team->hasUser($recipient)) {
            return response()->json([
                'email' => 'This user already a team member',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // send the invitation to the user
        $this->createInvitation(true, $team, $request->input('email'));

        return response()->json(['message' => 'Invitation sent to user']);
    }

    protected function createInvitation(bool $user_exists, Team $team, string $email): void
    {
        /** @var Invitation $invitation */
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => Str::random(32),
        ]);

        Mail::to($email)
            ->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }

    public function resend(int $id): JsonResponse
    {
        /** @var Invitation $invitation */
        $invitation = $this->invitations->find($id);

        $this->authorize('respond', $invitation);
        // check if the user owns the team
        if (! optional(auth()->user())->isOwnerOfTeam($invitation->team)) {
            return response()->json([
                'email' => 'You are not the team owner',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // get the recipient by email
        $recipient = $this->users->findByEmail($invitation->recipient_email);

        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinTeam($invitation, ! is_null($recipient)));

        return response()->json(['message' => 'Invitation resent to user']);

    }

    public function respond(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required', 'boolean'],
        ]);

        $token = $request->token;
        $decision = $request->decision;
        /** @var Invitation $invitation */
        $invitation = $this->invitations->find($id);

        // check if the invitation belongs to this user
        //        if($invitation->recipient_email !== auth()->user()->email) {
        //            return  \response()->json([
        //                'message' => 'This is not your invitation'
        //            ], Response::HTTP_UNAUTHORIZED);
        //        }

        $this->authorize('respond', $invitation);

        // check to make sure that the tokens match
        if ($invitation->token !== $token) {
            return \response()->json([
                'message' => 'The token is invalid',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($decision && $invitation->team) {
            $user->teams()->attach($invitation->team->id);
            $this->invitations->addUserToTeam($invitation->team, $user->id);
        }

        $invitation->delete();

        return \response()->json([
            'message' => 'Successful',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $invitation = $this->invitations->find($id);

        $this->authorize('delete', $invitation);

        $invitation->delete();

        return \response()->json(['message' => 'Successful']);
    }
}
