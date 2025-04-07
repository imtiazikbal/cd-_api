<?php

namespace App\Http\Controllers\API\V1\Theme;

use App\Http\Controllers\Controller;
use App\Http\Requests\MerchantThemeListRequest;
use App\Http\Requests\ThemeImportRequest;
use App\Http\Requests\ThemeStoreRequest;
use App\Http\Resources\LandingPageSearchResource;
use App\Models\ActiveTheme;
use App\Models\Page;
use App\Models\Theme;
use App\Models\ThemeEdit;
use App\Models\ThemeImage;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpseclib3\Net\SFTP;

class ThemeController extends Controller
{
    use sendApiResponse;

    public function getThemesByType(Request $request): JsonResponse
    {
        $perPage = 10;

        if ($request->query('perPage')) {
            $perPage = $request->query('perPage');
        }

        $query = Theme::query()->with('media');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $themes = $query->orderByDesc('id')->paginate($perPage);

        if ($themes->isEmpty()) {
            return $this->sendApiResponse([], 'No Data found');
        }

        return $this->sendApiResponse($themes);
    }

    public function getListByPage(Request $request, $page): JsonResponse
    {
        $query = ThemeEdit::query()->with('gallery')
            ->where('shop_id', $request->header('shop-id'))
            ->where('page', $page)
            ->where('title', $request->input('title'))
            ->get();

        if ($query->isEmpty()) {
            return $this->sendApiResponse('', 'No data available');
        }

        foreach ($query as $key => $q) {
            $themes = Theme::query()->where('name', $q->theme)->get();
            $query[$key]['themes'] = $themes;
        }

        return $this->sendApiResponse($query);
    }

    public function store(ThemeStoreRequest $request): JsonResponse
    {
        $data['shop_id'] = $request->header('shop-id');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo')->getClientOriginalName();
            $path = '/themes/images';
            $image = $request->file('logo')->storeAs($path, $file, 'local');
            $data['logo'] = $image;
        }
        $data['title'] = $request->input('title');
        $data['content'] = $request->input('content');

        $theme = ThemeEdit::query()->create($data);

        if ($request->input('gallery') !== null) {

            foreach (json_decode($request->input('gallery')) as $item) {

                $img = preg_replace('/^data:image\/\w+;base64,/', '', $item->file_name);
                $fileformat = explode(';', $item->file_name)[0];
                $type = explode('/', $fileformat)[1];

                $im = base64_decode($img);
                $file = time() . '-gallery' . '.' . $type;
                $path = '/themes/images/gallery';

                Storage::disk('local')->put($path . '/' . $file, $im);

                ThemeImage::query()->create([
                    'theme_edit_id' => $theme->id,
                    'type'          => $item->type,
                    'file_name'     => $path . '/' . $file
                ]);
            }
        }
        $theme->load('gallery');

