<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class BlockNonFranceAndBots
{
    /**
     * Liste des user agents de bots à bloquer
     */
    private $botPatterns = [
        'bot', 'crawl', 'spider', 'scraper', 'curl', 'wget', 'python', 'java',
        'headless', 'phantom', 'selenium', 'webdriver', 'automation',
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
        'yandexbot', 'facebookexternalhit', 'twitterbot', 'linkedinbot',
        'whatsapp', 'telegram', 'slackbot', 'discordbot',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Vérifier si c'est un bot
        if ($this->isBot($request)) {
            Log::warning('Bot detected and blocked from simulator', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);
            
            return response()->view('errors.bot-blocked', [], 403);
        }

        // 2. Vérifier le blocage géographique (France uniquement)
        $blockNonFrance = Setting::get('block_non_france', true); // Activé par défaut pour le simulateur
        
        if ($blockNonFrance) {
            $isAllowed = $this->isFranceOrAllowed($request);
            
            if (!$isAllowed) {
                Log::warning('Non-France access blocked from simulator', [
                    'ip' => $request->ip(),
                    'country' => $this->getCountry($request),
                    'url' => $request->fullUrl(),
                ]);
                
                return response()->view('errors.geo-blocked', [], 403);
            }
        }

        return $next($request);
    }

    /**
     * Détecter si c'est un bot
     */
    private function isBot(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        // Vérifier si le user agent correspond à un pattern de bot
        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return true;
            }
        }
        
        // Vérifier l'absence de user agent (souvent des bots)
        if (empty($userAgent)) {
            return true;
        }
        
        // Vérifier les headers suspects
        if ($request->header('X-Requested-With') === 'XMLHttpRequest' && 
            empty($request->header('Referer'))) {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifier si la requête vient de France ou d'un pays autorisé
     */
    private function isFranceOrAllowed(Request $request): bool
    {
        $ipAddress = $request->ip();
        
        // IPs locales toujours autorisées (développement)
        if (in_array($ipAddress, ['127.0.0.1', '::1', 'localhost'])) {
            return true;
        }
        
        try {
            // Utiliser le service de géolocalisation existant
            if (class_exists('\App\Services\IpGeolocationService')) {
                $geoService = new \App\Services\IpGeolocationService();
                $location = $geoService->getLocationFromIp($ipAddress);
            } else {
                // Fallback simple avec ip-api.com
                $response = @file_get_contents("http://ip-api.com/json/{$ipAddress}?fields=status,country,countryCode");
                $location = $response ? json_decode($response, true) : [];
            }
            
            // Pays et territoires autorisés : France + Suisse + DOM-TOM
            $allowedCountries = [
                'FR', 'France',
                'CH', 'Switzerland', 'Suisse',
                'RE', 'Réunion', 'Reunion',
                'GP', 'Guadeloupe',
                'MQ', 'Martinique',
                'GF', 'Guyane', 'French Guiana',
                'YT', 'Mayotte',
                'NC', 'Nouvelle-Calédonie', 'New Caledonia',
                'PF', 'Polynésie française', 'French Polynesia',
                'PM', 'Saint-Pierre-et-Miquelon',
                'BL', 'Saint-Barthélemy',
                'MF', 'Saint-Martin',
                'WF', 'Wallis-et-Futuna',
            ];
            
            $countryCode = strtoupper($location['country_code'] ?? $location['countryCode'] ?? '');
            $countryName = $location['country'] ?? '';
            
            $isAllowed = in_array($countryCode, $allowedCountries) || 
                         in_array($countryName, $allowedCountries);
            
            Log::info('Geographic check', [
                'ip' => $ipAddress,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'allowed' => $isAllowed,
            ]);
            
            return $isAllowed;
            
        } catch (\Exception $e) {
            Log::error('Error checking geolocation', [
                'error' => $e->getMessage(),
                'ip' => $ipAddress,
            ]);
            
            // En cas d'erreur, autoriser (fail open)
            return true;
        }
    }

    /**
     * Obtenir le pays depuis l'IP
     */
    private function getCountry(Request $request): string
    {
        try {
            $ipAddress = $request->ip();
            
            if (class_exists('\App\Services\IpGeolocationService')) {
                $geoService = new \App\Services\IpGeolocationService();
                $location = $geoService->getLocationFromIp($ipAddress);
                return $location['country'] ?? 'Unknown';
            }
            
            $response = @file_get_contents("http://ip-api.com/json/{$ipAddress}?fields=country");
            $data = $response ? json_decode($response, true) : [];
            return $data['country'] ?? 'Unknown';
            
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}

