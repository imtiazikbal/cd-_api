<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers\API\V1\Client\SupportTicket;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\SupportTicket;
use App\Models\TicketComment;
use App\Models\Shop;
use App\Traits\sendApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use sendApiResponse;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'merchant_id' => 'required'
        ]);
        $tickets = SupportTicket::query()->with('attachment', 'comments', 'comments.user', 'comments.attachment')
            ->where('user_id', $request->input('merchant_id'))
            ->orderByDesc('id')
            ->paginate($this->limit());

        return $this->sendApiResponse($tickets);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'merchant_id' => 'required',
            'subject'     => 'required|min:6',
            'content'     => 'required|min:10'
        ]);
        $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();

        $ticket = SupportTicket::query()->create([
            'user_id'   => $request->input('merchant_id'),
            'ticket_id' => mt_rand(11111, 99999),
            'subject'   => $request->input('subject'),
            'content'   => $request->input('content'),
            'shop_name' => $shop->name,
        ]);

        if ($request->hasFile('attachment')) {
            $attachment = $this->uploadAttachment($request);
            $ticket->update(['attachment_id' => $attachment]);
            $ticket->load('attachment');
        }

        return $this->sendApiResponse($ticket, 'Successfully ticket created');
    }

    /**
     * @param $merchant
     * @param $id
     * @return JsonResponse
     */
    public function show($merchant, $id): JsonResponse
    {

        $tickets = SupportTicket::query()->with('comments', 'attachment')->where('user_id', $merchant)->where('id', $id)->get();

        return $this->sendApiResponse($tickets);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function reply(Request $request, $id): JsonResponse
    {
        $request->validate([
            'merchant_id' => 'required',
            'content'     => 'required|min:10',
        ]);
        $ticket = SupportTicket::query()->where('user_id', $request->input('merchant_id'))->where('id', $id)->first();

        if (!$ticket) {
            return response()->json(['message' => 'No Ticket Found, Please Select Valid Ticket Id']);
        }
        $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();

        $comment = TicketComment::query()->create([
            'ticket_id' => $ticket->id,
            'user_id'   => $request->input('merchant_id'),
            'content'   => $request->input('content'),
            'shop_id'   => $request->header('shop-id'),
            'shop_name' => $shop->name,
        ]);

        if ($request->hasFile('attachment')) {
            $attachment = $this->uploadAttachment($request);
            echo $attachment;
            $comment->update(['attachment_id' => $attachment]);
        }

        $comment->load(['attachment', 'user']);

        return $this->sendApiResponse($comment, 'Reply has been sent successfully');
    }

    public function uploadAttachment($request)
    {
        $fileExt = $request->file('attachment')->getClientOriginalExtension();
        $size = $request->file('attachment')->getSize();
        $type = $request->file('attachment')->getMimeType();

        $path = 'media/support-ticket/' . $request->header('id') . '';
        $name = Carbon::now()->format('YmdHis') . '-' . uniqid() . '.' . $fileExt;
        $image = $request->file('attachment')->storeAs($path, $name, 'local');

        $file = $request->file('attachment');
        $resizedImage = imageResize($file, 720, 400);
        $filePath = Attachment::FILEPATH . $request->header('id') . '/';
        $s3FilePath = $filePath . $name;
        S3ImageHelpers($s3FilePath, $resizedImage);

        $attachment = Attachment::query()->create([
            'name' => $name,
            'type' => $type,
            'size' => $size,
            'path' => $image,
        ]);

        return $attachment->id;
    }
}
