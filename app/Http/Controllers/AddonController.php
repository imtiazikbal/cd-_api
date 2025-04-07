<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Addons;
use App\Models\MyAddons;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AddonController extends Controller
{
    // add
    public function addnew(Request $request): JsonResponse
    {
        // data store to DB
        $addons = Addons::create([
            'name'         => $request->name,
            'description'  => $request->description,
            'amount'       => $request->amount,
            'payment_type' => $request->payment_type,
            'status'       => $request->status,
        ]);

        if ($request->hasFile('addons_image')) {

            $file = $request->file('addons_image');
            $resizedImage = imageResize($file, 720, 400);
            $filePath = Addons::ADDONSIMAGEPATH . $request->header('id') . '/';
            $s3FilePath = $filePath . time() . rand() . '_addons_image.' . $file->getClientOriginalExtension();
            $s3ImageUrl = S3ImageHelpers($s3FilePath, $resizedImage);

            $media = new Media();
            $media->name = $s3ImageUrl;
            $media->parent_id = $addons->id;
            $media->type = 'addons';
            $media->parent_type = 'App\Models\Addons';
            $media->save();
        }
        $addons->load('addons_image');
        // response to api

        if (!$addons) {
            return $this->sendApiResponse('', 'Not found', 'NotFound');
        }

        return $this->sendApiResponse($addons, 'Addon added successfully');
    }

    //update
    public function AddonsUpdate($id, Request $request)
    {
        $addons = Addons::with('addons_image')->find($id);

        if (!$addons) {
            return $this->sendApiResponse('', Addons::NOTFOUNDMSG, 'NotFound');
        }
        $addons->name = $request->name;
        $addons->description = $request->description;
        $addons->amount = $request->amount;
        $addons->payment_type = $request->payment_type;
        $addons->status = $request->status ? $request->status : $addons->status;

        $addons->update();

        if ($request->has('addons_image')) {

            $file = $request->file('addons_image');
            $resizedImage = imageResize($file, 720, 400);
            $filePath = Addons::ADDONSIMAGEPATH . $request->header('id') . '/';
            $s3FilePath = $filePath . time() . rand() . '_addons_image.' . $file->getClientOriginalExtension();
            $s3ImageUrl = S3ImageHelpers($s3FilePath, $resizedImage);

            Media::query()->where('type', 'addons')->where('parent_id', $addons->id)->update([
                'name'        => $s3ImageUrl,
                'parent_type' => 'App\Models\Addons'
            ]);
        }
        $addons->load('addons_image');

        // return response
        return $this->sendApiResponse($addons, 'Addons update successfully');
    }

    //delete
    public function delete(Request $request, int $id): JsonResponse
    {
        $addons = Addons::with('addons_image')->find($id);
        $imgPath = $addons->addons_image->name;

        if (!$addons) {
            return $this->sendApiResponse('', Addons::NOTFOUNDMSG, 'NotFound');
        }

        // image delete from directory & Database
        $explodeArr = explode('/', $imgPath);
        $endIndex = end($explodeArr);
        $filePath = Addons::ADDONSIMAGEPATH . $request->header('id') . '/' . $endIndex . '';
        Storage::disk('s3')->delete($filePath);

        $addons->delete();

        // return response
        return $this->sendApiResponse('', 'Addons deleted successfully');
    }

    public function list()
    {
        // list
        $list = Addons::with('addons_image')->get();

        // return response
        return $this->sendApiResponse($list, 'All Addons');
    }

    public function install(Request $request)
    {

        $addons = Addons::with('addons_image')->where('id', $request->addons_id)->first();

        if (!$addons) {
            return $this->sendApiResponse('', Addons::NOTFOUNDMSG, 'NotFound');
        }

        $MyAddons = MyAddons::query()
            ->where('shop_id', $request->header('shop-id'))
            ->where('addons_id', $request->addons_id)
            ->first();

        if (!$MyAddons) {
            $MyAddonsU = new MyAddons();
            $MyAddonsU->shop_id = $request->header('shop-id');
            $MyAddonsU->addons_id = $request->addons_id;
            $MyAddonsU->status = $request->status;
            $MyAddonsU->save();

            return $this->sendApiResponse($MyAddonsU, 'Addons install successfully');
        }

        return $this->sendApiResponse('', 'Addons already installed');
    }

    public function showlist(Request $request): JsonResponse
    {
        $MyAddonsL = MyAddons::query()
            ->with('addons_image_details', 'addons_details')
            ->where('shop_id', $request->header('shop-id'))
            ->orderByDesc('id')
            ->get();

        if ($MyAddonsL->isEmpty()) {
            return $this->sendApiResponse('', 'No data available', 'NotAvailable');
        }

        return $this->sendApiResponse($MyAddonsL);
    }

    public function uninstall($id, Request $request): JsonResponse
    {
        $addons = MyAddons::where('shop_id', $request->header('shop-id'))
            ->find($id)
            ->first();

        if (!$addons) {
            return $this->sendApiResponse('', Addons::NOTFOUNDMSG, 'NotFound');
        }
        // uninstall
        MyAddons::where('shop_id', $request->header('shop-id'))->find($id)->delete();

        // return response
        return $this->sendApiResponse('', 'Addons uninstall successfully');
    }

    // Addons status Active or InActive
    public function ActiveInactiveStatus($id)
    {

        $addons = MyAddons::find($id);

        if (!$addons) {
            return $this->sendApiResponse('', 'Not found');
        }

        if ($addons->status == 0) {
            $addons->status = 1;
            $addons->update();
            $msg = 'Addons status activated';
        } else {
            $addons->status = 0;
            $addons->update();
            $msg = 'Addons status inactivated';
        }

        // return response
        return $this->sendApiResponse('', $msg);
    }

    // Addons searching
    public function AddonsSearch($search)
    {
        $filter = Addons::where('name', 'LIKE', '%' . $search . '%')->get();

        return $this->sendApiResponse($filter, 'Search result');
    }
}
