<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        // .env に STRIPE_WEBHOOK_SECRET を入れる（Stripe CLI またはDashboardで取得）
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // ① カード: Checkout完了
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $orderId = $session->metadata->order_id ?? null;

            if ($orderId) {
                $this->markPaidAndSold($orderId);
            }
        }

        // ② コンビニ: 実支払い完了（PaymentIntent成功）
        if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object;

            $orderId = $pi->metadata->order_id ?? null;

            if ($orderId) {
                $this->markPaidAndSold($orderId);
            }
        }

        return response('ok', 200);
    }

    private function markPaidAndSold(string $orderId): void
    {
        $order = Order::find($orderId);
        if (!$order) return;

        // すでに paid 済みなら何もしない（冪等性）
        if ($order->status === 'paid') return;

        // 注文を paid に
        $order->update(['status' => 'paid']);

        // items を SOLD に（status=1）
        Item::where('id', $order->item_id)
            ->where('status', '!=', 1)
            ->update(['status' => 1]);
    }
}