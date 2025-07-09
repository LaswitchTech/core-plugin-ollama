<?php

// Import additionnal class into the global namespace
use \LaswitchTech\Core\Abstracts\Helper;

class OllamaHelper extends Helper {

    // Global Properties
    protected $Log;

    // Properties
    private $Host;
    private $Port;
    private $Model;
    private $Enpoint;
    private $Stream;
    private $KeepAlive;

    // Promtps
    private $System;
    private $User;
    private $Prompts = [
        "ReadEmail" => '\nYou are an information-extraction engine.\nReturn ONLY valid JSON that matches this schema:\n\n{\n  "references": [                // strings, KEEP original formatting\n    "Ref# 2024-QT-8871",\n    "PO-AB-7721"\n  ],\n  "dates"     : ["2025-03-12"],  // ISO-8601 if you can parse any\n  "emails"    : ["buyer@example.com"],\n  "totals"    : ["USD 2 450.00"] // leave currency symbol as seen\n}\n\n- If nothing is found, use empty arrays.\n- Do not wrap the JSON in Markdown fences or extra text.\n- Do not invent values.',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Call the parent constructor
        parent::__construct();

        // Import Global Variables
        global $LOG;

        // Initialize properties
        $this->Log = $LOG;

        // Configure the logger
        $this->Log->add('ollama');

        // Configure Ollama
        $this->Host = $this->Config->get('ollama', 'host') ?? '127.0.0.1';
        $this->Port = $this->Config->get('ollama', 'port') ?? 11434;
        $this->Model = $this->Config->get('ollama', 'model') ?? 'llama3';
        $this->Enpoint = $this->Config->get('ollama', 'endpoint') ?? '/v1/chat/completions'; // or /api/chat
        $this->Stream = $this->Config->get('ollama', 'stream') ?? false;
        $this->KeepAlive = $this->Config->get('ollama', 'keep_alive') ?? -1; //  -1  = keep forever or 0 = unload immediately after replying. "10m", 300, "24h" → unload after that idle period.

        // Set default system prompt
        $this->System = $this->Prompts[array_key_first($this->Prompts)];
    }

    public function setModel(string $model): void
    {
        $this->Model = $model;
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->Enpoint = $endpoint;
    }

    public function setStream(bool $stream): void
    {
        $this->Stream = $stream;
    }

    public function setKeepAlive(int $keepAlive): void
    {
        $this->KeepAlive = $keepAlive;
    }

    public function system(string $prompt): void
    {
        $this->System = $this->Prompts[$prompt] ?? $this->Prompts[array_key_first($this->Prompts)];
    }

    public function prompt(string $prompt): void
    {
        $this->User = $prompt;
    }

    private function payload(): array
    {
        // Initialize the payload
        $payload = [
            'model'    => $this->Model,
            'messages' => [
                ['role' => 'system', 'content' => $this->System],
                ['role' => 'user', 'content' => $this->User],
            ],
            'keep_alive' => $this->KeepAlive,
            'stream'   => $this->Stream
        ];

        return $payload;
    }

    public function request(): string
    {
        // Initialize the cURL session
        $ch = curl_init('http://'.$this->Host.':'.$this->Port.$this->Enpoint);

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($this->payload()),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if ($response === false) {
            throw new RuntimeException('Curl error: '.curl_error($ch));
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $data = json_decode($response, true);

        return $data['choices'][0]['message']['content'] ?? "";
    }
}
