<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMessageRequest;
use App\Http\Requests\Admin\StoreRepliesRequest;
use App\Models\Admin\Message;
use App\Models\Admin\Reply;
use App\Models\Admin\Role;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (
            $this->isAdmin($request->user()) ||
            $this->isLoanOfficer($request->user())
            || $this->isBranchManager($request->user())
            || $this->isCashier($request->user())
        ) {
            $message = Message::where('user_id', $request->user()->user_id)
                ->get();

            // messages for authenticated users

            return $this->success($message);

            // $role = Role::get();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        if (
            $this->isAdmin($request->user()) ||
            $this->isLoanOfficer($request->user())
            || $this->isBranchManager($request->user())
            || $this->isCashier($request->user())
        ) {
            $request->validated($request->all());

            $message = Message::create([
                'message_title' => $request->input('message_title'),
                'message_desc' => $request->input('message_desc'),
                'respond' => $request->input('respond'),
                'user_id' => $request->user()->user_id,
                'branch_id' => $request->input('branches')['value'],
                'role_id' => $request->input('to')['value'],
            ]);
        }
    }

    public function storeReplies(StoreRepliesRequest $request)
    {
        if (
            $this->isAdmin($request->user()) ||
            $this->isLoanOfficer($request->user())
            || $this->isBranchManager($request->user())
            || $this->isCashier($request->user())
        ) {
            $request->validated($request->all()); //replyMessageRelation

            $message_replies = Reply::updateOrCreate([
                'replies' => $request->input('replies')

            ], [
                'replies' => $request->input('replies'),
                'message_id' => $request->message_id,
            ]);

            Message::where('message_id', $request->message_id)
                ->update([
                    'status' => 1
                ]);
            return $this->success($message_replies);
        }
    }

    public function getMessages(Request $request)
    {
        if (
            $this->isAdmin($request->user()) ||
            $this->isLoanOfficer($request->user())
            || $this->isBranchManager($request->user())
            || $this->isCashier($request->user())
        ) {
            $message = Message::where('user_id', '!=', $request->user()->user_id)
                ->where('role_id', $request->user()->role_id)
                ->where('status', 0)
                ->get();

            $message_for_admin_to_reply = Message::where('status', 1)
                ->with('messageReplyRelation')
                ->get();
            // messages for authenticated users

            return $this->success(['message' => $message, 'replied_msg' => $message_for_admin_to_reply]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getReplies(Request $request)
    {
        if (
            $this->isAdmin($request->user()) ||
            $this->isLoanOfficer($request->user())
            || $this->isBranchManager($request->user())
            || $this->isCashier($request->user())
        ) {
            $replies = Reply::where('message_id', '!=', $request->message_id)
                ->with('replyMessageRelation')->get();


            return $this->success($replies);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
