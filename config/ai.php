<?php
// AI 集成配置
return [
    'openai' => [
        'key' => env('OPENAI_API_KEY', ''),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
    ],
    'claude' => [
        'key' => env('CLAUDE_API_KEY', ''),
        'base_url' => env('CLAUDE_BASE_URL', 'https://api.anthropic.com'),
        // 可选：模型标识
        'model' => env('CLAUDE_MODEL', 'claude-v1'),
    ],
    'azure_openai' => [
        'endpoint' => env('AZURE_OPENAI_ENDPOINT', ''),
        'key' => env('AZURE_OPENAI_KEY', ''),
        'api_version' => env('AZURE_OPENAI_API_VERSION', '2023-12-01-preview'),
        'deployment' => env('AZURE_OPENAI_DEPLOYMENT', ''),
    ],
];
