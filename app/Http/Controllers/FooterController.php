<?php

namespace App\Http\Controllers;

use App\Http\Requests\FooterRequest;
use App\Models\Footer;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FooterController extends Controller
{
    protected $footerImagePath;

    public function __construct()
    {
        // Initialize the global variable if needed
        $this->footerImagePath = 'media/footer/';
    }

    // show all footers
    public function index(Request $request): JsonResponse
    {
        $footers = Footer::query()
        ->where('type', $request->type)
        ->get();

        return $this->sendApiResponse($footers, 'Footer list');
    }

    // Single footers
    public function singleFooter($id): JsonResponse
    {
        $footer = Footer::find($id);

        return $this->sendApiResponse($footer, 'Single footer');
    }

    // add footer
    public function addFooter(FooterRequest $request): JsonResponse
    {

        // image add
        if ($request->has('thumnail')) {

            // store image to S3
            $file = $request->file('thumnail');
            $imageName = time() . 'footer.' . $file->extension();
            $file_path = $this->footerImagePath . $request->header('id') . '/';
            $s3_image_url = S3ImageHelpers($file_path, $file, $imageName);
        }

        // add footer
        $footer = Footer::create([
            'name'     => $request->name,
            'thumnail' => $s3_image_url
        ]);

        return $this->sendApiResponse($footer, 'Footer added successfully');
    }

    // edit footers
    public function editFooter($id): JsonResponse
    {
        $footer = Footer::find($id);

        if (empty($footer)) {
            return $this->sendApiResponse('', 'Footer not found');
        }

        return $this->sendApiResponse($footer, 'Footer data');
    }

    // update footer
    public function updateFooter(FooterRequest $request, $id): JsonResponse
    {
        $footer = Footer::find($id);

        if (empty($footer)) {
            return $this->sendApiResponse('', 'Footer not found');
        }

        // image update
        if ($request->has('thumnail')) {

            // delete old image
            $image_path = $footer->thumnail;
            $explode_arr = explode('/', $image_path);
            $end_index = end($explode_arr);

            // image delete form S3
            // Storage::disk('s3')->delete($this->footerImagePath . $request->header('id') . '/' . $end_index . '');

            // store image to S3
            $file = $request->file('thumnail');
            $imageName = time() . 'footer.' . $file->extension();
            $file_path = $this->footerImagePath . $request->header('id') . '/';
            $s3_image_url = S3ImageHelpers($file_path, $file, $imageName);
        }

        // update footer
        $footer->name = $request->name;
        $footer->thumnail = $s3_image_url;
        $footer->update();

        return $this->sendApiResponse($footer, 'Footer updated successfully');
    }

    public function footerColorReset(Request $request, $id): JsonResponse
    {
        $page = Page::find($id);

        if (!$page) {
            return $this->sendApiResponse('', 'Footer not found !');
        }
        $page->footer_text_color = null;
        $page->footer_link_color = null;
        $page->footer_b_color = null;
        $page->footer_heading_color = null;
        $page->checkout_text_color = null;
        $page->checkout_link_color = null;
        $page->checkout_b_color = null;
        $page->checkout_button_color = null;
        $page->checkout_button_text_color = null;
        $page->update();

        return $this->sendApiResponse('', 'Footer color reset successfully');
    }
}
