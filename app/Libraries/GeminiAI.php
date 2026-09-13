<?php

namespace App\Libraries;

use Config\Services;

class GeminiAI
{
    private string $apiKey;
    private string $model = 'gemini-3.6-flash';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
    }

    public function analyzeCV(string $cvText): array
    {
        $prompt = $this->buildPrompt($cvText);

        $body = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 4096,
            ],
        ]);

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $client = Services::curlrequest();
        $startTime = microtime(true);

        try {
            $response = $client->post($url, [
                'body' => $body,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 60,
            ]);

            $elapsedMs = (int) ((microtime(true) - $startTime) * 1000);
            $data = json_decode($response->getBody(), true);

            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $usage = $data['usageMetadata']['totalTokenCount'] ?? 0;

            $analysis = $this->parseResponse($text);
            $cleaned = json_encode($analysis, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return [
                'respuesta' => $cleaned !== '[]' ? $cleaned : $text,
                'score_match' => $analysis['score'] ?? null,
                'fortalezas' => $analysis['fortalezas'] ?? null,
                'debilidades' => $analysis['debilidades'] ?? null,
                'recomendacion' => $analysis['recomendacion'] ?? null,
                'tokens_usados' => $usage,
                'tiempo_respuesta_ms' => $elapsedMs,
                'estado' => 'completado',
                'error' => null,
            ];
        } catch (\Throwable $e) {
            $elapsedMs = (int) ((microtime(true) - $startTime) * 1000);

            return [
                'respuesta' => '',
                'score_match' => null,
                'fortalezas' => null,
                'debilidades' => null,
                'recomendacion' => null,
                'tokens_usados' => null,
                'tiempo_respuesta_ms' => $elapsedMs,
                'estado' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function buildPrompt(string $cvText): string
    {
        return "Eres un reclutador experto. Analiza el siguiente CV y devuelve EXCLUSIVAMENTE un JSON valido con esta estructura:\n\n"
            . "{\n"
            . "  \"score\": <numero 0-100>,\n"
            . "  \"fortalezas\": \"<texto separado por comas>\",\n"
            . "  \"debilidades\": \"<texto separado por comas>\",\n"
            . "  \"recomendacion\": \"<texto con consejos de mejora>\",\n"
            . "  \"habilidades_detectadas\": [\"<habilidad1>\", \"<habilidad2>\", ...],\n"
            . "  \"experiencia_anios\": <numero>,\n"
            . "  \"nivel_educacion\": \"<nivel detectado>\",\n"
            . "  \"datos_personales\": {\n"
            . "    \"nombre\": \"<nombre completo>\",\n"
            . "    \"email\": \"<email>\",\n"
            . "    \"telefono\": \"<telefono>\",\n"
            . "    \"ubicacion\": \"<ciudad, pais>\",\n"
            . "    \"linkedin\": \"<url linkedin o vacio>\",\n"
            . "    \"resumen\": \"<resumen profesional de 2-3 lineas>\"\n"
            . "  },\n"
            . "  \"experiencias\": [\n"
            . "    {\n"
            . "      \"empresa\": \"<nombre empresa>\",\n"
            . "      \"cargo\": \"<cargo>\",\n"
            . "      \"fecha_inicio\": \"<YYYY-MM-DD o vacio>\",\n"
            . "      \"fecha_fin\": \"<YYYY-MM-DD o vacio si es actual>\",\n"
            . "      \"actual\": <true o false>,\n"
            . "      \"descripcion\": \"<descripcion breve>\"\n"
            . "    }\n"
            . "  ],\n"
            . "  \"educaciones\": [\n"
            . "    {\n"
            . "      \"institucion\": \"<nombre institucion>\",\n"
            . "      \"titulo\": \"<titulo obtenido>\",\n"
            . "      \"nivel\": \"<uno de: primaria, secundaria, tecnico, universitario, posgrado, doctorado>\",\n"
            . "      \"fecha_inicio\": \"<YYYY-MM-DD o vacio>\",\n"
            . "      \"fecha_fin\": \"<YYYY-MM-DD o vacio si en curso>\",\n"
            . "      \"en_curso\": <true o false>\n"
            . "    }\n"
            . "  ],\n"
            . "  \"idiomas\": [\n"
            . "    {\n"
            . "      \"nombre\": \"<idioma>\",\n"
            . "      \"nivel\": \"basico|intermedio|avanzado|nativo\"\n"
            . "    }\n"
            . "  ]\n"
            . "}\n\n"
            . "No incluyas markdown ni texto fuera del JSON.\n"
            . "Si un campo no se encuentra en el CV, usa string vacio o array vacio.\n"
            . "Para fechas usa formato YYYY-MM-DD. Si solo tienes el ano, usa YYYY-01-01.\n\n"
            . "CV a analizar:\n\n" . $cvText;
    }

    private function parseResponse(string $text): array
    {
        $text = trim($text);
        if (str_starts_with($text, '```')) {
            $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);
        }

        $data = json_decode($text, true);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    public function hasApiKey(): bool
    {
        return !empty($this->apiKey);
    }
}
