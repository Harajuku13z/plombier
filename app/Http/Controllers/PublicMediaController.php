<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicMediaController extends Controller
{
    /**
     * Stream a submission photo stored on the public disk.
     */
    public function submissionPhoto(Request $request, int $id, string $file)
    {
        // Sanitize filename
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $file)) {
            abort(404);
        }

        // Essayer d'abord le chemin pour les anciennes soumissions
        $relativePath = "uploads/submissions/{$id}/{$file}";
        
        // Si pas trouvé, essayer le chemin pour les urgences
        if (!Storage::disk('public')->exists($relativePath)) {
            $relativePath = "submissions/{$id}/{$file}";
        }

        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404);
        }

        $mime = Storage::disk('public')->mimeType($relativePath) ?: 'application/octet-stream';

        return response()->stream(function () use ($relativePath) {
            $stream = Storage::disk('public')->readStream($relativePath);
            if (is_resource($stream)) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Content-Disposition' => 'inline; filename="' . basename($relativePath) . '"',
        ]);
    }
    
    /**
     * Serve any file from public storage (for emergency photos, etc.)
     */
    public function serveFile(Request $request, string $path)
    {
        try {
            // Sécurité : vérifier que le chemin ne contient que des caractères autorisés
            if (preg_match('/\.\./', $path) || !preg_match('/^[A-Za-z0-9\/._-]+$/', $path)) {
                abort(404, 'Invalid path');
            }

            // Normaliser le chemin (enlever les slashes en début)
            $path = ltrim($path, '/');

            if (!Storage::disk('public')->exists($path)) {
                \Log::warning('File not found in storage', [
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'exists' => file_exists(storage_path('app/public/' . $path))
                ]);
                abort(404, 'File not found: ' . $path);
            }

            $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';

            return response()->stream(function () use ($path) {
                $stream = Storage::disk('public')->readStream($path);
                if (is_resource($stream)) {
                    fpassthru($stream);
                    fclose($stream);
                }
            }, 200, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error serving file', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Error serving file');
        }
    }
}
