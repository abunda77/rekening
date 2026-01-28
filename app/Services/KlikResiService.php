<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KlikResiService
{
    protected string $baseUrl = 'https://api.binderbyte.com/v1';

    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.klikresi.key', env('KLIKRESI_API_KEY', 'xx-xx-xx-xx-xx'));
    }

    /**
     * Get list of supported couriers.
     */
    public function getCouriers(): array
    {
        // Using the static list provided by the user to avoid unnecessary API calls
        // and ensure we have the list even if the API is down or quota exceeded.
        return [
            ['code' => 'anteraja', 'name' => 'Anteraja'],
            ['code' => 'ide', 'name' => 'ID Express'],
            ['code' => 'jne', 'name' => 'Jalur Nugraha Ekakurir'],
            ['code' => 'jnt', 'name' => 'J&T Express'],
            ['code' => 'lex', 'name' => 'Lazada Logistics'],
            ['code' => 'lion', 'name' => 'Lion Parcel'],
            ['code' => 'ninja', 'name' => 'Ninja Express'],
            ['code' => 'oexpress', 'name' => 'OExpress'],
            ['code' => 'pos', 'name' => 'POS Indonesia'],
            ['code' => 'sap', 'name' => 'SAP Express'],
            ['code' => 'sicepat', 'name' => 'Sicepat Express'],
            ['code' => 'spx', 'name' => 'Shopee Express'],
            ['code' => 'wahana', 'name' => 'Wahana Prestasi Logistik'],
        ];
    }

    /**
     * Track a shipment.
     */
    public function track(string $receiptNumber, string $courierCode): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/track", [
                'api_key' => $this->apiKey,
                'courier' => $courierCode,
                'awb' => $receiptNumber,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['status']) && $body['status'] == 200) {
                $data = $body['data'];
                $summary = $data['summary'];
                $histories = $data['history'];

                return [
                    'status' => ['code' => 200, 'message' => 'OK'],
                    'data' => [
                        'tracking_number' => $summary['awb'],
                        'courier_code' => $summary['courier'],
                        'current_status' => $summary['status'],
                        // Parse date string to timestamp for view compatibility
                        'last_updated' => \Carbon\Carbon::parse($summary['date'])->timestamp,
                        'histories' => collect($histories)->map(function ($item) {
                            return [
                                'status' => '', // BinderByte does not provide distinct status codes for history items
                                'description' => $item['desc'],
                                'location' => $item['location'] ?? '',
                                'date' => \Carbon\Carbon::parse($item['date'])->timestamp,
                            ];
                        })->toArray(),
                    ],
                ];
            }

            return [
                'status' => [
                    'code' => $body['status'] ?? $response->status(),
                    'message' => $body['message'] ?? $response->reason(),
                ],
                'data' => null,
            ];
        } catch (\Exception $e) {
            return [
                'status' => [
                    'code' => 500,
                    'message' => $e->getMessage(),
                ],
                'data' => null,
            ];
        }
    }
}
