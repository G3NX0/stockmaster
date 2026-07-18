<!DOCTYPE html>
<html lang="en" x-data="storeApp">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.initialUser = @json(Auth::user());
        // Always read fresh CSRF token from meta tag
        function getCsrf() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
    </script>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="description" content="A curated luxury full-width marketplace sync with StockMaster inventory system."/>
    <title>StockStore | Curated Full-Width Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            gold: '#C4A47C',
                            darkGreen: '#1E3F20',
                            linen: '#F8F6F2',
                            coal: '#1C1917',
                            clay: '#854D0E',
                            border: '#E7E5E4'
                        }
                    }
                }
            }
        }
    </script>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('storeApp', () => ({ 
    activeTab: 'catalog',
    cartOpen: false,
    wishlistOpen: false,
    detailModalOpen: false,
    mobileFiltersOpen: false,
    selectedItem: null,
    checkoutSuccess: false,
    checkoutError: '',
    checkoutLoading: false,
    orderTotal: 0,
    
    // Auth State
    isLoggedIn: false,
    currentUser: null,
    authModalOpen: false,
    authTab: 'login', // 'login' or 'register'
    
    // Auth Form Inputs
    authName: '',
    authEmail: '',
    authPassword: '',
    authError: '',
    authLoading: false,

    // Form Inputs
    customerName: localStorage.getItem('profile_name') || '',
    customerPhone: localStorage.getItem('profile_phone') || '',
    customerAddress: localStorage.getItem('profile_address') || '',
    paymentMethod: 'bank_transfer',
    
    // Contact Form
    contactName: '',
    contactEmail: '',
    contactMessage: '',
    contactSubmitted: false,
    
    // Newsletter
    newsletterEmail: '',
    newsletterSubscribed: false,

    // Filters
    searchQuery: '',
    selectedCategory: 'all',
    statusFilter: 'all', 
    sortBy: 'name_asc',
    minPrice: '',
    maxPrice: '',
    
    // Collections
    cart: JSON.parse(localStorage.getItem('store_cart') || '[]'),
    wishlist: JSON.parse(localStorage.getItem('store_wishlist') || '[]'),
    orders: [],
    items: {!! json_encode($items) !!},
    categories: {!! json_encode($categories) !!},
    
    // FAQ Accordion
    openFaq: null,
    
    // Countdown Timer values
    hours: 4,
    minutes: 12,
    seconds: 30,

    toasts: [],
    showToast(message, type = 'success') {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, message, type });
        setTimeout(() => {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }, 4000);
    },

    init() {
        this.currentUser = window.initialUser || null;
        this.isLoggedIn = !!this.currentUser;
        if (this.isLoggedIn && this.currentUser) {
            this.customerName = this.currentUser.name;
        }
        setInterval(() => {
            if (this.seconds > 0) {
                this.seconds--;
            } else {
                this.seconds = 59;
                if (this.minutes > 0) {
                    this.minutes--;
                } else {
                    this.minutes = 59;
                    if (this.hours > 0) {
                        this.hours--;
                    }
                }
            }
        }, 1000);

        // Persist cart to localStorage on every change
        this.$watch('cart', (val) => localStorage.setItem('store_cart', JSON.stringify(val)));

        // Auto-fill profile from DB user object
        if (this.isLoggedIn && this.currentUser) {
            if (this.currentUser.phone)   this.customerPhone   = this.currentUser.phone;
            if (this.currentUser.address) this.customerAddress = this.currentUser.address;
        }

        // Load real order history from server (only if logged in)
        if (this.isLoggedIn) this.loadOrders();
    },

    async loadOrders() {
        try {
            const res  = await fetch('/store/orders', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.orders) this.orders = data.orders;
        } catch(e) { /* fallback: keep localStorage orders */ }
    },
    
    async submitLogin() {
        this.authLoading = true;
        this.authError = '';
        try {
            let response = await fetch('{{ route('store.login') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: this.authEmail,
                    password: this.authPassword
                })
            });
            let result = await response.json();
            if (response.ok && result.success) {
                this.isLoggedIn = true;
                this.currentUser = result.user;
                this.customerName = result.user.name;
                if (result.user.phone)   this.customerPhone   = result.user.phone;
                if (result.user.address) this.customerAddress = result.user.address;
                this.authModalOpen = false;
                this.authEmail = '';
                this.authPassword = '';
                if (result.csrf_token) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.csrf_token);
                }
                this.showToast('Login berhasil! Selamat datang.', 'success');
                this.loadOrders();
            } else {
                this.authError = result.message || 'Login gagal. Silakan periksa kembali email & password Anda.';
                this.showToast(this.authError, 'error');
            }
        } catch (e) {
            this.authError = 'Terjadi kesalahan koneksi saat login.';
        } finally {
            this.authLoading = false;
        }
    },

    async submitRegister() {
        this.authLoading = true;
        this.authError = '';
        try {
            let response = await fetch('{{ route('store.register') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: this.authName,
                    email: this.authEmail,
                    password: this.authPassword
                })
            });
            let result = await response.json();
            if (response.ok && result.success) {
                this.isLoggedIn = true;
                this.currentUser = result.user;
                this.customerName = result.user.name;
                this.authModalOpen = false;
                this.authName = '';
                this.authEmail = '';
                this.authPassword = '';
                if (result.csrf_token) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.csrf_token);
                }
                this.showToast('Registrasi berhasil! Selamat datang.', 'success');
            } else {
                this.authError = result.message || 'Pendaftaran gagal. Pastikan email belum terdaftar.';
                this.showToast(this.authError, 'error');
            }
        } catch (e) {
            this.authError = 'Terjadi kesalahan koneksi saat mendaftar.';
        } finally {
            this.authLoading = false;
        }
    },

    async triggerLogout() {
        if (!confirm('Apakah Anda yakin ingin keluar?')) return;
        try {
            let response = await fetch('{{ route('logout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                this.isLoggedIn = false;
                this.currentUser = null;
                this.customerName = '';
                alert('Anda telah keluar.');
                window.location.reload();
            }
        } catch (e) {
            alert('Gagal melakukan logout.');
        }
    },
    
    getEffectivePrice(item) {
        if (!item) return 0;
        let now = new Date();
        if (item.promo_price) {
            let start = item.promo_start_date ? new Date(item.promo_start_date) : null;
            let end = item.promo_end_date ? new Date(item.promo_end_date) : null;
            if ((!start || start <= now) && (!end || end >= now)) {
                return parseFloat(item.promo_price);
            }
        }
        return parseFloat(item.selling_price || item.harga_barang);
    },

    isPromoActive(item) {
        if (!item) return false;
        let now = new Date();
        if (item.promo_price) {
            let start = item.promo_start_date ? new Date(item.promo_start_date) : null;
            let end = item.promo_end_date ? new Date(item.promo_end_date) : null;
            return (!start || start <= now) && (!end || end >= now);
        }
        return false;
    },

    addToCart(item) {
        if (!item) return;
        if (!this.isLoggedIn) {
            this.authTab = 'login';
            this.authModalOpen = true;
            this.showToast('Silakan log in terlebih dahulu.', 'info');
            return;
        }
        let effectivePrice = this.getEffectivePrice(item);
        let found = this.cart.find(c => c.id === item.id);
        if (found) {
            if (found.quantity < item.stok_barang) {
                found.quantity++;
                this.showToast('Jumlah ' + item.nama_barang + ' ditambah ke keranjang.', 'success');
            } else {
                this.showToast('Stok ' + item.nama_barang + ' tidak mencukupi!', 'error');
            }
        } else {
            this.cart.push({
                id: item.id,
                nama_barang: item.nama_barang,
                harga: effectivePrice,
                stok: item.stok_barang,
                unit: item.unit ? item.unit.name : 'Pcs',
                quantity: 1
            });
            this.showToast(item.nama_barang + ' berhasil ditambahkan ke keranjang.', 'success');
        }
    },

    removeFromCart(index) {
        let item = this.cart[index];
        this.cart.splice(index, 1);
        if (item) {
            this.showToast(item.nama_barang + ' dihapus dari keranjang.', 'info');
        }
    },

    updateQuantity(index, amount) {
        let item = this.cart[index];
        let newQty = item.quantity + amount;
        if (newQty >= 1 && newQty <= item.stok) {
            item.quantity = newQty;
        }
    },

    toggleWishlist(item) {
        if (!item) return;
        let idx = this.wishlist.findIndex(w => w.id === item.id);
        if (idx !== -1) {
            this.wishlist.splice(idx, 1);
            this.showToast(item.nama_barang + ' dihapus dari wishlist.', 'info');
        } else {
            this.wishlist.push({
                id: item.id,
                nama_barang: item.nama_barang,
                harga: this.getEffectivePrice(item),
                unit: item.unit ? item.unit.name : 'Pcs',
                stok_barang: item.stok_barang
            });
            this.showToast(item.nama_barang + ' ditambahkan ke wishlist.', 'success');
        }
        localStorage.setItem('store_wishlist', JSON.stringify(this.wishlist));
    },

    isWishlisted(item) {
        if (!item) return false;
        return this.wishlist.some(w => w.id === item.id);
    },

    clearFilters() {
        this.searchQuery = '';
        this.selectedCategory = 'all';
        this.statusFilter = 'all';
        this.sortBy = 'name_asc';
        this.minPrice = '';
        this.maxPrice = '';
    },

    async saveProfile() {
        localStorage.setItem('profile_name', this.customerName);
        localStorage.setItem('profile_phone', this.customerPhone);
        localStorage.setItem('profile_address', this.customerAddress);
        if (this.isLoggedIn) {
            try {
                await fetch('/store/profile', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                    body: JSON.stringify({ phone: this.customerPhone, address: this.customerAddress })
                });
            } catch(e) { /* ignore - localStorage already saved */ }
        }
        alert('Informasi profil berhasil disimpan!');
    },

    get filteredItems() {
        let list = this.items.filter(item => {
            const matchesSearch = item.nama_barang.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  item.kode_barang.toLowerCase().includes(this.searchQuery.toLowerCase());
            
            const matchesCategory = this.selectedCategory === 'all' || item.category_id == this.selectedCategory;
            
            let matchesStatus = true;
            if (this.statusFilter === 'ready') {
                matchesStatus = item.stok_barang > item.min_stock;
            } else if (this.statusFilter === 'promo') {
                matchesStatus = this.isPromoActive(item);
            } else if (this.statusFilter === 'wholesale') {
                matchesStatus = !!item.wholesale_price;
            }

            let effectivePrice = this.getEffectivePrice(item);
            let matchesMinPrice = this.minPrice === '' || effectivePrice >= parseFloat(this.minPrice);
            let matchesMaxPrice = this.maxPrice === '' || effectivePrice <= parseFloat(this.maxPrice);

            return matchesSearch && matchesCategory && matchesStatus && matchesMinPrice && matchesMaxPrice;
        });

        if (this.sortBy === 'name_asc') {
            list.sort((a, b) => a.nama_barang.localeCompare(b.nama_barang));
        } else if (this.sortBy === 'price_asc') {
            list.sort((a, b) => this.getEffectivePrice(a) - this.getEffectivePrice(b));
        } else if (this.sortBy === 'price_desc') {
            list.sort((a, b) => this.getEffectivePrice(b) - this.getEffectivePrice(a));
        } else if (this.sortBy === 'stock_desc') {
            list.sort((a, b) => b.stok_barang - a.stok_barang);
        }

        return list;
    },

    get cartCount() {
        return this.cart.reduce((sum, c) => sum + c.quantity, 0);
    },

    get cartSubtotal() {
        return this.cart.reduce((sum, c) => sum + (c.harga * c.quantity), 0);
    },

    get cartTax() {
        return Math.round(this.cartSubtotal * 0.11); // PPN 11%
    },

    get cartTotal() {
        return this.cartSubtotal + this.cartTax;
    },

    async submitCheckout() {
        if (!this.isLoggedIn) {
            this.authTab = 'login';
            this.authModalOpen = true;
            return;
        }
        if (this.cart.length === 0) return;
        this.checkoutLoading = true;
        this.checkoutError = '';
        try {
            let response = await fetch('{{ route('store.checkout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    customer_name: this.customerName,
                    customer_phone: this.customerPhone,
                    customer_address: this.customerAddress,
                    payment_method: this.paymentMethod,
                    cart: this.cart
                })
            });
            let result = await response.json();
            if (response.ok && result.success) {
                this.orderTotal = result.total;
                this.checkoutSuccess = true;
                this.showToast('Pesanan berhasil dibuat!', 'success');
                
                // Save profile inputs
                localStorage.setItem('profile_name', this.customerName);
                localStorage.setItem('profile_phone', this.customerPhone);
                localStorage.setItem('profile_address', this.customerAddress);

                // Add to client orders list
                let dateStr = new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                this.orders.unshift({
                    id: 'ORD-' + Math.floor(100000 + Math.random() * 900000),
                    date: dateStr,
                    total: result.total,
                    items: this.cart.map(c => ({ name: c.nama_barang, qty: c.quantity, price: c.harga })),
                    payment: this.paymentMethod,
                    status: 'Terkonfirmasi (WhatsApp Terkirim)'
                });
                localStorage.setItem('store_orders', JSON.stringify(this.orders));
                
                this.cart = [];
            } else {
                this.checkoutError = result.message || 'Terjadi kesalahan saat memproses checkout.';
                this.showToast(this.checkoutError, 'error');
            }
        } catch(e) {
            this.checkoutError = 'Gagal terhubung ke server. Silakan periksa koneksi Anda.';
            this.showToast(this.checkoutError, 'error');
        } finally {
            this.checkoutLoading = false;
        }
    }
}));
        });
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8F6F2;
            color: #1C1917;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }
        .serif-font {
            font-family: 'Playfair Display', serif;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased min-h-screen relative flex flex-col justify-between">

    <!-- Top Announcement Utility Bar -->
    <div class="bg-brand-darkGreen text-stone-200 text-[10px] font-bold tracking-widest uppercase py-2 px-6 flex justify-between items-center border-b border-white/10">
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span>Real-time Inventory Link: Active</span>
        </div>
        <div class="hidden md:block">
            <span>Special promo: discount up to 25% this season</span>
        </div>
        <div class="flex items-center gap-4">
            <span>WhatsApp Support: +62 812-3456-789</span>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-stone-200/80 transition-all duration-300">
        <nav class="w-full px-8 md:px-12 h-20 flex justify-between items-center">
            
            <!-- Branding -->
            <div class="flex items-center gap-10">
                <span class="serif-font text-2xl font-bold tracking-tight text-brand-darkGreen cursor-pointer" @click="activeTab = 'catalog'; window.scrollTo(0,0)">StockStore.</span>
                
                <!-- Main Nav Links -->
                <div class="hidden lg:flex items-center gap-8">
                    <button @click="activeTab = 'catalog'; window.scrollTo(0,0)" class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-brand-darkGreen pb-2 border-b-2" :class="activeTab === 'catalog' ? 'text-brand-darkGreen border-brand-darkGreen' : 'text-stone-500 border-transparent'">Catalog</button>
                    <button @click="activeTab = 'profile'; window.scrollTo(0,0)" class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-brand-darkGreen pb-2 border-b-2" :class="activeTab === 'profile' ? 'text-brand-darkGreen border-brand-darkGreen' : 'text-stone-500 border-transparent'">My Account & Orders</button>
                    <button @click="activeTab = 'faq'; window.scrollTo(0,0)" class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-brand-darkGreen pb-2 border-b-2" :class="activeTab === 'faq' ? 'text-brand-darkGreen border-brand-darkGreen' : 'text-stone-500 border-transparent'">Help & FAQs</button>
                    <button @click="activeTab = 'contact'; window.scrollTo(0,0)" class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-brand-darkGreen pb-2 border-b-2" :class="activeTab === 'contact' ? 'text-brand-darkGreen border-brand-darkGreen' : 'text-stone-500 border-transparent'">Contact Us</button>
                </div>
            </div>

            <!-- Global Actions -->
            <div class="flex items-center gap-6">
                <!-- Mobile Navigation Menu Toggle -->
                <div class="lg:hidden relative" x-data="{ openNav: false }">
                    <button @click="openNav = !openNav" class="p-2 border border-stone-200 rounded-lg hover:bg-stone-50">
                        <i data-lucide="menu" class="w-5 h-5 text-stone-700"></i>
                    </button>
                    <div x-show="openNav" @click.away="openNav = false" class="absolute right-0 mt-2 w-48 bg-white border border-stone-200 rounded-xl shadow-lg py-2 z-50 text-left">
                        <button @click="activeTab = 'catalog'; openNav = false" class="w-full text-left px-4 py-2.5 text-xs font-semibold hover:bg-stone-50">Catalog</button>
                        <button @click="activeTab = 'profile'; openNav = false" class="w-full text-left px-4 py-2.5 text-xs font-semibold hover:bg-stone-50">My Account</button>
                        <button @click="activeTab = 'faq'; openNav = false" class="w-full text-left px-4 py-2.5 text-xs font-semibold hover:bg-stone-50">FAQs</button>
                        <button @click="activeTab = 'contact'; openNav = false" class="w-full text-left px-4 py-2.5 text-xs font-semibold hover:bg-stone-50">Contact Us</button>
                        <hr class="border-stone-100 my-1">
                        <!-- Mobile Auth Actions -->
                        <template x-if="!isLoggedIn">
                            <div>
                                <button @click="authTab = 'login'; authModalOpen = true; openNav = false" class="w-full text-left px-4 py-2.5 text-xs font-bold text-stone-600 hover:bg-stone-50">Login</button>
                                <button @click="authTab = 'register'; authModalOpen = true; openNav = false" class="w-full text-left px-4 py-2.5 text-xs font-bold text-brand-darkGreen hover:bg-stone-50">Sign Up</button>
                            </div>
                        </template>
                        <template x-if="isLoggedIn && currentUser">
                            <div>
                                <template x-if="currentUser.role === 'admin' || currentUser.role === 'staff'">
                                    <a href="/dashboard" class="block w-full text-left px-4 py-2.5 text-xs font-bold text-brand-darkGreen hover:bg-stone-50">Dashboard Admin</a>
                                </template>
                                <button @click="triggerLogout(); openNav = false" class="w-full text-left px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-stone-50">Sign Out</button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- User Account / Auth Widget -->
                <div class="relative" x-data="{ openProfile: false }">
                    <!-- Guest View -->
                    <template x-if="!isLoggedIn">
                        <div class="hidden sm:flex items-center gap-4">
                            <button @click="authTab = 'login'; authModalOpen = true" class="text-xs font-bold uppercase tracking-wider text-stone-600 hover:text-brand-darkGreen transition-colors">Login</button>
                            <button @click="authTab = 'register'; authModalOpen = true" class="text-xs font-bold uppercase tracking-wider text-white bg-brand-darkGreen hover:bg-brand-darkGreen/90 px-4 py-2 rounded-lg transition-all shadow-md">Sign Up</button>
                        </div>
                    </template>
                    <template x-if="!isLoggedIn">
                        <button @click="authTab = 'login'; authModalOpen = true" class="sm:hidden p-2 text-stone-600 hover:text-brand-darkGreen">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </button>
                    </template>

                    <!-- Authenticated User View -->
                    <template x-if="isLoggedIn && currentUser">
                        <div class="flex items-center gap-2">
                            <button @click="openProfile = !openProfile" class="flex items-center gap-2 p-1.5 rounded-xl border border-stone-200/60 hover:bg-stone-50 transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-brand-darkGreen/10 text-brand-darkGreen font-bold text-xs flex items-center justify-center uppercase" x-text="currentUser.name.substring(0,2)"></div>
                                <span class="text-xs font-bold text-stone-700 hidden md:inline" x-text="currentUser.name"></span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-stone-500 hidden md:inline"></i>
                            </button>
                            <!-- Profile Dropdown -->
                            <div x-show="openProfile" @click.away="openProfile = false" x-transition class="absolute right-0 mt-2 w-52 bg-white border border-stone-200 rounded-xl shadow-xl py-2 z-50 text-left">
                                <div class="px-4 py-2 border-b border-stone-100">
                                    <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest block">Signed In As</p>
                                    <p class="text-xs font-bold text-brand-coal truncate block" x-text="currentUser.email"></p>
                                </div>
                                <template x-if="currentUser.role === 'admin' || currentUser.role === 'staff'">
                                    <a href="/dashboard" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-brand-darkGreen hover:bg-stone-50 transition-colors">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Admin Dashboard
                                    </a>
                                </template>
                                <button @click="triggerLogout(); openProfile = false" class="w-full text-left flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Wishlist -->
                <button @click="wishlistOpen = true" class="relative p-2 hover:opacity-80 transition-opacity">
                    <i data-lucide="heart" class="w-5 h-5 text-stone-700"></i>
                    <span x-show="wishlist.length > 0" x-text="wishlist.length" class="absolute -top-1 -right-1 bg-brand-clay text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                </button>

                <!-- Cart Button -->
                <button @click="cartOpen = true" class="relative p-2 flex items-center gap-2 hover:opacity-80 transition-opacity border-l border-stone-200 pl-6">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-brand-darkGreen"></i>
                    <span class="text-xs font-semibold text-brand-coal hidden sm:inline">Cart</span>
                    <span x-show="cartCount > 0" x-text="cartCount" class="bg-brand-darkGreen text-white text-[9px] font-bold px-2 py-0.5 rounded-full"></span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Main Content Fluid Section -->
    <main class="w-full px-8 md:px-12 py-10 flex-grow space-y-16">

        <!-- ==================== PAGE 1: CATALOG ==================== -->
        <div x-show="activeTab === 'catalog'" class="space-y-16">
            
            <!-- Full-Width Hero Section -->
            <section class="bg-[#ECE9E2] rounded-3xl p-8 md:p-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center border border-stone-200/50 relative overflow-hidden">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(196,164,124,0.1),transparent)] pointer-events-none"></div>
                
                <div class="space-y-6 text-left relative z-10">
                    <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-brand-gold bg-white/60 px-3 py-1.5 rounded-full border border-stone-200">System Registry v12.4</span>
                    <h1 class="serif-font text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-brand-darkGreen leading-tight">Refined inventory.<br>Sync in harmony.</h1>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed font-medium max-w-xl">
                        Kami menyederhanakan cara Anda berbelanja stok barang dagangan. Pilih produk premium, kirim pesanan, dan biarkan modul internal kami memotong database utama sekaligus melayangkan resi transaksi langsung ke WhatsApp Anda.
                    </p>
                    <div class="pt-4 flex flex-wrap gap-4">
                        <button @click="document.getElementById('catalog-grid').scrollIntoView({ behavior: 'smooth' })" class="bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-wider transition-all">
                            Browse Collection
                        </button>
                        <button @click="activeTab = 'contact'" class="bg-white border border-stone-300 hover:bg-stone-50 text-stone-700 px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-wider transition-all">
                            Contact Logistics
                        </button>
                    </div>
                </div>

                <div class="hidden lg:flex justify-end pr-12 relative">
                    <div class="w-72 h-72 bg-white/40 rounded-full flex items-center justify-center border border-white/60">
                        <div class="w-56 h-56 bg-white/80 rounded-full flex items-center justify-center shadow-lg">
                            <i data-lucide="package" class="w-24 h-24 text-brand-darkGreen/60"></i>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Value Proposition ribbon -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Value 1 -->
                <div class="bg-white border border-stone-200/60 p-6 rounded-2xl flex items-start gap-4 text-left shadow-sm">
                    <div class="p-3 bg-brand-darkGreen/10 rounded-xl text-brand-darkGreen">
                        <i data-lucide="truck" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-brand-coal">Free Shipping Registry</h4>
                        <p class="text-xs text-stone-500 mt-1">Pengiriman gratis untuk pesanan stok di atas Rp 500.000 ke area Jabodetabek.</p>
                    </div>
                </div>
                <!-- Value 2 -->
                <div class="bg-white border border-stone-200/60 p-6 rounded-2xl flex items-start gap-4 text-left shadow-sm">
                    <div class="p-3 bg-brand-darkGreen/10 rounded-xl text-brand-darkGreen">
                        <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-brand-coal">Live Database Sync</h4>
                        <p class="text-xs text-stone-500 mt-1">Stok terpotong realtime dan langsung mengupdate grafik laporan di dashboard admin.</p>
                    </div>
                </div>
                <!-- Value 3 -->
                <div class="bg-white border border-stone-200/60 p-6 rounded-2xl flex items-start gap-4 text-left shadow-sm">
                    <div class="p-3 bg-brand-darkGreen/10 rounded-xl text-brand-darkGreen">
                        <i data-lucide="message-square" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-brand-coal">WhatsApp Digital Receipt</h4>
                        <p class="text-xs text-stone-500 mt-1">Resi detail langsung dikirim ke nomor WhatsApp Anda lengkap dengan total dan invoice.</p>
                    </div>
                </div>
            </section>

            <!-- Browse by category visual grid -->
            <section class="space-y-6 text-left">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-2">
                    <div>
                        <span class="text-[9px] font-bold text-stone-400 uppercase tracking-widest block">Stok Terkategorisasi</span>
                        <h3 class="serif-font text-2xl font-bold text-brand-coal">Jelajahi Kategori Utama</h3>
                    </div>
                    <span class="text-xs text-stone-500">Klik kategori untuk memfilter katalog produk di bawah.</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
                    <!-- Cat Card 1: All -->
                    <div @click="selectedCategory = 'all'; document.getElementById('catalog-grid').scrollIntoView({ behavior: 'smooth' })" 
                         class="bg-white border border-stone-200/60 rounded-xl p-5 text-center cursor-pointer hover:border-brand-gold hover:shadow-lg transition-all"
                         :class="selectedCategory === 'all' ? 'border-brand-darkGreen bg-brand-darkGreen/5' : ''">
                        <div class="w-12 h-12 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="layout-grid" class="w-5 h-5 text-brand-darkGreen"></i>
                        </div>
                        <span class="text-xs font-bold text-brand-coal">All Products</span>
                    </div>

                    <!-- Cat Cards -->
                    <template x-for="cat in categories" :key="cat.id">
                        <div @click="selectedCategory = cat.id; document.getElementById('catalog-grid').scrollIntoView({ behavior: 'smooth' })" 
                             class="bg-white border border-stone-200/60 rounded-xl p-5 text-center cursor-pointer hover:border-brand-gold hover:shadow-lg transition-all"
                             :class="selectedCategory == cat.id ? 'border-brand-darkGreen bg-brand-darkGreen/5' : ''">
                            <div class="w-12 h-12 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="box" class="w-5 h-5 text-brand-darkGreen"></i>
                            </div>
                            <span class="text-xs font-bold text-brand-coal truncate block" x-text="cat.name"></span>
                        </div>
                    </template>
                </div>
            </section>

            <!-- Hot Deals Countdown and Promo Banner -->
            <section class="bg-brand-darkGreen text-stone-100 rounded-3xl p-8 lg:p-12 grid grid-cols-1 lg:grid-cols-3 gap-8 items-center border border-white/10 text-left relative overflow-hidden">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(196,164,124,0.15),transparent)] pointer-events-none"></div>
                <div class="lg:col-span-2 space-y-4">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-brand-gold">Flash Deal Registry</span>
                    <h3 class="serif-font text-3xl font-bold text-white">Diskon Spesial Sinkronisasi Database</h3>
                    <p class="text-xs text-stone-300 leading-relaxed max-w-xl">
                        Dapatkan potongan harga langsung untuk produk-produk bertanda khusus. Masukkan kode kupon saat checkout untuk klaim penawaran terbatas.
                    </p>
                    
                    <!-- Copy Coupon -->
                    <div class="inline-flex items-center bg-white/5 border border-white/10 rounded-lg p-2 gap-4">
                        <span class="text-xs font-mono text-brand-gold tracking-widest">COUPON CODE: SMARTSYNC</span>
                        <button onclick="navigator.clipboard.writeText('SMARTSYNC'); alert('Kode kupon disalin!');" class="bg-white text-brand-darkGreen px-3 py-1 rounded text-[10px] font-bold uppercase">
                            Copy
                        </button>
                    </div>
                </div>

                <!-- Timer countdown -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center space-y-4">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-brand-gold">Penawaran Berakhir Dalam:</h4>
                    <div class="flex justify-center gap-3">
                        <div class="bg-white text-brand-darkGreen w-12 py-2 rounded-lg">
                            <span class="text-lg font-bold block leading-none" x-text="hours.toString().padStart(2, '0')"></span>
                            <span class="text-[8px] font-bold uppercase tracking-widest block text-stone-400 mt-1">Hrs</span>
                        </div>
                        <div class="bg-white text-brand-darkGreen w-12 py-2 rounded-lg">
                            <span class="text-lg font-bold block leading-none" x-text="minutes.toString().padStart(2, '0')"></span>
                            <span class="text-[8px] font-bold uppercase tracking-widest block text-stone-400 mt-1">Min</span>
                        </div>
                        <div class="bg-white text-brand-darkGreen w-12 py-2 rounded-lg">
                            <span class="text-lg font-bold block leading-none" x-text="seconds.toString().padStart(2, '0')"></span>
                            <span class="text-[8px] font-bold uppercase tracking-widest block text-stone-400 mt-1">Sec</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Grid: Sidebar Filter + Catalog Viewport -->
            <div id="catalog-grid" class="flex flex-col lg:flex-row gap-8 items-start pt-6 border-t border-stone-200/60">
                
                <!-- Mobile Filter Drawer Backdrop & Panel -->
                <div x-cloak x-show="mobileFiltersOpen" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
                    <!-- Backdrop -->
                    <div x-show="mobileFiltersOpen" 
                         x-transition:enter="transition-opacity ease-linear duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-linear duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="mobileFiltersOpen = false"
                         class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs"></div>

                    <!-- Panel container -->
                    <div class="fixed inset-y-0 left-0 flex max-w-full">
                        <!-- Panel -->
                        <div x-show="mobileFiltersOpen"
                             x-transition:enter="transition ease-in-out duration-300 transform"
                             x-transition:enter-start="-translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transition ease-in-out duration-300 transform"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="-translate-x-full"
                             class="w-screen max-w-xs transform bg-white p-6 shadow-2xl flex flex-col justify-between h-full overflow-y-auto text-left">
                            
                            <div class="space-y-6">
                                <!-- Drawer Header -->
                                <div class="flex items-center justify-between pb-4 border-b border-stone-100">
                                    <h3 class="serif-font text-lg font-bold text-brand-coal">Filter Produk</h3>
                                    <button @click="mobileFiltersOpen = false" class="text-stone-400 hover:text-stone-600 transition-colors p-1">
                                        <i data-lucide="x" class="w-5 h-5 pointer-events-none"></i>
                                    </button>
                                </div>

                                <!-- Cari Produk -->
                                <div>
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Cari Produk</h4>
                                    <div class="relative">
                                        <input type="text" x-model="searchQuery" class="w-full bg-stone-50 border border-stone-200 rounded-lg pl-3 pr-8 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Nama barang atau SKU...">
                                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i data-lucide="search" class="w-3.5 h-3.5 text-stone-400"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Kategori -->
                                <div class="border-t border-stone-100 pt-5">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2.5">Kategori</h4>
                                    <div class="space-y-1 max-h-48 overflow-y-auto pr-1">
                                        <button @click="selectedCategory = 'all'" class="w-full text-left text-xs py-1.5 px-3 rounded-lg font-semibold flex justify-between items-center transition-colors" :class="selectedCategory === 'all' ? 'bg-brand-darkGreen/10 text-brand-darkGreen' : 'text-stone-600 hover:bg-stone-50'">
                                            <span>Semua Kategori</span>
                                            <span class="text-[10px] text-stone-400 font-bold" x-text="items.length"></span>
                                        </button>
                                        <template x-for="cat in categories" :key="cat.id">
                                            <button @click="selectedCategory = cat.id" class="w-full text-left text-xs py-1.5 px-3 rounded-lg font-semibold flex justify-between items-center transition-colors" :class="selectedCategory == cat.id ? 'bg-brand-darkGreen/10 text-brand-darkGreen' : 'text-stone-600 hover:bg-stone-50'">
                                                <span x-text="cat.name"></span>
                                                <span class="text-[10px] text-stone-400 font-bold" x-text="items.filter(i => i.category_id == cat.id).length"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Rentang Harga -->
                                <div class="border-t border-stone-100 pt-5">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Rentang Harga (Rp)</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1">Min</label>
                                            <input type="number" x-model="minPrice" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Rp 0">
                                        </div>
                                        <div>
                                            <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1">Max</label>
                                            <input type="number" x-model="maxPrice" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Rp Max">
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Khusus -->
                                <div class="border-t border-stone-100 pt-5">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Filter Khusus</h4>
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 text-xs font-semibold text-stone-600 cursor-pointer">
                                            <input type="radio" name="mobileStatusFilter" value="all" x-model="statusFilter" class="accent-brand-darkGreen">
                                            <span>Semua Status</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-xs font-semibold text-stone-600 cursor-pointer">
                                            <input type="radio" name="mobileStatusFilter" value="ready" x-model="statusFilter" class="accent-brand-darkGreen">
                                            <span>Stok Aman</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-xs font-semibold text-stone-600 cursor-pointer">
                                            <input type="radio" name="mobileStatusFilter" value="promo" x-model="statusFilter" class="accent-brand-darkGreen">
                                            <span>Sedang Promo</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-xs font-semibold text-stone-600 cursor-pointer">
                                            <input type="radio" name="mobileStatusFilter" value="wholesale" x-model="statusFilter" class="accent-brand-darkGreen">
                                            <span>Harga Grosir</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Urutan -->
                                <div class="border-t border-stone-100 pt-5">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Urutan</h4>
                                    <select x-model="sortBy" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-2 text-xs font-semibold text-stone-700 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200">
                                        <option value="name_asc">Nama (A-Z)</option>
                                        <option value="price_asc">Harga Terendah</option>
                                        <option value="price_desc">Harga Tertinggi</option>
                                        <option value="stock_desc">Stok Terbanyak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-stone-100 flex gap-2">
                                <button @click="clearFilters(); mobileFiltersOpen = false;" class="flex-1 bg-stone-100 hover:bg-stone-200 text-stone-600 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-colors text-center">
                                    Reset
                                </button>
                                <button @click="mobileFiltersOpen = false" class="flex-1 bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-colors text-center shadow-sm">
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Left Sidebar Filters (Desktop) -->
                <aside class="hidden lg:block w-72 flex-shrink-0 bg-white border border-stone-200/60 rounded-2xl p-6 space-y-6 text-left shadow-sm">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-3.5">Cari Produk</h4>
                        <div class="relative">
                            <input type="text" x-model="searchQuery" class="w-full bg-stone-50 border border-stone-200 rounded-lg pl-3 pr-8 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Nama barang atau SKU...">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-3.5 h-3.5 text-stone-400"></i>
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-stone-100 pt-5">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-3.5">Kategori Produk</h4>
                        <div class="space-y-2">
                            <button @click="selectedCategory = 'all'" class="w-full text-left text-xs py-1.5 px-3 rounded-lg font-semibold flex justify-between items-center transition-colors" :class="selectedCategory === 'all' ? 'bg-brand-darkGreen/10 text-brand-darkGreen' : 'text-stone-600 hover:bg-stone-50'">
                                <span>Semua Kategori</span>
                                <span class="text-[10px] text-stone-400 font-bold" x-text="items.length"></span>
                            </button>
                            <template x-for="cat in categories" :key="cat.id">
                                <button @click="selectedCategory = cat.id" class="w-full text-left text-xs py-1.5 px-3 rounded-lg font-semibold flex justify-between items-center transition-colors" :class="selectedCategory == cat.id ? 'bg-brand-darkGreen/10 text-brand-darkGreen' : 'text-stone-600 hover:bg-stone-50'">
                                    <span x-text="cat.name"></span>
                                    <span class="text-[10px] text-stone-400 font-bold" x-text="items.filter(i => i.category_id == cat.id).length"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="border-t border-stone-100 pt-5">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-3.5">Filter Harga (Rupiah)</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1">Min</label>
                                <input type="number" x-model="minPrice" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Rp 0">
                            </div>
                            <div>
                                <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1">Max</label>
                                <input type="number" x-model="maxPrice" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Rp Max">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-stone-100 pt-5">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-3.5">Filter Khusus</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-xs font-semibold text-stone-600 cursor-pointer">
                                <input type="radio" name="statusFilter" value="all" x-model="statusFilter" class="accent-brand-darkGreen">
                                <span>Semua Status</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-stone-600 cursor-pointer">
                                <input type="radio" name="statusFilter" value="ready" x-model="statusFilter" class="accent-brand-darkGreen">
                                <span>Stok Aman</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-stone-600 cursor-pointer">
                                <input type="radio" name="statusFilter" value="promo" x-model="statusFilter" class="accent-brand-darkGreen">
                                <span>Sedang Promo</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-stone-600 cursor-pointer">
                                <input type="radio" name="statusFilter" value="wholesale" x-model="statusFilter" class="accent-brand-darkGreen">
                                <span>Harga Grosir</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-stone-100 pt-5">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-3">Urutan</h4>
                        <select x-model="sortBy" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-2 text-xs font-semibold text-stone-700 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200">
                            <option value="name_asc">Nama (A-Z)</option>
                            <option value="price_asc">Harga Terendah</option>
                            <option value="price_desc">Harga Tertinggi</option>
                            <option value="stock_desc">Stok Terbanyak</option>
                        </select>
                    </div>

                    <button @click="clearFilters()" class="w-full bg-stone-100 hover:bg-stone-200 text-stone-600 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors">
                        Reset Filter
                    </button>
                </aside>

                <!-- Right Main Grid: Catalog Viewport -->
                <div class="flex-grow w-full space-y-6">
                    <!-- Grid Header Info -->
                    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center bg-white border border-stone-200/60 p-4 rounded-xl shadow-sm px-6 gap-3">
                        <div class="flex items-center justify-between sm:justify-start gap-4">
                            <span class="text-xs font-semibold text-stone-500" x-text="filteredItems.length + ' item ditemukan di database registry'"></span>
                            <!-- Mobile Filter Trigger -->
                            <button @click="mobileFiltersOpen = true" class="lg:hidden flex items-center gap-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors">
                                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                                Filter Produk
                            </button>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-3 border-t sm:border-t-0 border-stone-100 pt-3 sm:pt-0">
                            <span class="text-xs font-bold text-stone-400 uppercase tracking-widest">Urutan Aktif:</span>
                            <span class="text-xs font-bold text-brand-darkGreen uppercase" x-text="sortBy.replace('_', ' ')"></span>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                        <template x-for="item in filteredItems" :key="item.id">
                            <div class="bg-white rounded-2xl p-7 flex flex-col justify-between border border-stone-200/60 hover:border-brand-gold/60 hover:shadow-xl hover:shadow-stone-200/20 transition-all duration-300 min-h-[420px] relative group text-left">
                                
                                <!-- Promo label floating -->
                                <template x-if="isPromoActive(item)">
                                    <span class="absolute top-4 left-4 bg-brand-clay/10 border border-brand-clay/20 text-brand-clay text-[9px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                        Promo
                                    </span>
                                </template>

                                <!-- FOMO Critical Stock Badge -->
                                <template x-if="!isPromoActive(item) && item.stok_barang <= item.min_stock && item.stok_barang > 0">
                                    <span class="absolute top-4 left-4 bg-rose-500/10 border border-rose-500/30 text-rose-600 text-[9px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1 animate-pulse">
                                        <span class="w-1.5 h-1.5 bg-rose-500 rounded-full inline-block"></span>
                                        Sisa <span x-text="item.stok_barang"></span> Pcs!
                                    </span>
                                </template>

                                <div>
                                    <!-- Header info -->
                                    <div class="flex justify-between items-center mb-3 pt-3">
                                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider" x-text="item.category ? item.category.name : 'Stok'"></span>
                                        <button @click="toggleWishlist(item)" class="text-stone-300 hover:text-rose-500 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                 viewBox="0 0 24 24" 
                                                 fill="none" 
                                                 stroke="currentColor" 
                                                 stroke-width="2" 
                                                 stroke-linecap="round" 
                                                 stroke-linejoin="round" 
                                                 class="w-4.5 h-4.5 transition-all duration-300"
                                                 :class="isWishlisted(item) ? 'fill-rose-500 text-rose-500' : ''">
                                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="serif-font text-base font-bold text-brand-coal leading-snug line-clamp-2 min-h-[2.5rem] group-hover:text-brand-darkGreen transition-colors cursor-pointer"
                                        @click="selectedItem = item; detailModalOpen = true;"
                                        x-text="item.nama_barang"></h3>
                                    
                                    <!-- Code -->
                                    <p class="text-[9px] font-mono text-stone-400 uppercase tracking-widest mt-1.5" x-text="'CODE // ' + item.kode_barang"></p>

                                    <!-- Stock bar -->
                                    <div class="mt-8 space-y-1.5">
                                        <div class="flex justify-between text-[9px] font-bold uppercase tracking-widest text-stone-400">
                                            <span x-text="item.stok_barang <= 0 ? 'Stok Habis' : (item.stok_barang <= item.min_stock ? 'Stok kritis' : 'Tersedia')"></span>
                                            <span x-text="item.stok_barang + ' ' + (item.unit ? item.unit.name : 'Pcs')"></span>
                                        </div>
                                        <div class="w-full bg-stone-100 h-1.5 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full" 
                                                 :style="'width: ' + (item.stok_barang <= 0 ? '0%' : Math.min((item.stok_barang / (item.min_stock * 3 || 30)) * 100, 100) + '%')"
                                                 :class="item.stok_barang <= 0 ? 'bg-stone-300' : (item.stok_barang <= item.min_stock ? 'bg-brand-clay animate-pulse' : 'bg-brand-darkGreen/60')"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom actions -->
                                <div class="pt-4 border-t border-stone-100 flex flex-col gap-3">
                                    <div class="flex items-baseline justify-between">
                                        <span class="text-[9px] font-bold text-stone-400 uppercase tracking-widest">Harga</span>
                                        <div class="text-right">
                                            <template x-if="isPromoActive(item)">
                                                <span class="text-[10px] text-stone-400 line-through font-bold block leading-none mb-0.5" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.selling_price)"></span>
                                            </template>
                                            <span class="serif-font text-base font-bold text-brand-darkGreen block leading-none" 
                                                  x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getEffectivePrice(item))"></span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 w-full mt-1">
                                        <button @click="selectedItem = item; detailModalOpen = true;" 
                                                class="flex-1 py-2.5 border border-stone-200 rounded-lg hover:bg-stone-50 hover:border-brand-darkGreen/40 hover:text-brand-darkGreen text-[10px] font-bold uppercase tracking-wider text-stone-500 transition-colors flex items-center justify-center gap-1.5">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                        </button>
                                        <button @click="addToCart(item)" :disabled="item.stok_barang <= 0"
                                                :class="item.stok_barang <= 0 ? 'bg-stone-200 text-stone-400 cursor-not-allowed hover:bg-stone-200' : 'bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white'"
                                                class="flex-1 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                            <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i> <span x-text="item.stok_barang <= 0 ? 'Habis' : 'Buy'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <div x-show="filteredItems.length === 0" class="col-span-full py-24 text-center bg-white border border-stone-200/60 rounded-2xl shadow-sm">
                            <i data-lucide="inbox" class="w-10 h-10 text-stone-300 mx-auto mb-4"></i>
                            <p class="text-xs font-bold uppercase tracking-wider text-stone-400">Tidak ada produk yang cocok dengan filter aktif</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Warehouse Performance Stats -->
            <section class="bg-white border border-stone-200/60 rounded-3xl p-8 lg:p-12 text-center space-y-8 shadow-sm">
                <div class="space-y-2">
                    <span class="text-[9px] font-bold text-brand-gold uppercase tracking-[0.25em]">Live Logistics Analytics</span>
                    <h3 class="serif-font text-3xl font-bold text-brand-darkGreen">Statistik & Kredibilitas Pengiriman</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <!-- Stat 1 -->
                    <div class="space-y-1">
                        <span class="text-4xl font-bold text-brand-coal block serif-font">99.8%</span>
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest block">Order Accuracy Rate</span>
                        <p class="text-xs text-stone-500 mt-2 px-4">Keselarasan stok fisik dengan data database terjaga ketat.</p>
                    </div>
                    <!-- Stat 2 -->
                    <div class="space-y-1">
                        <span class="text-4xl font-bold text-brand-coal block serif-font">2 Detik</span>
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest block">WA Receipt Speed</span>
                        <p class="text-xs text-stone-500 mt-2 px-4">Kecepatan transmisi struk digital langsung ke WhatsApp Anda.</p>
                    </div>
                    <!-- Stat 3 -->
                    <div class="space-y-1">
                        <span class="text-4xl font-bold text-brand-coal block serif-font">12,000+</span>
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest block">Synced Stock Units</span>
                        <p class="text-xs text-stone-500 mt-2 px-4">Lebih dari 12 ribu SKU terhubung secara realtime lintas cabang.</p>
                    </div>
                </div>
            </section>

            <!-- Testimonials Grid -->
            <section class="space-y-8 text-left">
                <div class="text-center space-y-2">
                    <span class="text-[9px] font-bold text-brand-gold uppercase tracking-[0.25em]">What Customers Say</span>
                    <h3 class="serif-font text-3xl font-bold text-brand-coal">Kepuasan Pembeli & Client</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Testimonial 1 -->
                    <div class="bg-white border border-stone-200/60 p-6 rounded-2xl space-y-4 shadow-sm">
                        <div class="flex items-center gap-1 text-brand-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed font-medium">
                            &ldquo;Proses belanja sangat cepat! Setelah saya klik beli, tidak sampai 3 detik langsung ada notifikasi WhatsApp masuk menyertakan detail invoice dan struk belanja.&rdquo;
                        </p>
                        <div>
                            <h5 class="text-xs font-bold text-brand-coal">Budi Santoso</h5>
                            <span class="text-[9px] text-stone-400 uppercase">Retailer Elektronik</span>
                        </div>
                    </div>
                    <!-- Testimonial 2 -->
                    <div class="bg-white border border-stone-200/60 p-6 rounded-2xl space-y-4 shadow-sm">
                        <div class="flex items-center gap-1 text-brand-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed font-medium">
                            &ldquo;Integrasi stoknya luar biasa. Saya iseng ngecek dashboard admin kantor sebelah, ternyata stoknya langsung berkurang secara realtime pas saya checkout. Keren!&rdquo;
                        </p>
                        <div>
                            <h5 class="text-xs font-bold text-brand-coal">Siti Rahma</h5>
                            <span class="text-[9px] text-stone-400 uppercase">Office Procurement</span>
                        </div>
                    </div>
                    <!-- Testimonial 3 -->
                    <div class="bg-white border border-stone-200/60 p-6 rounded-2xl space-y-4 shadow-sm">
                        <div class="flex items-center gap-1 text-brand-gold">
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                            <i data-lucide="star" class="w-4 h-4 fill-brand-gold"></i>
                        </div>
                        <p class="text-xs text-stone-600 leading-relaxed font-medium">
                            &ldquo;UI barunya sangat memanjakan mata, keranjang belanja dan status checkout di HP responsif sekali. Pengiriman barang cepat dan struk pembayaran di WA rapi.&rdquo;
                        </p>
                        <div>
                            <h5 class="text-xs font-bold text-brand-coal">Ahmad Yani</h5>
                            <span class="text-[9px] text-stone-400 uppercase">Pelanggan Grosir</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Social Media Lifestyle Grid -->
            <section class="space-y-6 text-left">
                <div class="text-center space-y-2">
                    <span class="text-[9px] font-bold text-brand-gold uppercase tracking-[0.25em]">#StockMasterLiving</span>
                    <h3 class="serif-font text-3xl font-bold text-brand-coal">Registry di Media Sosial</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Photo item 1 -->
                    <div class="border border-stone-200 rounded-2xl aspect-square flex flex-col justify-between p-5 relative overflow-hidden group shadow-xs" style="background-image: url('{{ asset('assets/images/office_setup.png') }}'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 via-transparent to-stone-900/20 group-hover:from-stone-900/70 transition-all duration-500 z-0"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-white/90 relative z-10 drop-shadow-sm"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        <span class="text-[10px] font-bold text-white relative z-10 tracking-wider uppercase drop-shadow-sm">Modern Office Setup</span>
                    </div>
                    <!-- Photo item 2 -->
                    <div class="border border-stone-200 rounded-2xl aspect-square flex flex-col justify-between p-5 relative overflow-hidden group shadow-xs" style="background-image: url('{{ asset('assets/images/boutique_furnishing.png') }}'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 via-transparent to-stone-900/20 group-hover:from-stone-900/70 transition-all duration-500 z-0"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-white/90 relative z-10 drop-shadow-sm"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        <span class="text-[10px] font-bold text-white relative z-10 tracking-wider uppercase drop-shadow-sm">Boutique Furnishing</span>
                    </div>
                    <!-- Photo item 3 -->
                    <div class="border border-stone-200 rounded-2xl aspect-square flex flex-col justify-between p-5 relative overflow-hidden group shadow-xs" style="background-image: url('{{ asset('assets/images/smart_logistics.png') }}'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 via-transparent to-stone-900/20 group-hover:from-stone-900/70 transition-all duration-500 z-0"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-white/90 relative z-10 drop-shadow-sm"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        <span class="text-[10px] font-bold text-white relative z-10 tracking-wider uppercase drop-shadow-sm">Smart Logistics</span>
                    </div>
                    <!-- Photo item 4 -->
                    <div class="border border-stone-200 rounded-2xl aspect-square flex flex-col justify-between p-5 relative overflow-hidden group shadow-xs" style="background-image: url('{{ asset('assets/images/eco_friendly_packs.png') }}'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 via-transparent to-stone-900/20 group-hover:from-stone-900/70 transition-all duration-500 z-0"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-white/90 relative z-10 drop-shadow-sm"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        <span class="text-[10px] font-bold text-white relative z-10 tracking-wider uppercase drop-shadow-sm">Eco-Friendly Packs</span>
                    </div>
                </div>
            </section>
        </div>

        <!-- ==================== PAGE 2: ACCOUNT PROFILE & ORDERS ==================== -->
        <div x-show="activeTab === 'profile'" class="space-y-8 text-left" x-cloak>
            <div class="flex items-center gap-4 border-b border-stone-200/60 pb-5">
                <i data-lucide="user" class="w-7 h-7 text-brand-darkGreen"></i>
                <h2 class="serif-font text-3xl font-bold text-brand-darkGreen">Portal Akun & Riwayat Pembelian</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Personal Info Settings -->
                <div class="bg-white border border-stone-200/60 rounded-2xl p-6 space-y-6 shadow-sm">
                    <h3 class="serif-font text-xl font-bold text-brand-coal">Detail Informasi Pelanggan</h3>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        Data ini akan otomatis terisi ke form checkout saat Anda membeli barang dagangan di StockStore untuk kenyamanan Anda.
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                            <input type="text" x-model="customerName" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Nama Lengkap"/>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Nomor WhatsApp</label>
                            <input type="text" x-model="customerPhone" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Contoh: 08123456789"/>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Alamat Pengiriman Default</label>
                            <textarea rows="3" x-model="customerAddress" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Alamat lengkap"></textarea>
                        </div>
                        <button @click="saveProfile()" class="w-full bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>

                <!-- Order History Tracker -->
                <div class="lg:col-span-2 bg-white border border-stone-200/60 rounded-2xl p-6 space-y-6 shadow-sm">
                    <h3 class="serif-font text-xl font-bold text-brand-coal">Riwayat Transaksi Terakhir</h3>
                    
                    <div class="space-y-4">
                        <template x-if="orders.length === 0">
                            <div class="text-center py-20 bg-stone-50 rounded-xl border border-stone-200/40">
                                <i data-lucide="shopping-bag" class="w-10 h-10 text-stone-300 mx-auto mb-3"></i>
                                <p class="text-xs font-bold text-stone-400 uppercase tracking-widest">Belum ada pesanan terdaftar di perangkat ini</p>
                            </div>
                        </template>

                        <template x-for="ord in orders" :key="ord.id">
                            <div class="border border-stone-200 rounded-xl p-5 space-y-3 hover:shadow-md transition-shadow">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-stone-100 pb-3 gap-2">
                                    <div>
                                        <span class="text-xs font-black text-brand-darkGreen" x-text="ord.id"></span>
                                        <span class="text-[10px] text-stone-400 mx-2">•</span>
                                        <span class="text-xs text-stone-500 font-semibold" x-text="ord.date"></span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/50 uppercase" x-text="ord.status"></span>
                                </div>

                                <div class="space-y-1.5">
                                    <template x-for="item in ord.items">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-stone-600 font-medium" x-text="item.name + ' (x' + item.qty + ')'"></span>
                                            <span class="font-bold text-brand-coal" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.price * item.qty)"></span>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex justify-between items-center border-t border-stone-100 pt-3 text-xs">
                                    <span class="text-stone-400 uppercase tracking-wider text-[9px]">Pembayaran via: <strong class="text-stone-600" x-text="ord.payment.toUpperCase().replace('_', ' ')"></strong></span>
                                    <span class="font-bold text-sm text-brand-darkGreen">Total: Rp <span x-text="new Intl.NumberFormat('id-ID').format(ord.total)"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== PAGE 3: HELP & FAQS ==================== -->
        <div x-show="activeTab === 'faq'" class="space-y-8 text-left max-w-4xl mx-auto" x-cloak>
            <div class="flex items-center gap-4 border-b border-stone-200/60 pb-5">
                <i data-lucide="help-circle" class="w-7 h-7 text-brand-darkGreen"></i>
                <h2 class="serif-font text-3xl font-bold text-brand-darkGreen">Help & FAQs Center</h2>
            </div>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                    <button @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full px-6 py-4 flex justify-between items-center text-left text-sm font-bold text-brand-coal hover:bg-stone-50">
                        <span>Bagaimana sistem WhatsApp notifikasi ini bekerja?</span>
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             stroke-linecap="round" 
                             stroke-linejoin="round" 
                             class="w-4 h-4 text-stone-400 transition-transform duration-300"
                             :class="openFaq === 1 ? 'rotate-180' : ''">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 1" class="px-6 pb-4 pt-1 border-t border-stone-100 text-xs text-stone-600 leading-relaxed space-y-2">
                        <p>
                            Saat Anda melengkapi checkout pembelian di toko online kami, server kami akan langsung memproses pengurangan stok barang di database server utama secara realtime.
                        </p>
                        <p>
                            Setelah itu, sistem akan melakukan request API ke WhatsApp Bot kita untuk mengirim pesan berisi struk digital (Digital Receipt) yang mencantumkan nama, detail item, total, alamat, serta detail rekening pembayaran. Anda akan menerima notifikasi tersebut di nomor WhatsApp yang didaftarkan.
                        </p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                    <button @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full px-6 py-4 flex justify-between items-center text-left text-sm font-bold text-brand-coal hover:bg-stone-50">
                        <span>Apakah stok barang langsung tersinkronisasi di dashboard StockMaster?</span>
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             stroke-linecap="round" 
                             stroke-linejoin="round" 
                             class="w-4 h-4 text-stone-400 transition-transform duration-300"
                             :class="openFaq === 2 ? 'rotate-180' : ''">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 2" class="px-6 pb-4 pt-1 border-t border-stone-100 text-xs text-stone-600 leading-relaxed">
                        Ya, pemotongan stok dilakukan di dalam database transaction Laravel yang terisolasi. Jika terjadi kendala pada server utama, transaksi akan di-rollback secara otomatis untuk menghindari kesalahan selisih stok. Grafik analitik di dashboard utama admin akan langsung berdenyut dan terupdate secara real-time.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                    <button @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full px-6 py-4 flex justify-between items-center text-left text-sm font-bold text-brand-coal hover:bg-stone-50">
                        <span>Metode pembayaran apa saja yang didukung?</span>
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             stroke-linecap="round" 
                             stroke-linejoin="round" 
                             class="w-4 h-4 text-stone-400 transition-transform duration-300"
                             :class="openFaq === 3 ? 'rotate-180' : ''">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 3" class="px-6 pb-4 pt-1 border-t border-stone-100 text-xs text-stone-600 leading-relaxed">
                        Kami mendukung 3 metode pembayaran utama: Transfer Bank (BCA, Mandiri), Cash on Delivery (COD) langsung di tempat pengiriman, serta integrasi dompet digital E-Wallet (Dana, OVO, GoPay). Rekening tujuan transfer akan disertakan lengkap di struk WhatsApp.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                    <button @click="openFaq = (openFaq === 4 ? null : 4)" class="w-full px-6 py-4 flex justify-between items-center text-left text-sm font-bold text-brand-coal hover:bg-stone-50">
                        <span>Bagaimana kebijakan pengembalian barang atau komplain?</span>
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             stroke-linecap="round" 
                             stroke-linejoin="round" 
                             class="w-4 h-4 text-stone-400 transition-transform duration-300"
                             :class="openFaq === 4 ? 'rotate-180' : ''">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === 4" class="px-6 pb-4 pt-1 border-t border-stone-100 text-xs text-stone-600 leading-relaxed">
                        Jika Anda menerima barang yang salah atau rusak, Anda dapat menghubungi customer service WhatsApp kami dalam waktu 2x24 jam sejak barang diterima. Harap lampirkan foto struk digital dan video unboxing produk demi kelancaran proses verifikasi klaim.
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== PAGE 4: CONTACT US ==================== -->
        <div x-show="activeTab === 'contact'" class="space-y-8 text-left max-w-5xl mx-auto" x-cloak>
            <div class="flex items-center gap-4 border-b border-stone-200/60 pb-5">
                <i data-lucide="mail" class="w-7 h-7 text-brand-darkGreen"></i>
                <h2 class="serif-font text-3xl font-bold text-brand-darkGreen">Contact Customer Support</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                
                <!-- Contact info cards -->
                <div class="space-y-6">
                    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="serif-font text-lg font-bold text-brand-coal">Kantor Utama & Warehouse</h3>
                        <div class="flex items-start gap-3.5 text-xs text-stone-600">
                            <i data-lucide="map-pin" class="w-5 h-5 text-brand-darkGreen flex-shrink-0"></i>
                            <div>
                                <p class="font-bold text-brand-coal">StockMaster Logistik Regional</p>
                                <p>Jl. Jenderal Sudirman Kav. 21, Blok M-3</p>
                                <p>Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12190</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="serif-font text-lg font-bold text-brand-coal">Hubungi Kami Secara Langsung</h3>
                        
                        <div class="space-y-3.5 text-xs text-stone-600">
                            <div class="flex items-center gap-3.5">
                                <i data-lucide="phone" class="w-4 h-4 text-brand-darkGreen"></i>
                                <span>Support Call: +62 21 8907 2321</span>
                            </div>
                            <div class="flex items-center gap-3.5">
                                <i data-lucide="message-square" class="w-4 h-4 text-brand-darkGreen"></i>
                                <span>WhatsApp Node: +62 812-3456-789</span>
                            </div>
                            <div class="flex items-center gap-3.5">
                                <i data-lucide="clock" class="w-4 h-4 text-brand-darkGreen"></i>
                                <span>Senin - Sabtu (08:00 - 17:00 WIB)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="serif-font text-lg font-bold text-brand-coal">Kirim Pertanyaan / Feedback</h3>
                    
                    <div x-show="contactSubmitted" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold">
                        Pesan Anda berhasil terkirim ke customer registry! Kami akan segera menghubungi Anda kembali.
                    </div>

                    <form x-show="!contactSubmitted" @submit.prevent="contactSubmitted = true; contactName=''; contactEmail=''; contactMessage='';" class="space-y-4 text-xs">
                        <div>
                            <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                            <input required x-model="contactName" type="text" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Nama Lengkap"/>
                        </div>
                        <div>
                            <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Alamat Email</label>
                            <input required x-model="contactEmail" type="email" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="email@domain.com"/>
                        </div>
                        <div>
                            <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Pesan Anda</label>
                            <textarea required x-model="contactMessage" rows="4" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal" placeholder="Tuliskan keluhan atau pertanyaan Anda di sini..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white py-3 rounded-lg font-bold text-xs uppercase tracking-wider">
                            Kirim Pesan Dukungan
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </main>

    <!-- Product Quick View Modal (Minimal Luxury) -->
    <div x-cloak x-show="detailModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-brand-coal/40 backdrop-blur-sm" @click.self="detailModalOpen = false">
        <div x-show="detailModalOpen" x-transition class="bg-white rounded-3xl max-w-2xl w-full p-8 md:p-10 relative border border-stone-200 text-left space-y-6 max-h-[90vh] overflow-y-auto shadow-2xl">
            <!-- Close -->
            <button @click="detailModalOpen = false" class="absolute top-6 right-6 p-2 rounded-lg hover:bg-stone-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-stone-500 pointer-events-none"></i>
            </button>

            <template x-if="selectedItem">
                <div class="space-y-6">
                    <div class="flex flex-col gap-1 border-b border-stone-200 pb-5">
                        <span class="text-[9px] font-bold uppercase tracking-[0.25em] text-brand-gold" x-text="selectedItem.category ? selectedItem.category.name : 'Catalog'"></span>
                        <h3 class="serif-font text-3xl font-bold text-brand-darkGreen" x-text="selectedItem.nama_barang"></h3>
                        <p class="text-[10px] font-mono text-stone-400 uppercase tracking-widest mt-1" x-text="'REGISTRY SKU CODE // ' + selectedItem.kode_barang"></p>
                    </div>

                    <!-- Statistics grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-stone-50 rounded-xl border border-stone-200/50">
                            <span class="text-[9px] font-bold text-stone-400 uppercase tracking-widest block mb-1">Stok Tersedia</span>
                            <span class="text-base font-bold text-brand-darkGreen" x-text="selectedItem.stok_barang + ' ' + (selectedItem.unit ? selectedItem.unit.name : 'Pcs')"></span>
                        </div>
                        <div class="p-4 bg-stone-50 rounded-xl border border-stone-200/50">
                            <span class="text-[9px] font-bold text-stone-400 uppercase tracking-widest block mb-1">Batas Minimum</span>
                            <span class="text-base font-bold text-brand-clay" x-text="selectedItem.min_stock + ' ' + (selectedItem.unit ? selectedItem.unit.name : 'Pcs')"></span>
                        </div>
                        <div class="p-4 bg-stone-50 rounded-xl border border-stone-200/50">
                            <span class="text-[9px] font-bold text-stone-400 uppercase tracking-widest block mb-1">Jenis Aset</span>
                            <span class="text-base font-bold text-stone-700" x-text="selectedItem.is_asset ? 'Aset Kantor' : 'Retail Dagangan'"></span>
                        </div>
                        <div class="p-4 bg-stone-50 rounded-xl border border-stone-200/50">
                            <span class="text-[9px] font-bold text-stone-400 uppercase tracking-widest block mb-1">Wholesale</span>
                            <span class="text-base font-bold text-stone-700" x-text="selectedItem.wholesale_price ? 'Tersedia' : 'Tidak'"></span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <h4 class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Deskripsi Barang</h4>
                        <p class="text-xs text-stone-600 leading-relaxed bg-stone-50 p-5 rounded-xl border border-stone-200/40" 
                           x-text="'Produk ' + selectedItem.nama_barang + ' terdaftar secara resmi di StockMaster dengan kode SKU ' + selectedItem.kode_barang + '. Merupakan barang bertipe ' + (selectedItem.is_asset ? 'Asset Inventaris Perusahaan' : 'Barang Dagangan Retail') + ' yang didukung dengan satuan ' + (selectedItem.unit ? selectedItem.unit.name : 'Pcs') + ' untuk pergerakan logistik maksimal.'"></p>
                    </div>

                    <!-- Purchasing info bar -->
                    <div class="p-6 bg-stone-50 rounded-2xl border border-stone-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-[9px] font-bold text-stone-400 uppercase tracking-widest block">Price Value</span>
                            <div class="flex items-baseline gap-2">
                                <span class="serif-font text-2xl font-bold text-brand-darkGreen" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(getEffectivePrice(selectedItem))"></span>
                                <template x-if="isPromoActive(selectedItem)">
                                    <span class="text-xs text-stone-400 line-through font-bold" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedItem.selling_price)"></span>
                                </template>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button @click="toggleWishlist(selectedItem)" class="p-3 border border-stone-200 rounded-xl hover:bg-stone-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                     viewBox="0 0 24 24" 
                                     fill="none" 
                                     stroke="currentColor" 
                                     stroke-width="2" 
                                     stroke-linecap="round" 
                                     stroke-linejoin="round" 
                                     class="w-5 h-5 transition-all duration-300"
                                     :class="isWishlisted(selectedItem) ? 'fill-rose-500 text-rose-500 stroke-rose-500' : 'text-stone-500 stroke-stone-500'">
                                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                                </svg>
                            </button>
                            <button @click="addToCart(selectedItem); detailModalOpen = false;" :disabled="selectedItem.stok_barang <= 0"
                                    :class="selectedItem.stok_barang <= 0 ? 'bg-stone-200 text-stone-400 cursor-not-allowed hover:bg-stone-200' : 'bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white'"
                                    class="px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all">
                                <span x-text="selectedItem.stok_barang <= 0 ? 'Stok Habis' : 'Add to Cart'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Sliding Sidebar: Wishlist -->
    <div x-cloak x-show="wishlistOpen" class="fixed inset-0 z-50 overflow-hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="wishlistOpen" x-transition class="absolute inset-0 bg-brand-coal/40 backdrop-blur-sm transition-opacity" @click="wishlistOpen = false"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="wishlistOpen" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-md">
                    <div class="flex h-full flex-col bg-white shadow-2xl border-l border-stone-200 py-6 px-6 text-left">
                        
                        <div class="flex items-center justify-between border-b border-stone-100 pb-5">
                            <h2 class="serif-font text-xl font-bold flex items-center gap-3 text-brand-darkGreen">
                                <i data-lucide="heart" class="w-5 h-5 text-rose-500"></i> Wishlist
                            </h2>
                            <button @click="wishlistOpen = false" class="p-1 rounded-lg hover:bg-stone-100 transition-colors">
                                <i data-lucide="x" class="w-5 h-5 text-stone-500 pointer-events-none"></i>
                            </button>
                        </div>

                        <div class="flex-1 py-5 overflow-y-auto space-y-4">
                            <template x-if="wishlist.length === 0">
                                <div class="text-center py-20">
                                    <p class="text-xs font-bold text-stone-400 uppercase tracking-widest">Wishlist is empty</p>
                                </div>
                            </template>

                            <template x-for="(wItem, index) in wishlist" :key="wItem.id">
                                <div class="flex items-center justify-between p-4 bg-stone-50 rounded-xl border border-stone-200/50">
                                    <div class="flex-grow pr-3">
                                        <h4 class="text-xs font-bold text-brand-coal truncate" x-text="wItem.nama_barang"></h4>
                                        <p class="text-[10px] text-brand-darkGreen font-bold mt-0.5" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(wItem.harga)"></p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button @click="addToCart(wItem)" :disabled="wItem.stok_barang <= 0"
                                                 :class="wItem.stok_barang <= 0 ? 'bg-stone-200 text-stone-400 cursor-not-allowed hover:bg-stone-200' : 'bg-brand-darkGreen text-white hover:opacity-90'"
                                                 class="p-2 rounded-lg">
                                             <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                                         </button>
                                        <button @click="toggleWishlist(wItem)" class="text-rose-500 p-2">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sliding Sidebar: Shopping Cart -->
    <div x-cloak x-show="cartOpen" class="fixed inset-0 z-50 overflow-hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="cartOpen" x-transition class="absolute inset-0 bg-brand-coal/40 backdrop-blur-sm transition-opacity" @click="cartOpen = false"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="cartOpen" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-md">
                    <div class="flex h-full flex-col bg-white shadow-2xl border-l border-stone-200 py-6 px-6 text-left">
                        
                        <div class="flex items-center justify-between border-b border-stone-100 pb-5">
                            <h2 class="serif-font text-xl font-bold flex items-center gap-3 text-brand-darkGreen">
                                <i data-lucide="shopping-bag" class="w-5 h-5 text-brand-darkGreen"></i> Shopping Cart
                            </h2>
                            <button @click="cartOpen = false" class="p-1 rounded-lg hover:bg-stone-100 transition-colors">
                                <i data-lucide="x" class="w-5 h-5 text-stone-500 pointer-events-none"></i>
                            </button>
                        </div>

                        <!-- Cart items -->
                        <div class="flex-1 py-5 overflow-y-auto space-y-4">
                            <template x-if="cart.length === 0">
                                <div class="text-center py-20">
                                    <p class="text-xs font-bold text-stone-400 uppercase tracking-widest">Cart is empty</p>
                                </div>
                            </template>

                            <template x-for="(item, index) in cart" :key="item.id">
                                <div class="flex items-center justify-between p-4 bg-stone-50 rounded-xl border border-stone-200/50">
                                    <div class="flex-1 pr-3">
                                        <h4 class="text-xs font-bold text-brand-coal truncate" x-text="item.nama_barang"></h4>
                                        <p class="text-[10px] text-stone-500 font-bold mt-1" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.harga) + ' / ' + item.unit"></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="updateQuantity(index, -1)" class="w-6 h-6 bg-stone-200 text-stone-700 rounded flex items-center justify-center font-bold text-xs">-</button>
                                        <span class="font-bold text-xs text-stone-800" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(index, 1)" class="w-6 h-6 bg-stone-200 text-stone-700 rounded flex items-center justify-center font-bold text-xs">+</button>
                                        <button @click="removeFromCart(index)" class="text-rose-500 p-1.5 ml-1">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- checkout checkout details -->
                        <div x-show="cart.length > 0" class="border-t border-stone-100 pt-5 space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-xs text-stone-500 font-semibold">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-stone-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(cartSubtotal)"></span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-stone-500 font-semibold">
                                    <span>PPN (11%)</span>
                                    <span class="font-bold text-stone-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(cartTax)"></span>
                                </div>
                                <div class="flex justify-between items-center border-t border-stone-100 pt-3">
                                    <span class="text-xs font-bold text-stone-500">Order Total</span>
                                    <span class="font-bold text-xl text-brand-darkGreen" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(cartTotal)"></span>
                                </div>
                            </div>

                            <!-- Minimal checkout form -->
                            <form @submit.prevent="submitCheckout" class="space-y-3.5 pt-4 border-t border-stone-100">
                                <div>
                                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                                    <input required x-model="customerName" type="text" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal font-medium" placeholder="Full name"/>
                                </div>

                                <div>
                                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Nomor WhatsApp</label>
                                    <input required x-model="customerPhone" type="text" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal font-medium" placeholder="e.g. 08123456789"/>
                                </div>

                                <div>
                                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Alamat Lengkap</label>
                                    <textarea required x-model="customerAddress" rows="2" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal font-medium" placeholder="Shipping address"></textarea>
                                </div>

                                <div>
                                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Payment Method</label>
                                    <select x-model="paymentMethod" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all duration-200 text-brand-coal font-medium">
                                        <option value="bank_transfer">Transfer Bank (BCA/Mandiri)</option>
                                        <option value="cod">Cash on Delivery (COD)</option>
                                        <option value="e_wallet">E-Wallet (Dana/OVO/GoPay)</option>
                                    </select>
                                </div>

                                <div x-show="checkoutError" class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-lg text-[10px] font-bold" x-text="checkoutError"></div>

                                <button type="submit" :disabled="checkoutLoading" class="w-full bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white py-3.5 rounded-lg font-bold text-xs uppercase tracking-wider transition-all">
                                    <span x-show="!checkoutLoading">Confirm & Purchase</span>
                                    <span x-show="checkoutLoading" class="animate-pulse">Placing order...</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal Popup -->
    <div x-cloak x-show="checkoutSuccess" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-brand-coal/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full text-center space-y-5 border border-stone-200 shadow-2xl">
            <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-emerald-600">
                <i data-lucide="check" class="w-6 h-6"></i>
            </div>
            
            <div class="space-y-2">
                <h3 class="serif-font text-2xl font-bold text-brand-darkGreen">Checkout Successful</h3>
                <p class="text-xs text-stone-500 leading-relaxed">
                    Pesanan Anda sebesar <strong class="text-brand-darkGreen font-bold" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(orderTotal)"></strong> telah terkonfirmasi. Sinkronisasi persediaan berjalan dengan sukses dan digital receipt terkirim via WhatsApp.
                </p>
            </div>

            <button @click="checkoutSuccess = false; cartOpen = false; activeTab = 'profile';" class="w-full bg-brand-darkGreen text-white py-3 rounded-lg font-bold text-xs uppercase tracking-wider">
                Lihat Riwayat Transaksi
            </button>
        </div>
    </div>

    <!-- Authentication Modal (Login / Register) -->
    <div x-cloak x-show="authModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-brand-coal/40 backdrop-blur-sm" @click.self="authModalOpen = false">
        <div x-show="authModalOpen" x-transition class="bg-white rounded-3xl max-w-md w-full p-8 md:p-10 relative border border-stone-200 text-left space-y-6 shadow-2xl">
            <!-- Close Button -->
            <button @click="authModalOpen = false" class="absolute top-6 right-6 p-2 rounded-lg hover:bg-stone-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-stone-500 pointer-events-none"></i>
            </button>

            <!-- Tabs -->
            <div class="flex border-b border-stone-100">
                <button @click="authTab = 'login'; authError = ''" 
                        class="flex-1 pb-3 text-sm font-bold uppercase tracking-wider transition-colors border-b-2 text-center" 
                        :class="authTab === 'login' ? 'text-brand-darkGreen border-brand-darkGreen' : 'text-stone-400 border-transparent'">
                    Log In
                </button>
                <button @click="authTab = 'register'; authError = ''" 
                        class="flex-1 pb-3 text-sm font-bold uppercase tracking-wider transition-colors border-b-2 text-center" 
                        :class="authTab === 'register' ? 'text-brand-darkGreen border-brand-darkGreen' : 'text-stone-400 border-transparent'">
                    Register
                </button>
            </div>

            <!-- Error Banner -->
            <div x-show="authError" x-transition class="p-3.5 bg-rose-50 border border-rose-100 text-rose-600 rounded-xl text-xs font-semibold leading-relaxed" x-text="authError"></div>

            <!-- Login Form -->
            <form x-show="authTab === 'login'" @submit.prevent="submitLogin" class="space-y-4">
                <div>
                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </span>
                        <input required x-model="authEmail" type="email" placeholder="name@company.com" 
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-11 pr-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all text-brand-coal font-medium" />
                    </div>
                </div>

                <div>
                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input required x-model="authPassword" type="password" placeholder="••••••••" 
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-11 pr-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all text-brand-coal font-medium" />
                    </div>
                </div>

                <button type="submit" :disabled="authLoading" 
                        class="w-full bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all mt-2 shadow-md">
                    <span x-show="!authLoading">Sign In</span>
                    <span x-show="authLoading" class="animate-pulse">Authenticating...</span>
                </button>

                <p class="text-center text-[10px] text-stone-500 font-medium pt-2">
                    Belum punya akun? <a href="#" @click.prevent="authTab = 'register'; authError = ''" class="text-brand-darkGreen font-bold hover:underline">Daftar Sekarang</a>
                </p>
            </form>

            <!-- Register Form -->
            <form x-show="authTab === 'register'" @submit.prevent="submitRegister" class="space-y-4">
                <div>
                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input required x-model="authName" type="text" placeholder="Azzam Jawas" 
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-11 pr-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all text-brand-coal font-medium" />
                    </div>
                </div>

                <div>
                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </span>
                        <input required x-model="authEmail" type="email" placeholder="name@company.com" 
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-11 pr-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all text-brand-coal font-medium" />
                    </div>
                </div>

                <div>
                    <label class="block text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input required x-model="authPassword" type="password" placeholder="Min. 6 characters" 
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-11 pr-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-brand-darkGreen focus:bg-white transition-all text-brand-coal font-medium" />
                    </div>
                </div>

                <button type="submit" :disabled="authLoading" 
                        class="w-full bg-brand-darkGreen hover:bg-brand-darkGreen/90 text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all mt-2 shadow-md">
                    <span x-show="!authLoading">Create Account</span>
                    <span x-show="authLoading" class="animate-pulse">Creating account...</span>
                </button>

                <p class="text-center text-[10px] text-stone-500 font-medium pt-2">
                    Sudah punya akun? <a href="#" @click.prevent="authTab = 'login'; authError = ''" class="text-brand-darkGreen font-bold hover:underline">Masuk Di Sini</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Mega Footer (Full-Width, Multi-Column) -->
    <footer class="bg-brand-darkGreen text-stone-200 border-t border-white/10 pt-16 pb-8 px-8 md:px-12 w-full mt-auto">
        <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 text-left pb-12 border-b border-white/10">
            
            <!-- Col 1: About & Warehouse -->
            <div class="space-y-4">
                <span class="serif-font text-2xl font-bold tracking-tight text-white">StockStore.</span>
                <p class="text-xs text-stone-300 leading-relaxed font-medium">
                    Sistem pemesanan logistik e-commerce terintegrasi WhatsApp & live-sync database dengan server StockMaster. Mengedepankan efisiensi, akurasi, dan transparansi distribusi.
                </p>
                <div class="text-xs text-stone-400 space-y-1">
                    <p class="font-bold text-stone-200">Headquarters Warehouse</p>
                    <p>Jl. Jenderal Sudirman Kav. 21, Blok M-3, Jakarta Selatan</p>
                </div>
            </div>

            <!-- Col 2: Navigation Categories -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-widest text-white">Halaman Utama</h4>
                <ul class="space-y-2 text-xs text-stone-300 font-semibold">
                    <li><button @click="activeTab = 'catalog'; window.scrollTo(0,0)" class="hover:text-white transition-colors">Catalog / Shop</button></li>
                    <li><button @click="activeTab = 'profile'; window.scrollTo(0,0)" class="hover:text-white transition-colors">My Profile & Orders</button></li>
                    <li><button @click="activeTab = 'faq'; window.scrollTo(0,0)" class="hover:text-white transition-colors">Help & FAQ Center</button></li>
                    <li><button @click="activeTab = 'contact'; window.scrollTo(0,0)" class="hover:text-white transition-colors">Contact Customer Support</button></li>
                </ul>
            </div>

            <!-- Col 3: Customer Service Policies -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-widest text-white">Kebijakan Toko</h4>
                <ul class="space-y-2 text-xs text-stone-300 font-semibold">
                    <li><a href="#" class="hover:text-white transition-colors">Shipping & Delivery Policies</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Returns & Refund Registry</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Privacy Policy Protocol</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Terms of Inventory Service</a></li>
                </ul>
            </div>

            <!-- Col 4: Newsletter Signup -->
            <div class="space-y-4" x-data="{ newsletterSubmitted: false }">
                <h4 class="text-xs font-bold uppercase tracking-widest text-white">Ikuti Newsletter Kami</h4>
                <p class="text-xs text-stone-300 leading-relaxed font-medium">
                    Dapatkan update terbaru mengenai rilis barang grosir baru dan promo musiman langsung di email Anda.
                </p>

                <div x-show="newsletterSubmitted" class="p-3 bg-white/10 border border-white/20 rounded-lg text-[10px] text-white font-bold">
                    Terima kasih telah bergabung!
                </div>

                <form x-show="!newsletterSubmitted" @submit.prevent="newsletterSubmitted = true" class="flex gap-2">
                    <input required type="email" class="bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-white text-white placeholder-stone-400 flex-grow" placeholder="email@domain.com"/>
                    <button type="submit" class="bg-white text-brand-darkGreen hover:bg-stone-100 px-4 rounded-lg text-xs font-bold transition-all">
                        Join
                    </button>
                </form>
            </div>

        </div>

        <!-- Bottom Copyright & Payment Methods -->
        <div class="w-full pt-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-[10px] text-stone-400 uppercase tracking-widest">
                © 2026 E-Commerce Integration Protocol • Powered by StockMaster Zenith Core.
            </p>
            <!-- Payment Methods Mock Icons -->
            <div class="flex items-center gap-3 opacity-60">
                <span class="text-[9px] font-bold text-stone-400 uppercase tracking-widest mr-2">Metode Pembayaran:</span>
                <span class="px-2 py-1 bg-white/5 border border-white/15 rounded text-[8px] font-black tracking-widest text-white">COD</span>
                <span class="px-2 py-1 bg-white/5 border border-white/15 rounded text-[8px] font-black tracking-widest text-white">BCA</span>
                <span class="px-2 py-1 bg-white/5 border border-white/15 rounded text-[8px] font-black tracking-widest text-white">MANDIRI</span>
                <span class="px-2 py-1 bg-white/5 border border-white/15 rounded text-[8px] font-black tracking-widest text-white">OVO</span>
                <span class="px-2 py-1 bg-white/5 border border-white/15 rounded text-[8px] font-black tracking-widest text-white">DANA</span>
            </div>
        </div>
    </footer>

    <script>
        // Initial icon render
        if (window.lucide) {
            window.lucide.createIcons();
        }

        // Setup MutationObserver to automatically render dynamic icons (e.g. from Alpine.js templates)
        let iconTimer;
        const observer = new MutationObserver(() => {
            clearTimeout(iconTimer);
            iconTimer = setTimeout(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }, 50);
        });
        observer.observe(document.body, { childList: true, subtree: true });
    </script>

    <!-- Luxury Toast Notification Stack -->
    <div class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300 transform translate-y-[-10px] opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 opacity-0 transform scale-95"
                 class="pointer-events-auto flex items-center gap-3.5 px-5 py-4 bg-white/95 backdrop-blur-md border border-stone-200/80 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.06)]">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <template x-if="toast.type === 'success'">
                        <i data-lucide="check" class="w-4 h-4 text-brand-darkGreen pointer-events-none"></i>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 pointer-events-none"></i>
                    </template>
                    <template x-if="toast.type === 'info'">
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
</body>
</html>
