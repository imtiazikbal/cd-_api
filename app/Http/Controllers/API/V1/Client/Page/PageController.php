<?php

namespace App\Http\Controllers\API\V1\Client\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\MultiThemeUpdateRequest;
use App\Http\Requests\PageDuplicateRequest;
use App\Http\Requests\PageRequest;
use App\Models\ActiveTheme;
use App\Models\Page;
use App\Models\CheckFormDesign;
use App\models\Footer;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpseclib3\Net\SFTP;

class PageController extends Controller
{
    use sendApiResponse;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page = Page::query()
            ->select('id', 'title', 'slug', 'status', 'user_id', 'shop_id', 'created_at')
            ->where('shop_id', $request->header('shop-id'))
            ->orderByDesc('id')
            ->get();

        if ($page->isEmpty()) {
            return $this->sendApiResponse('', 'No data available right now', 'NotAvailable');
        }

        return $this->sendApiResponse($page, 'All pages');
    }

    public function footerlist(Request $request): JsonResponse
    {
        $footers = Footer::query()
            ->orderByDesc('id')
            ->get();

        if ($footers->isEmpty()) {
            return $this->sendApiResponse('', 'No Data available');
        }

        return $this->sendApiResponse($footers);
    }

    public function checkoutdesignlist(Request $request): JsonResponse
    {
        $checkouts = CheckFormDesign::query()
            ->orderByDesc('id')
            ->get();

        if ($checkouts->isEmpty()) {
            return $this->sendApiResponse('', 'No Data available');
        }

        return $this->sendApiResponse($checkouts);
    }

    /**
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function store(PageRequest $request): JsonResponse
    {
        $page = new Page();
        $page->user_id = $request->header('id');
        $page->shop_id = $request->header('shop-id');
        $page->title = $request->input('title');
        $page->descriptions = $request->input('page_content');
        $page->theme = $request->input('theme');
        $page->status = $request->input('status') ?: 1;
        $page->product_id = $request->input('product_id');
        $page->save();
        $page->load('product');

        if (!$page) {
            return $this->sendApiResponse('', 'Something went wrong', 'UnknownError');
        }

        return $this->sendApiResponse($page, 'Page created successfully');
    }

    /**
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function show(Request $request, $slug): JsonResponse
    {
        $page = Page::query()->with('product', 'product.main_image', 'product.other_images')
            ->where('slug', $slug)
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        if (!$page) {
            return $this->sendApiResponse('', Page::DATANOTFOUND, 'NotFound');
        }

        return $this->sendApiResponse($page);
    }

    public function edit(Request $request, $id): JsonResponse
    {
        $activepage = Page::with('themes', 'product', 'activeFooter')
            ->where('shop_id', $request->header('shop-id'))
            ->where('id', $id)
            ->first();

        if (!$activepage) {
            return $this->sendApiResponse('', Page::DATANOTFOUND, 'NotFound');
        }

        return $this->sendApiResponse($activepage);
    }

    /**
     * @param PageRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function pageUpdate(PageRequest $request, $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $page = Page::query()->where('id', $id)->where('shop_id', $request->header('shop-id'))->first();

            if (!$page) {
                return $this->sendApiResponse('', Page::DATANOTFOUND, 'NotFound');
            }

            if ($request->filled('title')) {
                $page->title = $request->input('title');
                $page->slug = Str::slug($request->input('title'));
            }
            $page->descriptions = $request->input('descriptions');
            $page->user_id = $request->header('id');
            $page->shop_id = $request->header('shop-id');
            $page->status = $request->input('status') ?: 1;
            $page->product_id = $request->input('product_id');
            $page->note = $request->input('note');

            if ($request->has('logo')) {
                // image delete form S3
                $imagePath = $page->logo;

                if ($imagePath) {
                    $explodeArr = explode('/', $imagePath);
                    $endIndex = end($explodeArr);
                    Storage::disk('s3')->delete('media/theme-logo/' . $request->header('id') . '/' . $endIndex . '');
                }

                $file = $request->file('logo');
                $resizedImage = imageResize($file, 720, 400);
                $filePath = 'media/logo/' . $request->header('id') . '/';
                $s3FilePath = $filePath . time() . rand() . '_logo.' . $file->getClientOriginalExtension();
                $s3ImageUrl = S3ImageHelpers($s3FilePath, $resizedImage);

                // updated new image
                $page->logo = $s3ImageUrl;
            }

            $page->fb = $request->input('fb');
            $page->twitter = $request->input('twitter');
            $page->linkedin = $request->input('linkedin');
            $page->instagram = $request->input('instagram');
            $page->youtube = $request->input('youtube');
            $page->address = $request->input('address');
            $page->phone = $request->input('phone');
            $page->email = $request->input('email');
            $page->footer_text_color = $request->input('footer_text_color');
            $page->footer_link_color = $request->input('footer_link_color');
            $page->footer_b_color = $request->input('footer_b_color');
            $page->footer_heading_color = $request->input('footer_heading_color');
            $page->checkout_text_color = $request->input('checkout_text_color');
            $page->checkout_link_color = $request->input('checkout_link_color');
            $page->checkout_b_color = $request->input('checkout_b_color');
            $page->checkout_button_color = $request->input('checkout_button_color');
            $page->checkout_button_text_color = $request->input('checkout_button_text_color');
            $page->checkout_button_text = $request->input('checkout_button_text');
            $page->order_title = $request->input('order_title');
            $page->update();

            // Active theme update
            $activeTheme = ActiveTheme::where('theme_id', $page->theme)
                ->where('shop_id', $request->header('shop-id'))
                ->where('type', 'landing')
                ->where('page_id', $page->id)
                ->first();

            if($activeTheme) {
                if($request->filled('checkout_form_id')) {
                    $activeTheme->checkout_form_id = $request->input('checkout_form_id');
                }

                if($request->filled('footer_id')) {
                    $activeTheme->footer_id = $request->input('footer_id');
                }
                $activeTheme->update();
            }

            $page->load('product');
            $page->load('activeTheme');
            $page->load('activeFooter');

            return $this->sendApiResponse($page, 'Page updated successfully');
        });
    }

    public function pageCopy(PageDuplicateRequest $request, $id): JsonResponse
    {
        $originalPage = Page::where('shop_id', $request->header('shop-id'))->findOrFail($id);
        $latestId = Page::latest()->value('id');
        //$newId = $latestId + 1;
        $activeTheme = ActiveTheme::where('shop_id', $request->header('shop-id'))
            ->where('page_id', $originalPage->id)
            ->first();

        $newPage = $originalPage->replicate();
        $newPage->title = $request->input('title');
        $newPage->slug = Str::slug($request->input('title'));
        $newPage->product_id = $request->input('product_id');
        $newPage->save();

        if ($activeTheme) {
            $theme = $activeTheme->replicate();
            $theme->page_id = $newPage->id;
            $theme->save();
        } else {
            return $this->sendApiResponse('', 'Active theme not found', 'NotFound');
        }

        // $sourceFolder = "/var/www/html/templates/" . $request->header('shop-id') . "/" . $id;
        // $destinationFolder = "/var/www/html/templates/" . $request->header('shop-id') . "/" . $newPage->id;
        // if (!File::exists($destinationFolder)) {
        //     File::makeDirectory($destinationFolder, 0755, true);
        // }

        // $filename = 'index.html';

        // $sourceFilePath = $sourceFolder . '/' . $filename;
        // if (File::exists($sourceFilePath)) {
        //     $destinationFilePath = $destinationFolder . '/' . $filename;
        //     File::copy($sourceFilePath, $destinationFilePath);
        // }

        $sourceFolder = "/var/www/editor.funnelliner.com/templates/" . $request->header('shop-id') . "/" . $id;
        $destinationFolder = "/var/www/editor.funnelliner.com/templates/" . $request->header('shop-id') . "/" . $newPage->id;

        $serverIp = env('EDITOR_SERVER_IP');
        $serverPort = env('EDITOR_SERVER_PORT');
        $serverUser = env('EDITOR_SERVER_USER');
        $serverPass = env('EDITOR_SERVER_PASS');

        $sftp = new SFTP($serverIp, $serverPort);

        if (!$sftp->login($serverUser, $serverPass)) {

            throw new \Exception('SFTP login failed');
        }

        $filename = 'index.html';
        $sourceFilePath = $sourceFolder . '/' . $filename;
        $destinationFilePath = $destinationFolder . '/' . $filename;

        if (!$sftp->file_exists($sourceFilePath)) {
            throw new \Exception('Source file does not exist: ' . $sourceFilePath);
        }

        if (!$sftp->is_dir($destinationFolder)) {
            $sftp->mkdir($destinationFolder, 0755, true);
        }

        if ($sftp->put($destinationFilePath, $sftp->get($sourceFilePath))) {

        } else {
            throw new \Exception('Failed to copy file via SFTP');
        }

        return $this->sendApiResponse($newPage, 'Page duplicated successfully');
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $page = Page::query()->find($id);

        if (!$page) {
            return $this->sendApiResponse('', Page::DATANOTFOUND, 'NotFound');
        }

        $theme = ActiveTheme::query()
            ->where('page_id', $page->id)
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        $directoryPath = "/var/www/editor.funnelliner.com/templates/" . $request->header('shop-id') . "/" . $page->id;
        $sftp = new SFTP(env('EDITOR_SERVER_IP'), env('EDITOR_SERVER_PORT'));

        if (!$sftp->login(env('EDITOR_SERVER_USER'), env('EDITOR_SERVER_PASS'))) {
            return $this->sendApiResponse('', 'SFTP login failed', 'Error');
        }

        if ($theme) {
            $theme->delete();
            $sftp->delete($directoryPath, true);
        }
        $page->delete();

        return $this->sendApiResponse('', 'Page deleted successfully');
    }

    public function multiPageUpdate(MultiThemeUpdateRequest $request): JsonResponse
    {
        $activeTheme = ActiveTheme::where('theme_id', $request->theme_id)
        ->where('shop_id', $request->header('shop-id'))
        ->where('type', 'multiple')
        ->with('shop', function ($query) {
            $query->select('id', 'multipage_color', 'shop_id');
        })
        ->first();

        if(!$activeTheme) {
            return $this->sendApiResponse('', 'Multi page not found');
        }

        $activeTheme->footer_id = $request->footer_id;
        $activeTheme->update();

        $activeTheme->shop->update([
            'multipage_color' => $request->multipage_color
        ]);

        return $this->sendApiResponse($activeTheme, 'Multi page updated successfully');
    }

    public function getMultiPageWithFooterId(Request $request): JsonResponse
    {
        $activeTheme = ActiveTheme::where('theme_id', $request->theme_id)
        ->select('id', 'shop_id', 'footer_id')
        ->where('shop_id', $request->header('shop-id'))
        ->where('type', 'multiple')
        ->with('shop', function ($query) {
            $query->select('id', 'multipage_color', 'shop_id');
        })
        ->first();

        if(!$activeTheme) {
            return $this->sendApiResponse('', 'Multi page not found');
        }

        return $this->sendApiResponse($activeTheme, 'Multi page & footer id');
    }
}