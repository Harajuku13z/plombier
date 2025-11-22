@extends('layouts.app')

@section('title', 'Demande Envoyée - Simulateur')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-white py-12 flex items-center">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full shadow-2xl mb-6 animate-bounce">
                <i class="fas fa-check text-5xl text-white"></i>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                Demande Envoyée !
            </h1>
            <p class="text-xl text-gray-600">
                Nous avons bien reçu votre demande de devis
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 mb-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 bg-green-50 text-green-700 px-6 py-3 rounded-full font-semibold mb-6">
                    <i class="fas fa-envelope-circle-check text-2xl"></i>
                    <span>Référence : #{{ str_pad($submissionId ?? '0000', 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-xl">
                    <div class="bg-primary text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Réponse Rapide</h3>
                        <p class="text-gray-700">
                            Notre équipe analyse votre demande et vous contacte <strong>sous 2 heures</strong> (jours ouvrés)
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-xl">
                    <div class="bg-secondary text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Devis Personnalisé</h3>
                        <p class="text-gray-700">
                            Nous vous enverrons un devis détaillé <strong>100% gratuit</strong> et sans engagement
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-xl">
                    <div class="bg-green-600 text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Expert Dédié</h3>
                        <p class="text-gray-700">
                            Un plombier professionnel étudiera votre projet en détail
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Box -->
        <div class="bg-gradient-to-br from-red-600 to-red-700 text-white rounded-2xl p-6 mb-8 shadow-xl">
            <div class="flex items-center gap-4">
                <i class="fas fa-exclamation-triangle text-4xl animate-pulse"></i>
                <div>
                    <h3 class="font-bold text-xl mb-1">Besoin urgent ?</h3>
                    <p class="text-red-100 mb-3">Appelez-nous maintenant pour une intervention rapide</p>
                    <a href="tel:{{ str_replace(' ', '', $companySettings['phone'] ?? '') }}" 
                       class="inline-flex items-center gap-2 bg-white text-red-600 hover:bg-red-50 px-6 py-3 rounded-full font-bold shadow-lg transition">
                        <i class="fas fa-phone-alt"></i>
                        <span>{{ $companySettings['phone'] ?? '07 86 48 65 39' }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center space-y-4">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-blue-700 text-white rounded-full font-bold text-lg shadow-lg transition">
                <i class="fas fa-home"></i>
                <span>Retour à l'Accueil</span>
            </a>
            
            <div class="text-gray-600">
                <p class="text-sm">
                    Vous n'avez pas reçu d'email ? Vérifiez vos spams ou contactez-nous
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

