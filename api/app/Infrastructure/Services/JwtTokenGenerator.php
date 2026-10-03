<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Ports\Outbound\ClockInterface;
use App\Application\Ports\Outbound\TokenGeneratorInterface;
use App\Domain\Entities\User;

final class JwtTokenGenerator implements TokenGeneratorInterface
{
    private string $signingKey;
    private int $ttlSeconds;
    private ClockInterface $clock;

    public function __construct(
        ClockInterface $clock,
        ?string $signingKey = null,
        int $ttlSeconds = 3600
    ) {
        $this->clock = $clock;
        $key = $signingKey ?? (string) env('JWT_SIGNING_KEY', '');
        if (trim($key) === '') {
            throw new \RuntimeException('JWT_SIGNING_KEY no está configurada.');
        }
        $this->signingKey = $key;
        $this->ttlSeconds = $ttlSeconds;
    }

    public function generateToken(User $user): array
    {
        $now = $this->clock->now();
        $nowTs = $now->getTimestamp();
        $expTs = $nowTs + $this->ttlSeconds;
        $expDate = $now->modify("+{$this->ttlSeconds} seconds");

        $jti = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'sub' => $user->id(),
            'unique_name' => $user->username(),
            'role' => $user->role(),
            'jti' => $jti,
            'exp' => $expTs,
            'iat' => $nowTs,
        ];

        $base64Header = $this->base64UrlEncode((string) json_encode($header));
        $base64Payload = $this->base64UrlEncode((string) json_encode($payload));

        $signature = hash_hmac('sha256', "{$base64Header}.{$base64Payload}", $this->signingKey, true);
        $base64Signature = $this->base64UrlEncode($signature);

        $token = "{$base64Header}.{$base64Payload}.{$base64Signature}";

        return [
            'accessToken' => $token,
            'expiresAt' => $expDate->format('Y-m-d\TH:i:s.u\+00:00'),
        ];
    }

    public function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $expectedSig = $this->base64UrlEncode(
            hash_hmac('sha256', "{$base64Header}.{$base64Payload}", $this->signingKey, true)
        );

        if (!hash_equals($expectedSig, $base64Signature)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($base64Payload);
        if ($payloadJson === null) {
            return null;
        }

        /** @var array{sub?: string, unique_name?: string, role?: string, jti?: string, exp?: int}|null $payload */
        $payload = json_decode($payloadJson, true);
        if (!is_array($payload) || !isset($payload['exp'], $payload['sub'], $payload['role'], $payload['unique_name'])) {
            return null;
        }

        // Check expiration with 30s clock leeway
        $nowTs = $this->clock->now()->getTimestamp();
        if ($payload['exp'] + 30 < $nowTs) {
            return null;
        }

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): ?string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        return $decoded === false ? null : $decoded;
    }
}
