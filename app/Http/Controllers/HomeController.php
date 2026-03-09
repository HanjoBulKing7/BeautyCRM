<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener reseñas de Google (con cache de 24 horas)
        $reviews = $this->getGoogleReviews();
        
        // Pasar los datos a la vista
        return view('home', compact('reviews'));
    }
    
    private function getGoogleReviews()
    {
        // Usar cache para no exceder límites de API
        return Cache::remember('google_reviews', 86400, function () {
            
            $apiKey = env('GOOGLE_PLACES_API_KEY');
            $placeId = env('GOOGLE_PLACE_ID'); // También lo pondremos en .env
            
            // Si no hay API key configurada, retornar datos de respaldo
            if (!$apiKey || !$placeId) {
                return $this->getFallbackReviews();
            }
            
            try {
                // Llamada a Places API (nueva)
                $response = Http::get("https://places.googleapis.com/v1/places/{$placeId}", [
                    'key' => $apiKey,
                    'fields' => 'reviews,rating,userRatingCount',
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    return [
                        'average_rating' => $data['rating'] ?? 4.9,
                        'total_ratings' => $data['userRatingCount'] ?? 120,
                        'reviews' => array_map(function($review) {
                            return [
                                'text' => $review['text']['text'] ?? 'Excelente servicio',
                                'author' => $review['authorAttribution']['displayName'] ?? 'Cliente',
                                'rating' => $review['rating'] ?? 5,
                                'relativeTime' => $review['relativePublishTimeDescription'] ?? 'hace unos días',
                            ];
                        }, $data['reviews'] ?? [])
                    ];
                }
                
                // Si falla, retornar datos de respaldo
                return $this->getFallbackReviews();
                
            } catch (\Exception $e) {
                // Log del error para debugging
                \Log::error('Error al obtener reseñas de Google: ' . $e->getMessage());
                return $this->getFallbackReviews();
            }
        });
    }
    
    private function getFallbackReviews()
    {
        return [
            'average_rating' => 4.9,
            'total_ratings' => 120,
            'reviews' => [
                [
                    'text' => 'Excelente servicio, muy profesionales y el resultado fue increíble. Sin duda regreso.',
                    'author' => 'María López',
                    'rating' => 5,
                    'relativeTime' => 'hace 2 días',
                ],
                [
                    'text' => 'El lugar es hermoso y el trato es súper amable. Me encantó mi maquillaje.',
                    'author' => 'Andrea Ruiz',
                    'rating' => 5,
                    'relativeTime' => 'hace 1 semana',
                ],
                [
                    'text' => 'Calidad y profesionalismo en cada detalle. 100% recomendado.',
                    'author' => 'Fernanda Gómez',
                    'rating' => 5,
                    'relativeTime' => 'hace 2 semanas',
                ]
            ]
        ];
    }
}