@# StockMaster System & Progress Memory Pack

This memory file contains the complete system architecture, active endpoints, logic flow implementations, and context summary for the **StockMaster** application. Use this document to resume pairing or hand off to the next workspace agent seamlessly.

---

## 1. Technology Stack
- **Backend Framework**: Laravel 12.x (PHP 8.2+)
- **Frontend Core**: TailwindCSS, Alpine.js (reactive data management), Lucide Icons
- **Database**: MySQL/SQLite (depending on environment settings)
- **Visual Theme**: Premium dark-mode glassmorphic interface ("Zen Glass") with custom Web Audio feedback and smooth GPU-accelerated micro-animations.

---

## 2. Core Routing Architecture (`routes/web.php`)
- **Landing Page**: `/`
- **Storefront (E-Commerce)**:
  - `GET /store` (`StoreController@index`) - The catalog & cart page.
  - `POST /store/checkout` (`StoreController@checkout`) - Places order, decrements inventory stock, and logs transactions.
  - `POST /store/login` / `POST /store/register` - Store authentication.
  - `POST /store/profile` - Customer profile updates.
  - `GET /store/orders` - Order history retrieval.
- **Admin Dashboard**:
  - `GET /dashboard` (`DashboardController@index`) - Main statistics and visualization panel.
  - `GET /dashboard/poll` (`DashboardController@poll`) - Real-time statistics and transaction feed updates (returns JSON).
- **Core Resources**:
  - `items`, `categories`, `units`, `suppliers`, `transactions`, `customers`, `transfers`, `pos` (Purchase Orders), `enterprise-assets`, `reconciliations`.
- **System Settings**:
  - `GET /settings/users` (`UserController@index`) - User role and permission management.
  - `GET /settings/backups` (`BackupController@index`) - Automated system backups.

---

## 3. Real-Time Admin Dashboard Synchronization
### A. The Polling Logic
In `dashboard.blade.php`, Alpine.js is initialized with `dashboardData()`. It triggers `poll()` every **5 seconds**:
```javascript
async poll() {
    try {
        const res  = await fetch(`/dashboard/poll?last_id=${this.latestId}`);
        const data = await res.json();
        if (data.transactions && data.transactions.length > 0) {
            data.transactions.forEach(tx => {
                if (!this.transactions.find(t => t.id === tx.id)) {
                    this.transactions.unshift(tx);
                    if (this.transactions.length > 6) this.transactions.pop();
                }
            });
            this.latestId      = data.latest_id;
            this.totalItems    = data.stats.totalItems;
            this.totalStock    = data.stats.totalStock;
            this.totalValue    = data.stats.totalValue;
            this.lowStockCount = data.stats.lowStockCount;
            
            // Audio-visual feedback
            window.playFeedback('success');
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: { message: 'Transaksi baru masuk!', type: 'success' }
            }));
            this.$nextTick(() => lucide.createIcons());
        }
    } catch(e) { /* silent fail */ }
}
```

### B. Separators & Number Formatting (Indonesian `id-ID` Style)
To match standard Indonesian notation (thousands separated by dot `.`, decimals by comma `,`), statistics cards in `dashboard.blade.php` are formatted as:
- **Total Assets**: `Number(totalItems).toLocaleString('id-ID')`
- **Inventory Volume**: `Number(totalStock).toLocaleString('id-ID')`
- **Market Value**: `'Rp ' + Number(totalValue).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })` (renders as e.g. `Rp 613.485.000,00`).

---

## 4. Key Logic & Architectural Fixes
During development, the following critical system-level items were refactored and verified:

### A. Alpine.js / Blade Directive Collision (CRITICAL)
- **Symptom**: Polling did not trigger the global toast notifications.
- **Root Cause**: The layout had `@show-toast.window="..."`. The Laravel Blade compiler parsed `@show` as its own native blade control statement, stripping it out and outputting an invalid `-toast.window` attribute in the DOM.
- **Fix**: Replaced `@show-toast.window` with standard verbose **`x-on:show-toast.window="..."`**. Always use `x-on:` for custom events in Blade files when the event name starts with `show`.

### B. Global Web Audio Feedback engine
- **Symptom**: Calling `playFeedback('success')` threw `ReferenceError` during polling intervals.
- **Fix**: Bound the synthesizer oscillator engine directly to `window.playFeedback(type)` in `layouts.blade.php`. This allows raw JavaScript setInterval functions to play interface sound cues (`success`, `hover`, `click`) dynamically without scoping constraints.

### C. Dynamic CSRF Token Sync
- **Symptom**: Customer checkouts failed with HTTP 419 token mismatches if they stayed on the storefront page long enough for their session to expire.
- **Fix**: Replaced static compiled token inclusions in Alpine fetch headers with a dynamically executed JS helper:
  ```javascript
  function getCsrf() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  }
  ```
  And configured headers to load `X-CSRF-TOKEN: getCsrf()` on every cart checkout and API transaction request.

---

## 5. Default User Credentials
- **Access Route**: `/login`
- **Role**: Admin
- **Credentials**:
  - **Email**: `admin@stock.com`
  - **Password**: `password`

---

## 6. Guidelines for the Next Developer
1. **Blade Precedence**: Do not use Alpine event binding shortcuts like `@show-` in Blade templates; they clash with Blade compiler statements. Use `x-on:show-`.
2. **View Cache**: When editing views, Laravel compiles them. Always run `php artisan view:clear` to verify updates immediately.
3. **Sound Engineering**: The audio effects engine is fully written using the browser Web Audio API in `layouts.blade.php`. No external media files are required.
4. **Store-to-Dashboard Sync**: Adding transactions on the storefront decreases stock volume on `Item` model, which automatically updates the bento cards on the admin panel when it polls.
