/* ========= store.js ========= */

/* LocalStorage ključ i poruke */
let storage_cart = {
    name: 'pk_cart',
    cart: { count: 0 }
};
let messages = {
    error: window.trans.errorr,
    cartAdd: window.trans.cartadd,
    cartUpdate: window.trans.cartppdate,
    cartRemove: window.trans.cartremove,
    couponSuccess: window.trans.couponsuccess,
    couponError: window.trans.couponerror,
};

/* API base – ne ovisi o axios.defaults.baseURL */
const API_BASE_REL = (window.API_BASE || '/api/v2').replace(/\/+$/, '');
const API_BASE = /^https?:\/\//i.test(API_BASE_REL)
    ? API_BASE_REL
    : (window.location.origin + API_BASE_REL);

class AgService {
    /* Dohvati košaricu */
    getCart() {
        return axios.get(`${API_BASE}/cart/get`)
            .then(response => response.data)
            .catch(() => this.returnError(messages.error));
    }

    /* Provjeri košaricu */
    checkCart(ids) {
        return axios.post(`${API_BASE}/cart/check`, { ids: ids })
            .then(response => response.data)
            .catch(() => this.returnError(messages.error));
    }

    /* Dodaj u košaricu */
    addToCart(item) {
        return axios.post(`${API_BASE}/cart/add`, { item: item })
            .then(response => {
                if (response.data && response.data.error) {
                    this.returnError(response.data.error);
                    return false;
                }

                // 1) Preferiraj backend GA4 payload
                var dlFromBackend = (response.data && response.data.dl) ? response.data.dl : null;

                // 2) Inače pokušaj izvući associatedModel
                var productModel = null;
                if (!dlFromBackend) {
                    var itemsObj = (response.data && response.data.items) ? response.data.items : {};
                    if (itemsObj && itemsObj[item.id] && itemsObj[item.id].associatedModel) {
                        productModel = itemsObj[item.id].associatedModel;
                    }
                    if (!productModel) {
                        var values = Object.values(itemsObj || {});
                        var found = values.find(function (v) { return String(v && v.id) === String(item.id); });
                        productModel = found && found.associatedModel ? found.associatedModel : null;
                    }
                    if (!productModel) {
                        var vals = Object.values(itemsObj || {});
                        productModel = vals.length ? (vals[vals.length - 1].associatedModel || null) : null;
                    }
                }

                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ ecommerce: null });

                if (dlFromBackend) {
                    window.dataLayer.push(dlFromBackend);
                } else if (productModel && productModel.dataLayer) {
                    var itemObj = Object.assign({}, productModel.dataLayer, { quantity: Number(item.quantity || 1) });
                    window.dataLayer.push({
                        event: 'add_to_cart',
                        ecommerce: { items: [itemObj] }
                    });
                } else {
                    window.dataLayer.push({
                        event: 'add_to_cart',
                        ecommerce: { items: [{ item_id: item.id || '', quantity: Number(item.quantity || 1) }] }
                    });
                }

                this.returnSuccess(messages.cartAdd);
                return response.data;
            })
            .catch(() => this.returnError(messages.error));
    }

    /* Ažuriraj stavku – podržava relative delta (+ i -) */
    updateCart(item) {
        return axios.post(`${API_BASE}/cart/update/${item.id}`, { item: item })
            .then(response => {
                if (response.data && response.data.error) {
                    this.returnError(response.data.error);
                    return false;
                }

                const isRelative = item && item.relative === true;
                const qty = Number(item.quantity);

                // RELATIVNO povećanje → add_to_cart
                if (isRelative && qty > 0) {
                    const qtyAdded = qty;
                    const dlFromBackend = response.data?.dl_add;

                    let productModel = null;
                    if (!dlFromBackend) {
                        const itemsObj = response.data.items || {};
                        productModel = itemsObj?.[item.id]?.associatedModel;

                        if (!productModel) {
                            const values = Object.values(itemsObj);
                            const found = values.find(v => String(v?.id) === String(item.id));
                            productModel = found?.associatedModel;
                        }
                        if (!productModel) {
                            const values = Object.values(itemsObj);
                            productModel = values.length ? values[values.length - 1]?.associatedModel : null;
                        }
                    }

                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({ ecommerce: null });

                    if (dlFromBackend) {
                        window.dataLayer.push(dlFromBackend);
                    } else if (productModel?.dataLayer) {
                        const itemObj = { ...productModel.dataLayer, quantity: qtyAdded };
                        window.dataLayer.push({
                            event: 'add_to_cart',
                            ecommerce: { items: [itemObj] }
                        });
                    } else {
                        window.dataLayer.push({
                            event: 'add_to_cart',
                            ecommerce: { items: [{ item_id: item.id ?? '', quantity: qtyAdded }] }
                        });
                    }
                }
                // RELATIVNO smanjenje → remove_from_cart
                else if (isRelative && qty < 0) {
                    const qtyRemoved = Math.abs(qty);
                    const dlFromBackend = response.data?.dl_remove;

                    let productModel = null;
                    if (!dlFromBackend) {
                        const itemsObj = response.data.items || {};
                        productModel = itemsObj?.[item.id]?.associatedModel;

                        if (!productModel) {
                            const values = Object.values(itemsObj);
                            const found = values.find(v => String(v?.id) === String(item.id));
                            productModel = found?.associatedModel;
                        }
                        if (!productModel) {
                            const values = Object.values(itemsObj);
                            productModel = values.length ? values[values.length - 1]?.associatedModel : null;
                        }
                    }

                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({ ecommerce: null });

                    if (dlFromBackend) {
                        window.dataLayer.push(dlFromBackend);
                    } else if (productModel?.dataLayer) {
                        const itemObj = { ...productModel.dataLayer, quantity: qtyRemoved };
                        window.dataLayer.push({
                            event: 'remove_from_cart',
                            ecommerce: { items: [itemObj] }
                        });
                    } else {
                        window.dataLayer.push({
                            event: 'remove_from_cart',
                            ecommerce: { items: [{ item_id: item.id ?? '', quantity: qtyRemoved }] }
                        });
                    }
                }

                this.returnSuccess(messages.cartUpdate);
                return response.data;
            })
            .catch(() => this.returnError(messages.error));
    }

    /* Ukloni stavku */
    removeItem(item) {
        return axios.get(`${API_BASE}/cart/remove/${item.id}`)
            .then(response => {
                const itemsObj = response.data?.items || {};
                let productModel = itemsObj?.[item.id]?.associatedModel;

                if (!productModel) {
                    const values = Object.values(itemsObj);
                    const found = values.find(v => String(v?.id) === String(item.id));
                    productModel = found?.associatedModel || null;
                }

                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ ecommerce: null });

                if (productModel?.dataLayer) {
                    window.dataLayer.push({
                        event: 'remove_from_cart',
                        ecommerce: { items: [{ ...productModel.dataLayer, quantity: Number(item.quantity || 1) }] }
                    });
                } else {
                    window.dataLayer.push({
                        event: 'remove_from_cart',
                        ecommerce: { items: [{ item_id: item.id ?? '', quantity: Number(item.quantity || 1) }] }
                    });
                }

                this.returnSuccess(messages.cartRemove);
                return response.data;
            })
            .catch(() => this.returnError(messages.error));
    }

    /* Kupon */
    checkCoupon(coupon) {
        if (!coupon) coupon = null;
        return axios.get(`${API_BASE}/cart/coupon/${coupon}`)
            .then(response => {
                this.returnSuccess(messages.couponSuccess);
                return response.data;
            })
            .catch(() => this.returnError(messages.error));
    }

    /* Loyalty */
    updateLoyalty(loyalty) {
        if (!loyalty) loyalty = null;
        return axios.get(`${API_BASE}/cart/loyalty/${loyalty}`)
            .then(response => {
                this.returnSuccess(messages.couponSuccess);
                return response.data;
            })
            .catch(() => this.returnError(messages.error));
    }

    /* Settings (ostavi ako ide preko drugog endpointa) */
    getSettings() {
        return axios.get('settings/get')
            .then(response => response.data)
            .catch(() => this.returnError(messages.error));
    }

    /* Helperi za toast */
    returnSettings(settings) { window.AGSettings = settings; }
    returnError(msg) { window.ToastWarning.fire(msg); }
    returnSuccess(msg) { window.ToastSuccess.fire(msg); }

    /* Formatiraj cijene */
    formatPrice(price) {
        return Number(price).toLocaleString('hr-HR', {
            style: 'currency',
            currencyDisplay: 'symbol',
            currency: 'HRK'
        });
    }

    formatMainPrice(price) {
        if (!store.state.settings) {
            this.getSettings().then((response) => {
                return this.resolvePrice(response['currency.list'], price);
            });
        } else {
            return this.resolvePrice(store.state.settings['currency.list'], price);
        }
    }

    resolvePrice(currency_list, price, main = true) {
        let list = currency_list;
        let main_currency = {};

        list.forEach((item) => {
            if (main) {
                if (item.main) {
                    main_currency = item;
                }
            } else {
                if (!item.main) {
                    main_currency = item;
                    return;
                }
            }
        });

        let left = main_currency.symbol_left ? main_currency.symbol_left + '' : '';
        let right = main_currency.symbol_right ? '' + main_currency.symbol_right : '';

        return left + Number(price * main_currency.value).toFixed(main_currency.decimal_places) + right;
    }

    formatSecondaryPrice(price) {
        if (!store.state.settings) {
            this.getSettings().then((response) => {
                return this.resolvePrice(response['currency.list'], price, false);
            });
        } else {
            return this.resolvePrice(store.state.settings['currency.list'], price, false);
        }
    }

    /* Porez / popust helperi */
    getDiscountAmount(price, special) {
        let discount = ((price - special) / price) * 100;
        return Math.round(discount).toFixed(0);
    }

    calculateItemsTax(items) {
        let tax = 0;
        if (isNaN(items)) {
            for (const key in items) {
                tax += items[key].price - (items[key].price / (Number(items[key].attributes.tax.rate) / 100 + 1));
            }
        } else {
            tax = items - (items / 1.25);
        }
        return tax;
    }
}

