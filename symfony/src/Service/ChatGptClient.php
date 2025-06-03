<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatGptClient
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function generateStructuredSyllabus(string $title, int $duration, string $level, int $nbSessions = 5): array
    {
        return [
            'description_and_objectives' => $this->generateDescriptionAndObjectives($title, $duration, $level),
            'sessions' => $this->generateSessionsPlan($title, $duration, $nbSessions),
            'activities_and_resources' => $this->generateActivitiesAndResources($title),
        ];
    }

    private function generateDescriptionAndObjectives(string $title, int $duration, string $level): string
    {
        $prompt = sprintf(
            "Tu es un assistant pédagogique. Rédige une <strong>description</strong> HTML (balise <p>) pour un module intitulé « %s », de niveau %s, d’une durée totale de %d heures. " .
            "Ensuite, fournis une liste HTML (<ul class='list-disc pl-5'> et <li class='ml-4'>) des objectifs pédagogiques. Ne génère aucun texte en dehors des balises HTML.",
            $title,
            $level,
            $duration
        );

        return $this->callChatGpt($prompt);
    }

    private function generateSessionsPlan(string $title, int $duration, int $nbSessions): string
    {
        $prompt = sprintf(
            "Rédige le plan HTML du module « %s » (durée totale : %d heures, réparties sur %d séances).\n\n" .
            "Pour chaque séance, affiche :\n" .
            "- Un titre avec <h3 class='text-xl font-bold title-primary my-4'> contenant le numéro de séance, le titre et la durée en heure\n" .
            "- Une description avec <p>\n" .
            "- Une liste d'activités avec <ul class='list-disc pl-5'> et <li class='ml-4'>\n\n" .
            "Ne produis que du HTML, sans texte explicatif.",
            $title,
            $duration,
            $nbSessions
        );

        return $this->callChatGpt($prompt);
    }

    private function generateActivitiesAndResources(string $title): string
    {
        $prompt = sprintf(
            "Tu es un assistant pédagogique.\n\n" .
            "Pour le module intitulé « %s », génère deux blocs HTML riches et spécifiques :\n\n" .
            "1. Un titre <h3 class='text-xl font-bold title-primary my-4'>Activités pédagogiques</h3>, suivi d'une liste <ul class='list-disc pl-5'> contenant des <li class='ml-4'> décrivant des activités pédagogiques concrètes réellement adaptées à ce module (par exemple : travaux pratiques sur un sujet précis, exercices encadrés, projet en groupe, etc).\n\n" .
            "2. Un titre <h3 class='text-xl font-bold title-primary my-4'>Ressources nécessaires</h3>, suivi d'une liste <ul class='list-disc pl-5'> contenant des <li class='ml-4'> listant les outils, supports, matériels ou plateformes réellement utiles pour ce module.\n\n" .
            "Tu dois écrire uniquement du HTML, sans utiliser ```html ni aucun commentaire. Le contenu doit être adapté au module « %s ».",
            $title,
            $title
        );

        return $this->callChatGpt($prompt);
    }


    private function callChatGpt(string $prompt): string
    {
        $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'timeout' => 240,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4',
                'temperature' => 0.7,
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant pédagogique.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ],
        ]);

        $data = $response->toArray();
        return trim($this->cleanMarkdown($data['choices'][0]['message']['content'] ?? ''));
    }

    private function cleanMarkdown(string $content): string
    {
        // Supprime les ```html ou ``` simples qui encadrent le contenu
        return preg_replace('/^```(?:html)?\s*([\s\S]*?)\s*```$/', '$1', trim($content));
    }

}
