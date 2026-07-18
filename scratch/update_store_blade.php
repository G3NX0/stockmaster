<?php

$file = 'resources/views/store.blade.php';
if (!file_exists($file)) {
    die("File not found: $file\n");
}

$content = file_get_contents($file);

// 1. Add toasts state and helper before init()
$toastsCode = "    toasts: [],\n    showToast(message, type = 'success') {\n        const id = Date.now() + Math.random();\n        this.toasts.push({ id, message, type });\n        setTimeout(() => {\n            this.toasts = this.toasts.filter(t => t.id !== id);\n        }, 4000);\n    },\n\n    init() {";
$content = str_replace('    init() {', $toastsCode, $content);

// 2. Add null checks in getEffectivePrice(item)
$oldPriceCode = "    getEffectivePrice(item) {\n        let now = new Date();";
$newPriceCode = "    getEffectivePrice(item) {\n        if (!item) return 0;\n        let now = new Date();";
$content = str_replace($oldPriceCode, $newPriceCode, $content);

// 3. Add null checks in isPromoActive(item)
$oldPromoCode = "    isPromoActive(item) {\n        let now = new Date();";
$newPromoCode = "    isPromoActive(item) {\n        if (!item) return false;\n        let now = new Date();";
$content = str_replace($oldPromoCode, $newPromoCode, $content);

// 4. Safe isWishlisted
$oldWishlisted = "    isWishlisted(item) {\n        return this.wishlist.some(w => w.id === item.id);\n    },";
$newWishlisted = "    isWishlisted(item) {\n        if (!item) return false;\n        return this.wishlist.some(w => w.id === item.id);\n    },";
$content = str_replace($oldWishlisted, $newWishlisted, $content);

// 5. Update addToCart(item)
$oldAddToCart = '    addToCart(item) {
        if (!this.isLoggedIn) {
            this.authTab = \'login\';
            this.authModalOpen = true;
            return;
        }
        let effectivePrice = this.getEffectivePrice(item);
        let found = this.cart.find(c => c.id === item.id);
        if (found) {
            if (found.quantity < item.stok_barang) {
                found.quantity++;
            } else {
                alert(\'Stok barang di toko tidak mencukupi!\');
            }
        } else {
            this.cart.push({
                id: item.id,
                nama_barang: item.nama_barang,
                harga: effectivePrice,
                stok: item.stok_barang,
                unit: item.unit ? item.unit.name : \'Pcs\',
                quantity: 1
            });
        }
    },';

