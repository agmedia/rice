<template>
    <div class="cart">
        <div class="d-flex flex-wrap align-items-center pt-1 pb-2 mb-1">
            <input
                class="form-control me-3 mb-1"
                type="number"
                inputmode="numeric"
                pattern="[0-9]*"
                v-model.number="quantity"
                :min="1"
                :max="remaining"
                style="width: 5rem;"
            />

            <button
                class="btn btn-primary btn-shadow me-3 mb-1"
                @click="add"
                :disabled="disabled || remaining <= 0 || quantity <= 0"
                aria-label="add to cart button"
            >
                <i class="ci-cart"></i> {{ trans.add_to_cart }}
            </button>
        </div>

        <div class="form-check mb-3" v-if="Number(min_cart) > 1">
            <input class="form-check-input" type="checkbox" @change="onChangeProcessed" id="ex-check-1" />
            <label class="form-check-label" for="ex-check-1">
                {{ trans.add_to_cart_combo }} {{ Math.floor(Number(min_cart)) }} = {{ fullprice }}
            </label>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        id: [String, Number],
        available: { type: [String, Number], default: 0 },
        min_cart: { type: [String, Number], default: 1 },
        fullprice: String
    },

    data() {
        return {
            // DELTA koju želimo dodati ovim klikom
            quantity: 1,
            // koliko već ima u košarici (postojeće stanje)
            has_in_cart: 0,
            disabled: false,
            trans: window.trans
        };
    },

    computed: {
        availableNum() {
            return Number(this.available) || 0;
        },
        remaining() {
            // koliko još smije ući u košaricu
            const rem = this.availableNum - Number(this.has_in_cart || 0);
            return rem > 0 ? rem : 0;
        }
    },

    mounted() {
        const cart = this.$store.state.storage.getCart();

        if (cart && cart.items) {
            for (const key in cart.items) {
                if (String(this.id) === String(cart.items[key].id)) {
                    this.has_in_cart = Math.floor(Number(cart.items[key].quantity) || 0);
                }
            }
        }

        this.checkAvailability();
    },

    methods: {
        add() {
            // delta koliko želimo dodati sada
            let delta = Number(this.quantity) || 1;

            // odreži delta da ne prelazi remaining
            if (delta > this.remaining) delta = this.remaining;

            // ako nema što dodati, disable i izlaz
            if (delta <= 0) {
                this.disabled = true;
                return;
            }

            if (this.has_in_cart > 0) {
                // RELATIVNO povećanje — ovo u store.js (updateCart) šalje GA add_to_cart
                this.updateCart(delta);
            } else {
                // prvi put dodajemo — store.js (addToCart) već šalje GA add_to_cart
                this.addToCart(delta);
            }

            // lokalno ažuriraj stanje tek nakon dispatche-a
            this.has_in_cart = Number(this.has_in_cart) + delta;
            // reset delta za sljedeći klik
            this.quantity = 1;
            this.checkAvailability();
        },

        onChangeProcessed(e) {
            const base = e.target.checked ? Math.floor(Number(this.min_cart) || 1) : 1;
            // nemoj dozvoliti da pređe preostalu dostupnost
            this.quantity = Math.min(base, this.remaining > 0 ? this.remaining : 0);
        },

        addToCart(delta) {
            const item = {
                id: this.id,
                quantity: delta // DELTA koju dodajemo
            };

            this.$store.dispatch('addToCart', item);
        },

        updateCart(delta) {
            const item = {
                id: this.id,
                quantity: delta, // DELTA koju dodajemo
                relative: true   // signal store-u da tretira kao add_to_cart
            };

            this.$store.dispatch('updateCart', item);
        },

        checkAvailability() {
            // disablaj ako smo dosegli dostupnost
            if (this.remaining <= 0) {
                this.disabled = true;
                this.has_in_cart = this.availableNum;
            } else {
                this.disabled = false;
                // osiguraj da trenutni delta ne prelazi remaining
                if (Number(this.quantity) > this.remaining) {
                    this.quantity = this.remaining;
                }
                if (Number(this.quantity) < 1) {
                    this.quantity = 1;
                }
            }
        }
    }
};
</script>
