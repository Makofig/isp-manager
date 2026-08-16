<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected bool $initialized = false;

    protected function init(): void
    {
        if ($this->initialized) {
            return;
        }

        $token = config('services.mercadopago.token');

        if (!$token) {
            throw new \Exception('MercadoPago token no configurado');
        }

        MercadoPagoConfig::setAccessToken($token);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        $this->initialized = true;
    }

    public function crearLinkPago(array $data): array
    {
        $this->init();

        try {
            $client = new PreferenceClient();

            $preference = $client->create([
                "auto_return" => "approved",
                "back_urls" => [
                    "success" => route('mp.success'),
                    "failure" => route('mp.failure'),
                    "pending" => route('mp.pending')
                ],
                "statement_descriptor" => config('app.name', 'InterSys'),
                "binary_mode" => true,
                "external_reference" => $data['external_reference'] ?? null,
                "items" => [
                    [
                        "id" => $data['item_id'] ?? '001',
                        "title" => $data['title'] ?? 'Internet Service',
                        "quantity" => 1,
                        "unit_price" => (float) ($data['price'] ?? 0),
                        "description" => $data['description'] ?? 'Internet Service Payment',
                        "category_id" => "services",
                    ]
                ],
                "payer" => [
                    "email" => $data['payer_email'] ?? 'test@test.com',
                ],
                "notification_url" => route('mp.webhook'),
            ]);

            return [
                "id" => $preference->id,
                "init_point" => $preference->init_point,
                "sandbox_init_point" => $preference->sandbox_init_point,
            ];

        } catch (MPApiException $e) {
            Log::error('MercadoPago API error', [
                'status' => $e->getApiResponse()->getStatusCode(),
                'content' => $e->getApiResponse()->getContent(),
            ]);
            throw new \Exception('Error MercadoPago API: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('MercadoPago error', ['message' => $e->getMessage()]);
            throw new \Exception('Error al crear link de pago: ' . $e->getMessage());
        }
    }

    public function showToken(): string
    {
        $this->init();

        return MercadoPagoConfig::getAccessToken();
    }
}
