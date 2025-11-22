@extends('layouts.app')

@section('title', 'Simulateur de Prix - Photos du Projet')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .progress-bar {
        background-color: {{ setting('primary_color', '#2563eb') }};
    }
    
    .photo-upload-zone {
        border: 3px dashed #cbd5e1;
        transition: all 0.3s ease;
    }
    
    .photo-upload-zone:hover {
        border-color: {{ setting('primary_color', '#2563eb') }};
        background-color: #eff6ff;
    }
    
    .photo-upload-zone.dragover {
        border-color: {{ setting('primary_color', '#2563eb') }};
        background-color: #dbeafe;
        transform: scale(1.02);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-gray-600">Étape {{ $currentStepIndex + 1 }} sur {{ $totalSteps ?? 5 }}</span>
                <span class="text-sm font-semibold" style="color: {{ setting('primary_color', '#2563eb') }};">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="progress-bar h-3 rounded-full transition-all duration-500" 
                     style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                Ajoutez des Photos (Optionnel)
            </h1>
            <p class="text-xl text-gray-600">
                Des photos nous aident à mieux comprendre votre projet
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('simulator.submit', 'photos') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <!-- Upload Zone -->
                <div class="photo-upload-zone rounded-2xl p-12 text-center mb-6">
                    <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        Glissez vos photos ici ou cliquez pour sélectionner
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Maximum 5 photos • 5 MB par image • JPG, PNG
                    </p>
                    <input type="file" 
                           name="photos[]" 
                           id="photo-input"
                           multiple 
                           accept="image/*"
                           class="hidden">
                    <label for="photo-input" 
                           class="inline-block px-6 py-3 text-white rounded-full font-bold cursor-pointer transition hover:opacity-90"
                           style="background-color: {{ setting('primary_color', '#2563eb') }};">
                        <i class="fas fa-images mr-2"></i>
                        Sélectionner des Photos
                    </label>
                </div>

                <!-- Preview Zone -->
                <div id="preview-zone" class="grid grid-cols-2 md:grid-cols-3 gap-4"></div>

                @error('photos.*')
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-4 rounded">
                        <p class="text-red-700 text-sm">{{ $message }}</p>
                    </div>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-6 mb-8">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl flex-shrink-0 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-2">Cette étape est optionnelle</h4>
                        <p class="text-gray-700 text-sm">
                            Vous pouvez passer cette étape si vous n'avez pas de photos. Cependant, des photos nous aident à mieux évaluer votre projet et vous proposer un devis plus précis.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('simulator.previous', 'photos') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 rounded-full font-semibold text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-8 py-4 text-white rounded-full font-bold text-lg shadow-xl transition transform hover:scale-105"
                        style="background-color: {{ setting('primary_color', '#2563eb') }};">
                    <span>Suivant</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('photo-input');
    const uploadZone = document.querySelector('.photo-upload-zone');
    const previewZone = document.getElementById('preview-zone');
    
    // Click to upload
    uploadZone.addEventListener('click', function(e) {
        if (e.target.tagName !== 'LABEL' && e.target.tagName !== 'INPUT') {
            input.click();
        }
    });
    
    // File selection
    input.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });
    
    // Drag & Drop
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });
    
    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
    });
    
    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
        
        // Update input files
        input.files = e.dataTransfer.files;
    });
    
    function handleFiles(files) {
        previewZone.innerHTML = '';
        
        Array.from(files).slice(0, 5).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative rounded-xl overflow-hidden shadow-lg';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-40 object-cover">
                        <div class="absolute top-2 right-2 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                    `;
                    previewZone.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection

