<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceAlertController extends Controller
{
    private const UNAVAILABLE_MESSAGE = 'Price alerts are temporarily unavailable. Please try again later.';

    public function index()
    {
        $assets = [];
        $alerts = [];
        $unavailable = false;

        try {
            $assetsResponse = Http::timeout(5)->get($this->apiUrl('/assets'), ['limit' => 100]);
            $alertsResponse = Http::timeout(5)
                ->withHeaders(['x-api-key' => config('services.price_alerts.key')])
                ->get($this->apiUrl('/alerts'), ['email' => auth()->user()->email, 'limit' => 100]);

            if ($assetsResponse->successful() && $alertsResponse->successful()) {
                $assets = $assetsResponse->json('data', []);
                $alerts = $alertsResponse->json('data', []);
            } else {
                $unavailable = true;
            }
        } catch (ConnectionException $e) {
            Log::error('Failed to reach Price Alerts API: ' . $e->getMessage());
            $unavailable = true;
        }

        return view('price-alerts.index', compact('assets', 'alerts', 'unavailable'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|integer',
            'direction' => 'required|in:above,below',
            'target_price' => 'required|numeric|gt:0',
        ]);

        try {
            $response = Http::timeout(5)
                ->withHeaders(['x-api-key' => config('services.price_alerts.key')])
                ->post($this->apiUrl('/alerts'), [
                    'asset_id' => (int) $request->asset_id,
                    'email' => auth()->user()->email,
                    'direction' => $request->direction,
                    'target_price' => (float) $request->target_price,
                ]);
        } catch (ConnectionException $e) {
            Log::error('Failed to reach Price Alerts API: ' . $e->getMessage());
            return back()->with('error', self::UNAVAILABLE_MESSAGE);
        }

        if ($response->status() === 201) {
            return back()->with('success', 'Price alert created.');
        }

        $message = collect($response->json('errors', []))->pluck('message')->implode(' ');

        return back()->with('error', $message ?: ($response->json('error') ?? 'Could not create the alert.'));
    }

    public function destroy(int $id)
    {
        try {
            $existing = Http::timeout(5)
                ->withHeaders(['x-api-key' => config('services.price_alerts.key')])
                ->get($this->apiUrl("/alerts/{$id}"));
        } catch (ConnectionException $e) {
            Log::error('Failed to reach Price Alerts API: ' . $e->getMessage());
            return back()->with('error', self::UNAVAILABLE_MESSAGE);
        }

        if (!$existing->successful()) {
            return back()->with('error', 'Alert not found.');
        }

        // The Node API only checks the API key, not which user owns the alert -
        // that check has to happen here, before proxying the delete.
        if ($existing->json('data.email') !== auth()->user()->email) {
            abort(403);
        }

        try {
            Http::timeout(5)
                ->withHeaders(['x-api-key' => config('services.price_alerts.key')])
                ->delete($this->apiUrl("/alerts/{$id}"));
        } catch (ConnectionException $e) {
            Log::error('Failed to reach Price Alerts API: ' . $e->getMessage());
            return back()->with('error', self::UNAVAILABLE_MESSAGE);
        }

        return back()->with('success', 'Price alert deleted.');
    }

    private function apiUrl(string $path): string
    {
        return rtrim(config('services.price_alerts.url'), '/') . $path;
    }
}
