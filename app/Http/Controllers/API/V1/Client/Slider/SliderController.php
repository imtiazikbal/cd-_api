<?php

namespace App\Http\Controllers\API\V1\Client\Slider;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\FileSizeLimitExceededException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SliderController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        $sliders = Slider::query()
        ->where('shop_id', $request->header('shop-id'))
        ->orderByDesc('id')
        ->limit(3)
        ->get();


        return $this->sendApiResponse(SliderResource::collection($sliders), 'Slider list');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'image.*' => 'required|image|mimes:png,jpg,jpeg',
                'link.*'  => 'nullable',
            ]);

            $shopId = $request->header('shop_id');
            $userId = $request->header('id');

            if ($request->hasFile('image')) {
                $sliders = $request->file('image');
                $links = $request->input('link') ?? array_fill(0, count($sliders), '#');

                // Add new sliders
                foreach ($sliders as $key => $photo) {

                    $resizedImage = imageResize($photo, 1920, 700);
                    $filePath = Slider::SLIDERIMAGEPATH . $request->header('id') . '/';
                    $s3FilePath = $filePath . time() . rand() . '_slider_image.' . $photo->getClientOriginalExtension();
                    $s3ImageUrl = S3ImageHelpers($s3FilePath, $resizedImage);

                    // add new slider
                    $newSlider = new Slider();
                    $newSlider->link = $links[$key];
                    $newSlider->image = $s3ImageUrl;
                    $newSlider->user_id = $userId;
                    $newSlider->shop_id = $shopId;
                    $newSlider->save();
                }

                return response()->json([
                    'success' => true,
                    'msg'     => 'Slider added successfully',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'msg'     => 'No slider images provided',
            ], 400);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 400);
        } catch (FileSizeLimitExceededException $e) {
            return response()->json([
                'success' => false,
                'message' => 'The image size exceeds the maximum limit of 1MB.',
            ], 413);
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
            $slider = Slider::where('shop_id', $request->header('shop-id'))->findOrFail($id);

            $imagePath = $slider->image;
            $explodeArr = explode('/', $imagePath);
            $endIndex = end($explodeArr);

            $filePath = Slider::SLIDERIMAGEPATH . $request->header('id') . '/' . $endIndex . '';
            Storage::disk('s3')->delete($filePath);

            $slider->delete();

            return response()->json([
                'success' => true,
                'msg'     => 'Slider deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'msg'     => 'Slider not found'
            ], 404); // Not Found status code
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the slider'
            ], 500); // Internal Server Error status code
        }
    }
}
