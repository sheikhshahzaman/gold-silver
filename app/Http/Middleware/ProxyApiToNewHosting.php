<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProxyApiToNewHosting
{
    public function handle(Request $request, Closure $next): Response
    {
        $baseUrl = rtrim((string) config('services.api_proxy.base_url', ''), '/');

        if ($baseUrl === '' || $this->shouldSkipProxy($request)) {
            return $next($request);
        }

        try {
            $response = $this->forward($request, $baseUrl);
        } catch (ConnectionException $e) {
            Log::warning('API proxy failed, falling back to local handler', [
                'path' => $request->path(),
                'error' => $e->getMessage(),
            ]);

            return $next($request);
        }

        return response($response->body(), $response->status())
            ->withHeaders($this->responseHeaders($response->headers()))
            ->header('X-IBE-Api-Proxy', 'new-hosting');
    }

    private function shouldSkipProxy(Request $request): bool
    {
        $path = trim($request->path(), '/');

        if (str_starts_with($path, 'api/internal/')) {
            return true;
        }

        return $request->headers->has('X-IBE-No-Proxy');
    }

    private function forward(Request $request, string $baseUrl): \Illuminate\Http\Client\Response
    {
        $path = preg_replace('#^api/?#', '', trim($request->path(), '/'));
        $url = $baseUrl . '/' . ltrim((string) $path, '/');
        $query = $request->getQueryString();

        if ($query) {
            $url .= '?' . $query;
        }

        $client = Http::timeout((int) config('services.api_proxy.timeout', 20))
            ->withHeaders($this->requestHeaders($request));

        if (! filter_var(config('services.api_proxy.verify_ssl', true), FILTER_VALIDATE_BOOLEAN)) {
            $client = $client->withoutVerifying();
        }

        if ($request->files->count() > 0) {
            return $client->send($request->method(), $url, [
                'multipart' => $this->multipartPayload($request),
            ]);
        }

        $content = $request->getContent();
        if ($content !== '') {
            return $client
                ->withBody($content, $request->headers->get('Content-Type', 'application/json'))
                ->send($request->method(), $url);
        }

        return $client->send($request->method(), $url);
    }

    private function requestHeaders(Request $request): array
    {
        $headers = [
            'Accept' => $request->headers->get('Accept', 'application/json'),
            'X-Forwarded-Host' => config('services.api_proxy.forwarded_host', $request->getHost()),
            'X-Forwarded-Proto' => $request->getScheme(),
            'X-Forwarded-For' => $request->ip(),
        ];

        foreach (['Authorization', 'User-Agent', 'Accept-Language'] as $header) {
            if ($request->headers->has($header)) {
                $headers[$header] = $request->headers->get($header);
            }
        }

        return $headers;
    }

    private function multipartPayload(Request $request): array
    {
        $payload = [];

        foreach ($request->except(array_keys($request->allFiles())) as $name => $value) {
            $payload[] = [
                'name' => $name,
                'contents' => is_scalar($value) ? (string) $value : json_encode($value),
            ];
        }

        foreach ($request->allFiles() as $name => $file) {
            foreach ($this->flattenFiles($file) as $uploadedFile) {
                $payload[] = [
                    'name' => $name,
                    'contents' => fopen($uploadedFile->getRealPath(), 'r'),
                    'filename' => $uploadedFile->getClientOriginalName(),
                    'headers' => [
                        'Content-Type' => $uploadedFile->getClientMimeType(),
                    ],
                ];
            }
        }

        return $payload;
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function flattenFiles(mixed $file): array
    {
        if ($file instanceof UploadedFile) {
            return [$file];
        }

        if (! is_array($file)) {
            return [];
        }

        return collect($file)
            ->flatMap(fn (mixed $nested) => $this->flattenFiles($nested))
            ->values()
            ->all();
    }

    private function responseHeaders(array $headers): array
    {
        return collect($headers)
            ->except(['transfer-encoding', 'content-encoding', 'connection'])
            ->map(fn (array $value): string => implode(', ', $value))
            ->all();
    }
}
