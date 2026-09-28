/**
 * NOTTE POS Offline-First & Local Sync Engine (IndexedDB)
 * Author: Antigravity / NOTTE ERP
 */

class PosOfflineEngine {
    constructor() {
        this.dbName = 'NottePosDB';
        this.dbVersion = 1;
        this.db = null;
        this.isSyncing = false;
        this.isOnline = navigator.onLine;

        this.init();
    }

    async init() {
        await this.openDatabase();
        this.bindNetworkListeners();
        console.log('⚡ [POS Engine] IndexedDB Initialized. Online status:', this.isOnline);
    }

    openDatabase() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Store 1: Menu catalog cache
                if (!db.objectStoreNames.contains('menus_cache')) {
                    db.createObjectStore('menus_cache', { keyPath: 'id' });
                }

                // Store 2: Offline orders pending synchronization
                if (!db.objectStoreNames.contains('offline_orders')) {
                    const orderStore = db.createObjectStore('offline_orders', { keyPath: 'client_id' });
                    orderStore.createIndex('status', 'status', { unique: false });
                    orderStore.createIndex('timestamp', 'timestamp', { unique: false });
                }
            };

            request.onsuccess = (event) => {
                this.db = event.target.result;
                resolve(this.db);
            };

            request.onerror = (event) => {
                console.error('❌ [POS Engine] IndexedDB Error:', event.target.error);
                reject(event.target.error);
            };
        });
    }

    bindNetworkListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            console.log('🌐 [POS Engine] Network Restored (ONLINE). Auto-triggering Sync...');
            window.dispatchEvent(new CustomEvent('pos-network-change', { detail: { online: true } }));
            this.syncOfflineOrders();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            console.warn('⚠️ [POS Engine] Network Lost (OFFLINE). Running on Local IndexedDB.');
            window.dispatchEvent(new CustomEvent('pos-network-change', { detail: { online: false } }));
        });
    }

    /**
     * Cache active menus into IndexedDB
     */
    async cacheMenus(menus) {
        if (!this.db || !menus || !Array.isArray(menus)) return;

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('menus_cache', 'readwrite');
            const store = tx.objectStore('menus_cache');

            // Clear old cache then insert fresh
            store.clear();
            menus.forEach(menu => store.put(menu));

            tx.oncomplete = () => {
                console.log(`📦 [POS Engine] Cached ${menus.length} menus into IndexedDB.`);
                resolve();
            };
            tx.onerror = (e) => reject(e.target.error);
        });
    }

    /**
     * Get cached menus when offline
     */
    async getCachedMenus() {
        if (!this.db) await this.openDatabase();

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('menus_cache', 'readonly');
            const store = tx.objectStore('menus_cache');
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result || []);
            request.onerror = (e) => reject(e.target.error);
        });
    }

    /**
     * Store offline transaction payload
     */
    async saveOfflineOrder(payload) {
        if (!this.db) await this.openDatabase();

        const clientId = 'OFFLINE-POS-' + Date.now() + '-' + Math.floor(100 + Math.random() * 900);
        const orderRecord = {
            client_id: clientId,
            customer_name: payload.customer_name,
            order_source: payload.order_source || 'offline_pos',
            payment_method: payload.payment_method || 'Cash',
            items: payload.items,
            total_amount: payload.total_amount || 0,
            status: 'pending_sync',
            timestamp: new Date().toISOString()
        };

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('offline_orders', 'readwrite');
            const store = tx.objectStore('offline_orders');
            store.put(orderRecord);

            tx.oncomplete = () => {
                console.log('💾 [POS Engine] Saved offline order locally:', clientId);
                resolve(orderRecord);
            };
            tx.onerror = (e) => reject(e.target.error);
        });
    }

    /**
     * Count pending offline orders
     */
    async getPendingOrdersCount() {
        if (!this.db) await this.openDatabase();

        return new Promise((resolve) => {
            const tx = this.db.transaction('offline_orders', 'readonly');
            const store = tx.objectStore('offline_orders');
            const countReq = store.count();

            countReq.onsuccess = () => resolve(countReq.result || 0);
            countReq.onerror = () => resolve(0);
        });
    }

    /**
     * Fetch all pending offline orders
     */
    async getAllPendingOrders() {
        if (!this.db) await this.openDatabase();

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('offline_orders', 'readonly');
            const store = tx.objectStore('offline_orders');
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result || []);
            request.onerror = (e) => reject(e.target.error);
        });
    }

    /**
     * Delete synced orders by client_id list
     */
    async removeSyncedOrders(clientIds) {
        if (!this.db || !clientIds.length) return;

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('offline_orders', 'readwrite');
            const store = tx.objectStore('offline_orders');

            clientIds.forEach(id => store.delete(id));

            tx.oncomplete = () => resolve();
            tx.onerror = (e) => reject(e.target.error);
        });
    }

    /**
     * Batch Sync Offline Orders to Server
     */
    async syncOfflineOrders(csrfToken, syncUrl = '/admin/pos/sync-offline') {
        if (this.isSyncing || !navigator.onLine) return { synced: 0 };
        this.isSyncing = true;

        try {
            const pendingOrders = await this.getAllPendingOrders();
            if (pendingOrders.length === 0) {
                this.isSyncing = false;
                return { synced: 0 };
            }

            console.log(`🔄 [POS Engine] Syncing ${pendingOrders.length} offline orders to server...`);

            const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch(syncUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || ''
                },
                body: JSON.stringify({ orders: pendingOrders })
            });

            const result = await response.json();

            if (result.success && result.synced && result.synced.length > 0) {
                const syncedClientIds = result.synced.map(item => item.client_id).filter(Boolean);
                await this.removeSyncedOrders(syncedClientIds);

                console.log(`✅ [POS Engine] Successfully synced ${syncedClientIds.length} orders.`);
                
                window.dispatchEvent(new CustomEvent('pos-sync-complete', {
                    detail: {
                        count: syncedClientIds.length,
                        synced: result.synced
                    }
                }));

                return { synced: syncedClientIds.length, result };
            }

            return { synced: 0, error: result.message };

        } catch (error) {
            console.error('❌ [POS Engine] Offline Sync failed:', error);
            return { synced: 0, error: error.message };
        } finally {
            this.isSyncing = false;
        }
    }
}

// Global Singleton Instance
window.posOfflineEngine = new PosOfflineEngine();
