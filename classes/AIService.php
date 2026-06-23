<?php
class AIService {
    private $apiKey = "AIzaSyAUBPLXnQy791UEdzKNCwLwhXxi1lrSYLc"; // 🔴 Double check your key is pasted here

    public function askStockSense($userQuery, $databaseContext) {
        // Pointing explicitly to the current stable production-ready alias node
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey;

        $systemInstructions = "You are StockSense, the intelligent neural co-pilot for StockPilot, a supermarket management system in Ethiopia. "
                            . "You have direct access to live store logs. Analyze the data provided below and answer the user's question. "
                            . "Be professional, concise, actionable, and straight to the point. Do not make up figures; only use the context provided.\n\n"
                            . "LIVE STORE DATA CONTEXT:\n" . json_encode($databaseContext) . "\n\n"
                            . "USER QUESTION: " . $userQuery;

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $systemInstructions]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return "Connection error: " . curl_error($ch);
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        // Extracting text from standard candidate properties
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        } elseif (isset($result['error']['message'])) {
            return "Gemini API Error: " . $result['error']['message'];
        } else {
            return "Unexpected Payload Format. Please re-check connection settings.";
        }
    }
}