<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\Attachment;
use App\Models\Shop;
use App\Models\SupportTicket;
use App\Models\TicketComment;
use App\Models\User;
use App\Services\Sms;
use App\Traits\sendApiResponse;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends AdminBaseController
{
    use sendApiResponse;

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $userData = session('user_data');
        $merchants = User::query()->where('role', 'merchant')->get();
        return view('panel.support_ticket.index', compact('merchants'), ['userData' => $userData]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required'],
            'subject' => ['required'],
            'content' => ['required'],
        ]);
        $attachment = null;

        if($request->hasFile('attachment')) {
            $fileExt = $request->file('attachment')->getClientOriginalExtension();
            $size = $request->file('attachment')->getSize();
            $type = $request->file('attachment')->getMimeType();

            $path = '/upload/support-ticket';
            $name = Carbon::now()->format('YmdHis') . '-' . uniqid() . '.' . $fileExt;
            $image = $request->file('attachment')->storeAs($path, $name, 'local');

            $attachment = Attachment::query()->create([
                'name' => $name,
                'type' => $type,
                'size' => $size,
                'path' => $image,
            ]);
        }

        $ticket = SupportTicket::query()->create([
            'user_id'       => $request->input('user_id'),
            'ticket_id'     => mt_rand(11111, 99999),
            'subject'       => $request->input('subject'),
            'content'       => $request->input('content'),
            'status'        => 'opened',
            'attachment_id' => $attachment ?: null,
        ]);

        $shop = Shop::query()->where('user_id', $ticket->user_id)->first();

        if(!$shop->sms_balance < 0.30) {
            $sms = new Sms();
            $sms->sendSmsForSupportTicket($ticket->merchant->phone, $ticket);

            $shop->sms_balance -= .30;
            $shop->update();
        }

        return response()->json($ticket);
    }

    public function tickets(): JsonResponse
    {
        $tickets = SupportTicket::query()
                        ->orderByDesc('id')
                        ->get();
        $tickets->load('merchant');
        $counts = $tickets->groupBy('status')->map->count();
        $data = [
            'tickets' => $tickets,
            'counts'  => $counts,
            'total'   => count($tickets)
        ];

        return $this->sendApiResponse($data);
    }

    public function show($uuid)
    {
        $userData = session('user_data');
        return view('panel.support_ticket.details', compact('uuid'), ['userData' => $userData]);
    }

    public function getTicketDetails($uuid): JsonResponse
    {
        $support_ticket = SupportTicket::query()->with('comments', 'comments.user', 'staff', 'merchant')
            ->where('uuid', $uuid)
            ->first();

        return $this->sendApiResponse($support_ticket);
    }

    public function replyToTicket(Request $request, $id): JsonResponse
    {
        $request->validate([
            'content' => 'required'
        ]);

        $ticket_reply = TicketComment::query()->create([
            'ticket_id' => $id,
            'content'   => $request->input('content'),
            'user_id'   => auth()->id()
        ]);

        if($request->hasFile('attachment')) {
            $fileExt = $request->file('attachment')->getClientOriginalExtension();
            $size = $request->file('attachment')->getSize();
            $type = $request->file('attachment')->getMimeType();

            $path = '/upload/support-ticket/reply';
            $name = Carbon::now()->format('YmdHis') . '-' . uniqid() . '.' . $fileExt;
            $image = $request->file('attachment')->storeAs($path, $name, 'local');

            $attachment = Attachment::query()->create([
                'name' => $name,
                'type' => $type,
                'size' => $size,
                'path' => $image,
            ]);
            $ticket_reply->attachment_id = $attachment->id;
            $ticket_reply->save();
        }

        return $this->sendApiResponse($ticket_reply, 'Reply added successfully');
    }

    public function download($id)
    {
        $attachment = Attachment::query()->findOrFail($id);

        return response()->download($attachment->getAttributes()['path']);
    }

    public function statusUpdate(SupportTicket $supportTicket, Request $request): JsonResponse
    {
        $supportTicket->status = match($request->input('status')) {
            SupportTicket::OPENED     => SupportTicket::OPENED,
            SupportTicket::PROCESSING => SupportTicket::PROCESSING,
            SupportTicket::SOLVED     => SupportTicket::SOLVED,
            SupportTicket::CLOSED     => SupportTicket::CLOSED,
            default                   => $supportTicket->status
        };
        $supportTicket->update();

        $shop = Shop::query()->where('user_id', $supportTicket->user_id)->first();

        if($shop->sms_balance >= 0.30) {
            $sms = new Sms();
            $sms->sendSmsForSupportTicket($supportTicket->merchant->phone, $supportTicket);

            $shop->sms_balance -= .30;
            $shop->update();
        }

        return $this->sendApiResponse($supportTicket, 'Support ticket status updated');
    }

    public function multiSearchSupportTicket(Request $request): JsonResponse
    {
        $supportTicket = SupportTicket::query()
            ->multiSearch($request)
            ->orderByDesc('id')
            ->get();

        if(count($supportTicket) === 0) {
            return $this->sendApiResponse('', 'Data not found !');
        }
        $supportTicket->load('merchant');

        return $this->sendApiResponse($supportTicket, 'Support ticket search result');
    }
}
