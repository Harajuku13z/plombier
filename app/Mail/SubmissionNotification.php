<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SubmissionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;

    /**
     * Create a new message instance.
     */
    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $name = $this->submission->name 
            ?? ($this->submission->first_name . ' ' . $this->submission->last_name);
        
        $mail = $this->subject('🔔 Nouvelle soumission - ' . $name)
                     ->view('emails.submission-notification');
        
        // Attacher les photos si disponibles
        $allPhotos = $this->getAllPhotos();
        
        if (!empty($allPhotos)) {
            Log::info('Attaching photos to submission notification', [
                'submission_id' => $this->submission->id,
                'photo_count' => count($allPhotos)
            ]);
            
            foreach ($allPhotos as $index => $photoPath) {
                try {
                    // Nettoyer le chemin (enlever le préfixe 'storage/' si présent)
                    $cleanPath = str_replace('storage/', '', $photoPath);
                    
                    // Vérifier si le fichier existe
                    if (Storage::disk('public')->exists($cleanPath)) {
                        $fileName = 'photo_' . ($index + 1) . '_' . basename($cleanPath);
                        $filePath = Storage::disk('public')->path($cleanPath);
                        
                        $mail->attach($filePath, [
                            'as' => $fileName,
                            'mime' => Storage::disk('public')->mimeType($cleanPath) ?: 'image/jpeg'
                        ]);
                        
                        Log::info('Photo attached successfully', [
                            'file' => $fileName,
                            'path' => $cleanPath
                        ]);
                    } else {
                        Log::warning('Photo not found for attachment', [
                            'path' => $cleanPath,
                            'original_path' => $photoPath
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error attaching photo to email', [
                        'photo_path' => $photoPath,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return $mail;
    }
    
    /**
     * Récupérer toutes les photos de la soumission
     */
    private function getAllPhotos(): array
    {
        $photos = [];
        
        // 1. Photos du champ 'photos' (urgence ou autres)
        if ($this->submission->photos && is_array($this->submission->photos)) {
            $photos = array_merge($photos, $this->submission->photos);
        }
        
        // 2. Photos du tracking_data (simulateur)
        if (isset($this->submission->tracking_data['photos']) 
            && is_array($this->submission->tracking_data['photos'])) {
            $photos = array_merge($photos, $this->submission->tracking_data['photos']);
        }
        
        // Dédupliquer
        return array_values(array_unique($photos));
    }
}







