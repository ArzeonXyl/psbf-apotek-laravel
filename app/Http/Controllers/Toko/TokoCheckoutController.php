<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Obat;

class TokoCheckoutController extends Controller
{
    /**
     * Menampilkan form checkout.
     */
    public function showCheckoutForm()
    {
        $cart = Session::get('cart', collect());

        if ($cart->isEmpty()) {
            return redirect()->route('toko.obat')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $totalPrice = $cart->sum(function ($item) {
            return $item['HARGA'] * $item['quantity'];
        });

        return view('toko.checkout', compact('cart', 'totalPrice'));
    }

    /**
     * Memproses checkout.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            // 'customer_phone' => 'required|string|max:20', // <-- HAPUS VALIDASI INI
        ]);

        $cart = Session::get('cart', collect());

        if ($cart->isEmpty()) {
            return redirect()->route('toko.obat')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $totalAmount = $cart->sum(function ($item) {
            return $item['HARGA'] * $item['quantity'];
        });

        DB::beginTransaction();
        try {
            foreach ($cart as $item) {
                $obat = Obat::find($item['ID_OBAT']);
                if (!$obat || $obat->JUMLAH_STOCK < $item['quantity']) {
                    DB::rollBack();
                    return redirect()->route('toko.cart')->with('error', 'Stok ' . $item['NAMA_OBAT'] . ' tidak mencukupi atau tidak ditemukan.');
                }
            }

            $order = Order::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'customer_name' => $request->customer_name,
                // 'customer_phone' => null, // <-- HAPUS BARIS INI atau set null jika kolomnya ada di DB tapi tidak diisi
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => null,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ID_OBAT' => $item['ID_OBAT'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['HARGA'],
                    'sub_total' => $item['HARGA'] * $item['quantity'],
                ]);

                $obat = Obat::find($item['ID_OBAT']);
                $obat->decrement('JUMLAH_STOCK', $item['quantity']);
            }

            $qrCodeData = json_encode([
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
            ]);
            $qrCodeFileName = 'qrcodes/order_' . $order->id . '.svg';
            Storage::disk('public')->put($qrCodeFileName, QrCode::format('svg')->size(200)->generate($qrCodeData));

            $order->qr_code_path = $qrCodeFileName;
            $order->save();

            DB::commit();

            Session::forget('cart');

            return redirect()->route('toko.order.success', ['orderId' => $order->id])
                             ->with('success', 'Pesanan Anda berhasil dibuat! Mohon tunjukkan QR Code untuk pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }

    public function orderSuccess($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('toko.obat')->with('error', 'Order tidak ditemukan.');
        }

        return view('toko.order-success', compact('order'));
    }
}