        return $this->sendApiResponse($theme, 'Data Created Successfully');
    }

    public function update(Request $request, $id): JsonResponse
    {

        $data = ThemeEdit::query()->findOrFail($id);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo')->getClientOriginalName();
            $path = '/themes/images';
            $image = $request->file('logo')->storeAs($path, $file, 'local');
            $data->logo = $image;
            $data->save();
        }

        if ($request->input('gallery') !== null) {
            $gallery = [];

            foreach (json_decode($request->input('gallery')) as $item) {

                if (Str::contains($item->file_name, 'base64')) {
                    $img = preg_replace('/^data:image\/\w+;base64,/', '', $item->file_name);

                    $fileformat = explode(';', $item->file_name)[0];
                    $type = explode('/', $fileformat)[1];

                    $im = base64_decode($img);
                    $file = time() . '-gallery' . '.' . $type;
                    $path = '/themes/images/gallery';

                    Storage::disk('local')->put($path . '/' . $file, $im);
                    $image = [
                        'type'      => $item->type,
                        'file_name' => $path . '/' . $file
                    ];
                    array_push($gallery, $image);
                } else {
                    $image = [
                        'type'      => $item->type,
                        'file_name' => $item->file_name
                    ];
                    array_push($gallery, $image);
                }
            }
            $old_gallery = ThemeImage::query()->where('theme_edit_id', $id)->get();

            if ($old_gallery->isNotEmpty()) {
                foreach ($old_gallery as $old_image) {
                    $old_image->delete();
                }
            }

            foreach ($gallery as $gimage) {

                ThemeImage::query()->create([
                    'theme_edit_id' => $id,
                    'type'          => $gimage['type'],
                    'file_name'     => $gimage['file_name']
                ]);
            }
        }

        $data->update($request->except('logo', 'gallery'));
        $data->load('gallery');

        return $this->sendApiResponse($data, 'Data Updated Successfully');
    }

    public function import(ThemeImportRequest $request): JsonResponse
    {
        $theme = Theme::query()->where('id', $request->input('theme_id'))->first();

        if (!$theme) {
            return $this->sendApiResponse('', 'Theme not available right now', 'themeNotFound', [], 401);
        }

        if ($theme->type === 'multiple') {
            $import = ActiveTheme::query()->where('shop_id', $request->header('shop-id'))->where('type', 'multiple')->first();

            if (!$import) {
                $import = new ActiveTheme();
            }
            $import->shop_id = $request->header('shop-id');
            $import->theme_id = $theme->id;
            $import->type = 'multiple';
            $import->footer_id = 1;
            $import->save();
            $import->load(['theme', 'theme.media']);
        } else {
            $latestId = Page::latest()->value('id');

            $sourceFolder = "/var/www/editor.funnelliner.com/templates/main_landing/" . $request->input('type') . "-" . $theme->name;
            $destinationFolder = "/var/www/editor.funnelliner.com/templates/" . $request->header('shop-id') . "/" . $latestId;

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

                $import = ActiveTheme::create([
                    'shop_id'  => $request->header('shop-id'),
                    'theme_id' => $theme->id,
                    'page_id'  => $latestId,
                    'type'     => $request->input('type'),
                ]);

                $import->load(['theme', 'theme.media']);
            } else {
                throw new \Exception('Failed to copy file via SFTP');
            }
        }

        return $this->sendApiResponse($import, 'Theme Imported Successfully');
    }

    public function getMerchantsTheme(MerchantThemeListRequest $request): JsonResponse
    {
        $perPage = 10;

        if ($request->query('perPage')) {
            $perPage = $request->query('perPage');
        }

        $activeThemes = ActiveTheme::query()->where('shop_id', $request->header('shop-id'))
            ->with('media')
            ->with(['page' => function ($query) use ($request) {
                $query->select('id', 'title', 'slug', 'status', 'user_id', 'shop_id', 'created_at')
                    ->where('shop_id', $request->header('shop-id'));
            }])
            ->where('type', $request->input('type'))
            ->orderByDesc('id')
            ->paginate($perPage);

        if ($activeThemes->isEmpty()) {
            return $this->sendApiResponse('', 'No theme has been imported', 'themeNotFound', []);
        }

        return $this->sendApiResponse($activeThemes);
    }

    public function searchLandingPage(string $search): JsonResponse
    {
        $search = '%' . $search . '%';
        $filters = Theme::with('media')
            ->where('theme_name', 'LIKE', $search)
            ->where('type', 'landing')
            ->get();

        return $this->sendApiResponse(LandingPageSearchResource::collection($filters));
    }

    public function searchActiveTheme(Request $request, string $search): JsonResponse
    {
        $search = '%' . $search . '%';
        $filters = ActiveTheme::with('media')
            ->with('theme', function ($q) use ($search) {
                $q->where('theme_name', 'LIKE', $search)->where('type', 'landing');
            })
            ->where('shop_id', $request->header('shop-id'))
            ->get();

        return $this->sendApiResponse($filters, 'Active theme search result');
    }
}