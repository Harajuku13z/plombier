<?php

use App\Models\Setting;

if (!function_exists('full_address')) {
    /**
     * Retourne l'adresse complète de l'entreprise
     * 
     * @param string $separator Séparateur entre les lignes (par défaut : ', ')
     * @param bool $includeCountry Inclure le pays (par défaut : true)
     * @return string
     */
    function full_address(string $separator = ', ', bool $includeCountry = true): string
    {
        return Setting::getFullAddress($separator, $includeCountry);
    }
}

if (!function_exists('full_address_html')) {
    /**
     * Retourne l'adresse complète HTML formatée
     * 
     * @return string
     */
    function full_address_html(): string
    {
        return Setting::getFullAddressHtml();
    }
}

if (!function_exists('address_for_maps')) {
    /**
     * Retourne l'adresse pour Google Maps / liens
     * 
     * @return string
     */
    function address_for_maps(): string
    {
        return Setting::getAddressForMaps();
    }
}

if (!function_exists('company_address_line')) {
    /**
     * Retourne l'adresse sur une seule ligne
     * 
     * @return string
     */
    function company_address_line(): string
    {
        return full_address(', ', false);
    }
}

