<?php

namespace App\Http\Controllers\API\V1\Client\Banner;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        $banners = Banner::where('shop_id', $request->header('shop-id'))
        ->orderByDesc('id')
        ->limit(3)
        ->get();

        return $this->sendApiResponse(BannerResource::collection($banners), 'Banner list');
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image.*' => 'required|image|mimes:png,jpg,jpeg',
                'link.*'  => 'nullable',
            ]);

            $shopId = $request->header('shop_id');
            $userId = $request->header('id');

            $banners = $request->file('image');
            $links = $request->input('link') ?? array_fill(0, count($banners), '#');

            foreach ($banners as $key => $photo) {

                $resizedImage = imageResize($photo, 1920, 700);
                $filePath = Banner::BANNERIMAGEPATH . $request->header('id') . '/';
                $s3FilePath = $filePath . time() . rand() . '_banner_image.' . $photo->getClientOriginalExtension();
                $s3ImageUrl = S3ImageHelpers($s3FilePath, $resizedImage);

                $newBanner = new Banner();
                $newBanner->link = $links[$key];
                $newBanner->image = $s3ImageUrl;
                $newBanner->user_id = $userId;
                $newBanner->shop_id = $shopId;
                $newBanner->save();
            }

            return response()->json([
                'success' => true,
                'msg'     => 'Banner updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $banner = Banner::where('shop_id', $request->header('shop-id'))->findOrFail($id);
            $imagePath = public_path($banner->image);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // delete old image
            $imagePath = $banner->image;
            $explodeArr = explode('/', $imagePath);
            $endIndex = end($explodeArr);

            // image delete form S3
            Storage::disk('s3')->delete(Banner::BANNERIMAGEPATH . $request->header('id') . '/' . $endIndex . '');


            // Delete slider from the database
            $banner->delete();

            return response()->json([
                'success' => true,
                'msg'     => 'Banner deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'msg'     => 'Banner not found'
            ], 404); // Not Found status code
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the slider',
            ], 500); // Internal Server Error status code
        }
    }
}