$newAddToCart = '    addToCart(item) {
        if (!item) return;
        if (!this.isLoggedIn) {
            this.authTab = \'login\';
            this.authModalOpen = true;
            this.showToast(\'Silakan log in terlebih dahulu.\', \'info\');
            return;
        }
        let effectivePrice = this.getEffectivePrice(item);
        let found = this.cart.find(c => c.id === item.id);
        if (found) {
            if (found.quantity < item.stok_barang) {
                found.quantity++;
                this.showToast(\'Jumlah \' + item.nama_barang + \' ditambah ke keranjang.\', \'success\');
            } else {
                this.showToast(\'Stok \' + item.nama_barang + \' tidak mencukupi!\', \'error\');
            }
        } else {
            this.cart.push({
                id: item.id,
                nama_barang: item.nama_barang,
                harga: effectivePrice,
                stok: item.stok_barang,
                unit: item.unit ? item.unit.name : \'Pcs\',
                quantity: 1
            });
            this.showToast(item.nama_barang + \' berhasil ditambahkan ke keranjang.\', \'success\');
        }
    },';
$content = str_replace($oldAddToCart, $newAddToCart, $content);

// 6. Update removeFromCart(index)
$oldRemove = '    removeFromCart(index) {
        this.cart.splice(index, 1);
    },';
$newRemove = '    removeFromCart(index) {
        let item = this.cart[index];
        this.cart.splice(index, 1);
        if (item) {
            this.showToast(item.nama_barang + \' dihapus dari keranjang.\', \'info\');
        }
    },';
$content = str_replace($oldRemove, $newRemove, $content);

// 7. Update toggleWishlist(item)
$oldToggleWishlist = '    toggleWishlist(item) {
        let idx = this.wishlist.findIndex(w => w.id === item.id);
        if (idx !== -1) {
            this.wishlist.splice(idx, 1);
        } else {
            this.wishlist.push({
                id: item.id,
                nama_barang: item.nama_barang,
                harga: this.getEffectivePrice(item),
                unit: item.unit ? item.unit.name : \'Pcs\',
                stok_barang: item.stok_barang
            });
        }
        localStorage.setItem(\'store_wishlist\', JSON.stringify(this.wishlist));
    },';

$newToggleWishlist = '    toggleWishlist(item) {
        if (!item) return;
        let idx = this.wishlist.findIndex(w => w.id === item.id);
        if (idx !== -1) {
            this.wishlist.splice(idx, 1);
            this.showToast(item.nama_barang + \' dihapus dari wishlist.\', \'info\');
        } else {
            this.wishlist.push({
                id: item.id,
                nama_barang: item.nama_barang,
                harga: this.getEffectivePrice(item),
                unit: item.unit ? item.unit.name : \'Pcs\',
                stok_barang: item.stok_barang
            });
            this.showToast(item.nama_barang + \' ditambahkan ke wishlist.\', \'success\');
        }
        localStorage.setItem(\'store_wishlist\', JSON.stringify(this.wishlist));
    },';
$content = str_replace($oldToggleWishlist, $newToggleWishlist, $content);

// 8. Update submitLogin()
$oldSubmitLogin = '            if (response.ok && result.success) {
                this.isLoggedIn = true;
                this.currentUser = result.user;
                this.customerName = result.user.name;
                if (result.user.phone)   this.customerPhone   = result.user.phone;
                if (result.user.address) this.customerAddress = result.user.address;
                this.authModalOpen = false;
                this.authEmail = \'\';
                this.authPassword = \'\';
                this.loadOrders();
            } else {
                this.authError = result.message || \'Login gagal. Silakan periksa kembali email & password Anda.\';
            }';

$newSubmitLogin = '            if (response.ok && result.success) {
                this.isLoggedIn = true;
                this.currentUser = result.user;
                this.customerName = result.user.name;
                if (result.user.phone)   this.customerPhone   = result.user.phone;
                if (result.user.address) this.customerAddress = result.user.address;
                this.authModalOpen = false;
                this.authEmail = \'\';
                this.authPassword = \'\';
                if (result.csrf_token) {
                    document.querySelector(\'meta[name="csrf-token"]\').setAttribute(\'content\', result.csrf_token);
                }
                this.showToast(\'Login berhasil! Selamat datang.\', \'success\');
                this.loadOrders();
            } else {
                this.authError = result.message || \'Login gagal. Silakan periksa kembali email & password Anda.\';
                this.showToast(this.authError, \'error\');
            }';
$content = str_replace($oldSubmitLogin, $newSubmitLogin, $content);

// 9. Update submitRegister()
$oldSubmitRegister = '            if (response.ok && result.success) {
                this.isLoggedIn = true;
                this.currentUser = result.user;
                this.customerName = result.user.name;
                this.authModalOpen = false;
                this.authName = \'\';
                this.authEmail = \'\';
                this.authPassword = \'\';
            } else {
                this.authError = result.message || \'Pendaftaran gagal. Pastikan email belum terdaftar.\';
            }';

$newSubmitRegister = '            if (response.ok && result.success) {
                this.isLoggedIn = true;
                this.currentUser = result.user;
                this.customerName = result.user.name;
                this.authModalOpen = false;
                this.authName = \'\';
                this.authEmail = \'\';
                this.authPassword = \'\';
                if (result.csrf_token) {
                    document.querySelector(\'meta[name="csrf-token"]\').setAttribute(\'content\', result.csrf_token);
                }
                this.showToast(\'Registrasi berhasil! Selamat datang.\', \'success\');
            } else {
                this.authError = result.message || \'Pendaftaran gagal. Pastikan email belum terdaftar.\';
                this.showToast(this.authError, \'error\');
            }';
$content = str_replace($oldSubmitRegister, $newSubmitRegister, $content);

// 10. Update submitCheckout() success/error toasts
$oldCheckout = '            if (response.ok && result.success) {
                this.orderTotal = result.total;
                this.checkoutSuccess = true;';

$newCheckout = '            if (response.ok && result.success) {
                this.orderTotal = result.total;
                this.checkoutSuccess = true;
                this.showToast(\'Pesanan berhasil dibuat!\', \'success\');';
$content = str_replace($oldCheckout, $newCheckout, $content);

$oldCheckoutErr1 = '} else {
                this.checkoutError = result.message || \'Terjadi kesalahan saat memproses checkout.\';
            }';
$newCheckoutErr1 = '} else {
                this.checkoutError = result.message || \'Terjadi kesalahan saat memproses checkout.\';
                this.showToast(this.checkoutError, \'error\');
            }';
$content = str_replace($oldCheckoutErr1, $newCheckoutErr1, $content);

$oldCheckoutErr2 = '} catch(e) {
            this.checkoutError = \'Gagal terhubung ke server. Silakan periksa koneksi Anda.\';
        }';
$newCheckoutErr2 = '} catch(e) {
            this.checkoutError = \'Gagal terhubung ke server. Silakan periksa koneksi Anda.\';
            this.showToast(this.checkoutError, \'error\');
        }';
$content = str_replace($oldCheckoutErr2, $newCheckoutErr2, $content);

// 11. Add pointer-events-none to the Lucide X icon close buttons
// We want to target: <i data-lucide="x" class="w-5 h-5 text-stone-500"></i> or similar
// Note: Some buttons have <i data-lucide="x" class="w-5 h-5"></i>
$content = str_replace('<i data-lucide="x" class="w-5 h-5 text-stone-500"></i>', '<i data-lucide="x" class="w-5 h-5 text-stone-500 pointer-events-none"></i>', $content);
$content = str_replace('<i data-lucide="x" class="w-5 h-5"></i>', '<i data-lucide="x" class="w-5 h-5 pointer-events-none"></i>', $content);

// 12. Add dynamic toast stack overlay right before </body>
$toastStackHtml = '
    <!-- Luxury Toast Notification Stack -->
    <div class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300 transform translate-y-[-10px] opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 opacity-0 transform scale-95"
                 class="pointer-events-auto flex items-center gap-3.5 px-5 py-4 bg-white/95 backdrop-blur-md border border-stone-200/80 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.06)]">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <template x-if="toast.type === \'success\'">
                        <i data-lucide="check" class="w-4 h-4 text-brand-darkGreen pointer-events-none"></i>
                    </template>
                    <template x-if="toast.type === \'error\'">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 pointer-events-none"></i>
                    </template>
                    <template x-if="toast.type === \'info\'">
                        <i data-lucide="info" class="w-4 h-4 text-brand-gold pointer-events-none"></i>
                    </template>
                </div>
                <!-- Message -->
                <div class="flex-grow">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-brand-coal" x-text="toast.message"></p>
                </div>
                <!-- Close btn -->
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-stone-400 hover:text-stone-600 transition-colors p-0.5">
                    <i data-lucide="x" class="w-3.5 h-3.5 pointer-events-none"></i>
                </button>
            </div>
        </template>
    </div>
';

$content = str_replace('</body>', $toastStackHtml . '</body>', $content);

file_put_contents($file, $content);
echo "Successfully updated $file\n";
