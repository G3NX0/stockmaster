<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function index()
    {
        // Ambil barang beserta relasi category dan unit yang memiliki stok > 0
        $items = Item::with(['category', 'unit'])
            ->get();

        // Ambil semua kategori yang memiliki barang terdaftar
        $categories = \App\Models\Category::whereHas('items')->get();

        return view('store', compact('items', 'categories'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:items,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $grandTotal = 0;
            $itemsSummary = [];

            DB::transaction(function () use ($request, &$grandTotal, &$itemsSummary) {
                foreach ($request->cart as $cartItem) {
                    $item = Item::lockForUpdate()->find($cartItem['id']);

                    if ($item->stok_barang < $cartItem['quantity']) {
                        throw new \Exception("Stok tidak mencukupi untuk barang: " . $item->nama_barang);
                    }

                    // 1. Kurangi stok barang
                    $item->stok_barang -= $cartItem['quantity'];
                    $item->save();

                    // 2. Gunakan harga efektif (promosi/diskon jika ada)
                    $price = $item->getEffectivePrice();
                    $subtotal = $price * $cartItem['quantity'];
                    $grandTotal += $subtotal;

                    // 3. Catat transaksi keluar (out)
                    Transaction::create([
                        'item_id'  => $item->id,
                        'user_id'  => Auth::check() ? Auth::id() : null,
                        'type'     => 'out',
                        'quantity' => $cartItem['quantity'],
                        'note'     => "Pembelian E-Commerce oleh {$request->customer_name} ({$request->customer_phone}). Alamat: {$request->customer_address}",
                    ]);

                    $itemsSummary[] = "📦 *{$item->nama_barang}*\n   Kuantitas: {$cartItem['quantity']}x\n   Harga: Rp " . number_format($price, 0, ',', '.') . "\n   Subtotal: Rp " . number_format($subtotal, 0, ',', '.');
                }
            });

            // 4. Format data item untuk payload webhook Bot WA baru
            $orderItems = [];
            foreach ($request->cart as $cartItem) {
                $item = Item::find($cartItem['id']);
                if ($item) {
                    $price = $item->getEffectivePrice();
                    $orderItems[] = [
                        'name'     => $item->nama_barang,
                        'quantity' => $cartItem['quantity'],
                        'price'    => $price,
                        'subtotal' => $price * $cartItem['quantity'],
                    ];
                }
            }

            // 5. Kirim HTTP POST ke Bot WhatsApp BARU (port 5000, Non-blocking)
            try {
                $botUrl = env('WHATSAPP_BOT_URL', 'http://localhost:5000');
                Http::timeout(3)->post($botUrl . '/notify-order', [
                    'customer_name'    => $request->customer_name,
                    'customer_phone'   => $request->customer_phone,
                    'customer_address' => $request->customer_address,
                    'items'            => $orderItems,
                    'total'            => $grandTotal,
                ]);
            } catch (\Exception $e) {
                Log::warning("WhatsApp notification skipped (Bot offline/unreachable): " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'total' => $grandTotal
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'user' => Auth::user(),
                'csrf_token' => csrf_token()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah.'
        ], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil!',
            'user' => $user,
            'csrf_token' => csrf_token()
        ]);
    }

    // ── Save customer profile to DB ──────────────────────────────────────────
    public function saveProfile(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'phone'   => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return response()->json(['success' => true, 'message' => 'Profil berhasil disimpan.']);
    }

    // ── Get customer order history ────────────────────────────────────────────
    public function getOrders()
    {
        if (!Auth::check()) {
            return response()->json(['orders' => []]);
        }

        // Group transactions by user into pseudo-orders by note prefix timestamp
        $transactions = Transaction::with('item')
            ->where('user_id', Auth::id())
            ->where('type', 'out')
            ->latest('id')
            ->get();

        // Build order groups: each transaction becomes a line-item on its own order
        // because currently one checkout = one Transaction per cart item.
        // We group by minute-precision timestamp to merge items in same checkout.
        $groups = $transactions->groupBy(function ($tx) {
            return $tx->created_at->format('Y-m-d H:i');
        });

        $orders = $groups->map(function ($txGroup, $timestamp) {
            $first = $txGroup->first();
            $total = $txGroup->sum(fn($tx) => $tx->quantity * ($tx->item ? $tx->item->getEffectivePrice() : 0));
            return [
                'id'      => 'ORD-' . $first->id,
                'date'    => $first->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm'),
                'total'   => $total,
                'payment' => 'transfer_bank',
                'status'  => 'Terkonfirmasi',
                'items'   => $txGroup->map(fn($tx) => [
                    'name'  => $tx->item ? $tx->item->nama_barang : 'Produk',
                    'qty'   => $tx->quantity,
                    'price' => $tx->item ? $tx->item->getEffectivePrice() : 0,
                ])->values(),
            ];
        })->values();

        return response()->json(['orders' => $orders]);
    }
}