class AgStorage {
    getCart() {
        let item = localStorage.getItem(storage_cart.name);
        return (item && item !== 'undefined') ? JSON.parse(item) : null;
    }
    setCart(value) {
        return localStorage.setItem(storage_cart.name, JSON.stringify(value));
    }
}

/* Vuex-like store objekt */
let store = {
    state: {
        storage: new AgStorage(),
        service: new AgService(),
        cart: storage_cart.cart,
        messages: messages,
        settings: null
    },

    actions: {
        getCart(context) { context.commit('setCart'); },

        addToCart(context, item) {
            let state = context.state;
            state.service.addToCart(item).then(cart => {
                if (cart) {
                    state.storage.setCart(cart);
                    state.cart = cart;
                }
            });
        },

        updateCart(context, item) {
            let state = context.state;
            state.service.updateCart(item).then(cart => {
                if (cart) {
                    state.storage.setCart(cart);
                    state.cart = cart;
                }
            });
        },

        removeFromCart(context, item) {
            let state = context.state;
            state.service.removeItem(item).then(cart => {
                state.storage.setCart(cart);
                state.cart = cart;
            });
        },

        checkCart(context, ids) {
            let state = context.state;
            state.service.checkCart(ids).then(response => {
                state.storage.setCart(response.cart);
                if (response.message && window.location.pathname != '/uspjeh') {
                    window.ToastWarningLong.fire(response.message);
                    if (window.location.pathname != '/kosarica') {
                        window.setTimeout(() => { window.location.href = '/kosarica'; }, 5000);
                    }
                }
            });
        },

        checkCoupon(context, coupon) {
            let state = context.state;
            state.cart.coupon = coupon;
            state.storage.setCart(state.cart);

            state.service.checkCoupon(coupon).then(response => {
                if (response) {
                    state.service.returnSuccess(messages.couponSuccess);
                } else {
                    state.service.returnError(messages.couponError);
                }
                context.commit('setCart');
            });
        },

        updateLoyalty(context, loyalty) {
            let state = context.state;
            state.cart.loyalty = loyalty;
            state.storage.setCart(state.cart);

            state.service.updateLoyalty(loyalty).then(response => {
                if (response) {
                    state.service.returnSuccess(messages.couponSuccess);
                } else {
                    state.service.returnError(messages.couponError);
                }
                context.commit('setCart');
            });
        },

        flushCart(context) {
            context.state.cart = context.state.storage.setCart(storage_cart.cart);
        },

        getSettings(context, item) {
            let state = context.state;
            state.service.getSettings(item).then(settings => {
                if (settings) {
                    state.settings = settings;
                }
            });
        },
    },

    mutations: {
        setCart(state) {
            return state.cart = state.service.getCart().then(cart => {
                state.cart = cart;
                return state.storage.setCart(cart);
            });
        }
    },
};

export default store;
