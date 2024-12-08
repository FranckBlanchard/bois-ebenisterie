#!/usr/bin/env php
<?php

/**
 * Script PHP pour extraire les adresses de site web d'un fichier HTML et afficher leur code HTTP avec des couleurs.
 *
 * Ce script lit le contenu d'un fichier HTML spécifié, extrait toutes les adresses de site web (HTTP et HTTPS),
 * teste le code HTTP de chaque adresse et les affiche à l'écran avec des couleurs.
 *
 * @package    ExtractionAdressesWeb
 * @version    1.4
 * @author     Votre Nom
 * @license    MIT License
 */
// Nom du fichier HTML à lire
$filename = __DIR__ . '/../build/html/canada/canada-lfdb.html';

/**
 * Fonction pour tester le code HTTP d'une URL.
 *
 * @param string $url L'URL à tester.
 * @return string Le code HTTP de l'URL.
 */
function getHttpResponseCode($url) {
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.64',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
            ]
        ]
    ];
    $context = stream_context_create($options);
    $headers = get_headers($url, 1, $context);
    return substr($headers[0], 9, 3);
}

/**
 * Fonction pour colorer le code HTTP.
 *
 * @param string $code Le code HTTP.
 * @return string Le code HTTP coloré.
 */
function colorHttpCode($code) {
    switch ($code) {
        case '200':
            return "\033[32m$code\033[0m"; // Vert
        case '301':
            return "\033[33m$code\033[0m"; // Orange
        case '400':
        case '403':
        case '404':
            return "\033[31m$code\033[0m"; // Rouge
        default:
            return $code; // Pas de couleur pour les autres codes
    }
}

// Vérifie si le fichier existe
if (file_exists($filename)) {
    // Lit le contenu du fichier
    $content = file_get_contents($filename);

    // Utilise une expression régulière pour extraire les adresses de site web
    preg_match_all('/https?:\/\/[^\s"<]+/', $content, $matches);

    // Tableau pour stocker les adresses uniques
    $unique_urls = array_unique($matches[0]);

    // Affiche les adresses de site web et leur code HTTP coloré
    foreach ($unique_urls as $url) {
        $http_code = getHttpResponseCode($url);
        $colored_code = colorHttpCode($http_code);
        echo "URL: $url - HTTP Code: $colored_code" . PHP_EOL;
    }
} else {
    // Affiche un message d'erreur si le fichier n'existe pas
    echo "Le fichier n'existe pas." . PHP_EOL;
}
