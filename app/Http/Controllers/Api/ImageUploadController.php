<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function store(UploadImageRequest $request): JsonResponse
    {
        try {
            $file = $request->file('image');
            
            // 1. Tạo đường dẫn lưu trữ theo Year/Month
            // storage/app/public/uploads/images/2023/10
            $directory = 'uploads/images/' . date('Y/m');
            
            // 2. Lưu file với tên unique (hash)
            $path = $file->store($directory, 'public');

            // 3. Tạo record trong database
            $image = Image::create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'alt_text' => $request->input('alt_text'),
                'user_id' => auth('sanctum')->id() ?? null, // Nếu có auth sanctum
            ]);

            // 4. Trả về response thành công
            return response()->json([
                'success' => true,
                'message' => 'Upload thành công',
                'data' => [
                    'id' => $image->id,
                    'url' => $image->url, // Accessor từ model
                    'original_name' => $image->filename,
                    'size' => $image->size,
                    'mime_type' => $image->mime_type,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload image from URL
     */
    public function storeFromUrl(\App\Http\Requests\UploadImageUrlRequest $request): JsonResponse
    {
        try {
            $url = $request->input('url');
            $altText = $request->input('alt_text');

            // 1. Download file thông qua HTTP Client (timeout 10s)
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);

            if ($response->failed()) {
                return response()->json(['success' => false, 'message' => 'Không thể tải ảnh từ URL.'], 400);
            }

            // 2. Validate Content-Type
            $mimeType = $response->header('Content-Type');
            if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
                 return response()->json(['success' => false, 'message' => 'URL không phải là hình ảnh hợp lệ.'], 422);
            }
            
            // 3. Validate Size (5MB limit - thủ công vì không qua FormRequest)
            $content = $response->body();
            $size = strlen($content);
            if ($size > 5 * 1024 * 1024) {
                 return response()->json(['success' => false, 'message' => 'Ảnh quá lớn (>5MB).'], 422);
            }

            // 4. Xác định extension & Generate Filename
            // Cố gắng lấy extension từ MimeType, fallback về jpg
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
            ];
            // Xử lý mime type có thể kèm charset (vd: image/jpeg; charset=utf-8)
            $cleanMime = explode(';', $mimeType)[0];
            $ext = $extensions[$cleanMime] ?? 'jpg';
            
            $filename = \Illuminate\Support\Str::random(40) . '.' . $ext;
            
            // 5. Lưu Storage
            $directory = 'uploads/images/' . date('Y/m');
            $path = $directory . '/' . $filename;
            
            Storage::disk('public')->put($path, $content);

            // 6. DB Record
            $image = Image::create([
                'filename' => $filename, // Hoặc basename($url) nếu muốn giữ tên gốc (nhưng rất rủi ro trùng/dài)
                'path' => $path,
                'size' => $size,
                'mime_type' => $cleanMime,
                'alt_text' => $altText,
                'user_id' => auth('sanctum')->id() ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Upload từ URL thành công',
                'data' => [
                    'id' => $image->id,
                    'url' => $image->url,
                    'source' => 'url',
                    'original_name' => $url, // Lưu URL gốc để tham chiếu
                    'size' => $image->size,
                    'mime_type' => $image->mime_type,
                ]
            ], 201);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of images
     */
    public function index(): JsonResponse
    {
        $images = Image::latest()->paginate(20);
        return response()->json($images);
    }

    /**
     * Delete image
     */
    public function destroy($id): JsonResponse
    {
        try {
             $image = Image::findOrFail($id);
             
             // 1. Delete from Storage
             if (Storage::disk('public')->exists($image->path)) {
                 Storage::disk('public')->delete($image->path);
             }
             
             // 2. Delete from DB
             $image->delete();

             return response()->json(['success' => true, 'message' => 'Đã xóa ảnh thành công.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi xóa ảnh: ' . $e->getMessage()], 500);
        }
    }
}
