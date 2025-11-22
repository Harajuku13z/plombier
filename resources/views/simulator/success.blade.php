@extends('layouts.app')

@section('title', 'Demande Envoyée - Simulateur')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-white py-12 flex items-center">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full shadow-2xl mb-6 animate-bounce"
                 style="background-color: #10b981;">
                <i class="fas fa-check text-5xl text-white"></i>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                ✅ Demande Envoyée !
            </h1>
            <p class="text-xl font-bold text-gray-700">
                Nous avons bien reçu votre demande de devis
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 mb-8 border-2 border-green-200">
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 bg-green-100 text-green-800 px-8 py-4 rounded-full font-bold text-lg mb-6 border-2 border-green-300">
                    <i class="fas fa-hashtag text-2xl"></i>
                    <span>Référence : {{ str_pad($submissionId ?? '0000', 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-start gap-4 p-6 bg-blue-50 rounded-xl border-2 border-blue-200">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg"
                         style="background-color: {{ setting('primary_color', '#2563eb') }};">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-xl text-gray-900 mb-2">⏱️ Réponse Rapide</h3>
                        <p class="text-gray-800 font-semibold text-lg">
                            Notre équipe analyse votre demande et vous contacte <strong class="text-blue-600">sous 2 heures</strong> (jours ouvrés)
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-6 bg-blue-50 rounded-xl border-2 border-blue-200">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg"
                         style="background-color: {{ setting('secondary_color', '#0284c7') }};">
                        <i class="fas fa-file-invoice text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-xl text-gray-900 mb-2">📄 Devis Personnalisé</h3>
                        <p class="text-gray-800 font-semibold text-lg">
                            Nous vous enverrons un devis détaillé <strong class="text-blue-600">100% gratuit</strong> et sans engagement
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-6 bg-blue-50 rounded-xl border-2 border-blue-200">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg"
                         style="background-color: #10b981;">
                        <i class="fas fa-user-check text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-xl text-gray-900 mb-2">👨‍🔧 Expert Dédié</h3>
                        <p class="text-gray-800 font-semibold text-lg">
                            Un plombier professionnel étudiera votre projet en détail
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Box -->
        <div class="bg-red-600 text-white rounded-2xl p-6 mb-8 shadow-xl border-2 border-red-700">
            <div class="flex flex-col md:flex-row items-center gap-4">
                <i class="fas fa-exclamation-triangle text-5xl animate-pulse"></i>
                <div class="text-center md:text-left flex-1">
                    <h3 class="font-black text-2xl mb-2">🚨 Besoin urgent ?</h3>
                    <p class="text-red-100 font-semibold text-lg mb-4">Appelez-nous maintenant pour une intervention rapide</p>
                    <a href="tel:{{ str_replace(' ', '', $companySettings['phone'] ?? '') }}" 
                       class="inline-flex items-center gap-3 bg-white text-red-600 hover:bg-red-50 px-8 py-4 rounded-full font-black text-xl shadow-2xl transition transform hover:scale-105">
                        <i class="fas fa-phone-alt text-2xl"></i>
                        <span>{{ $companySettings['phone'] ?? '07 86 48 65 39' }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center space-y-4">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-3 px-10 py-5 text-white rounded-full font-bold text-xl shadow-xl transition transform hover:scale-105"
               style="background-color: {{ setting('primary_color', '#2563eb') }};">
                <i class="fas fa-home text-2xl"></i>
                <span>Retour à l'Accueil</span>
            </a>
            
            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-xl p-4 mt-6">
                <p class="text-gray-800 font-bold text-lg">
                    <i class="fas fa-envelope mr-2 text-yellow-600"></i>
                    Vous n'avez pas reçu d'email ? Vérifiez vos spams ou contactez-nous
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

