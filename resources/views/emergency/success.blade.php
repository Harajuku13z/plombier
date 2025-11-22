@extends('layouts.app')

@section('title', "Demande d'Urgence Envoyée")

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-white py-12 flex items-center">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full shadow-2xl mb-6">
                <i class="fas fa-check text-5xl text-white"></i>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                Demande d'Urgence Envoyée !
            </h1>
            <p class="text-xl text-gray-600">
                Nous avons bien reçu votre demande d'intervention urgente
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 mb-8">
            <div class="space-y-6">
                <div class="flex items-start gap-4 p-4 bg-green-50 rounded-xl">
                    <div class="bg-green-600 text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-phone-volume text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1 text-lg">Appel Imminent</h3>
                        <p class="text-gray-700">
                            Un plombier vous contacte dans les <strong class="text-green-600">15 prochaines minutes</strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-xl">
                    <div class="bg-blue-600 text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-truck text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1 text-lg">Intervention Rapide</h3>
                        <p class="text-gray-700">
                            Selon la gravité, intervention possible sous <strong class="text-blue-600">1 heure</strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-purple-50 rounded-xl">
                    <div class="bg-purple-600 text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1 text-lg">Service Professionnel</h3>
                        <p class="text-gray-700">
                            Plombiers certifiés avec matériel professionnel
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-gradient-to-br from-red-600 to-red-700 text-white rounded-2xl p-6 mb-8 shadow-xl">
            <div class="text-center">
                <p class="text-red-100 mb-3">Vous n'avez pas été contacté dans les 30 minutes ?</p>
                <a href="tel:{{ str_replace(' ', '', setting('company_phone', '')) }}" 
                   class="inline-flex items-center gap-2 bg-white text-red-600 hover:bg-red-50 px-6 py-3 rounded-full font-bold shadow-lg transition text-xl">
                    <i class="fas fa-phone-alt"></i>
                    <span>{{ setting('company_phone', '07 86 48 65 39') }}</span>
                </a>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-2 px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-full font-bold text-lg shadow-lg transition">
                <i class="fas fa-home"></i>
                <span>Retour à l'Accueil</span>
            </a>
        </div>
    </div>
</div>
@endsection

