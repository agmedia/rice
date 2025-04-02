<template>
    <div class="cart ">
        <div class="d-flex flex-wrap align-items-center pt-1 pb-2 mb-1">
        <input class="form-control me-3 mb-1" type="number" inputmode="numeric" pattern="[0-9]*" v-model="quantity" min="1" :max="available" style="width: 5rem;">


      <button class="btn btn-primary btn-shadow me-3 mb-1 " @click="add()" :disabled="disabled" aria-label="add to cart button"><i class="ci-cart"></i> {{ trans.add_to_cart }}</button>
     <!-- <p style="width: 100%;" class="fs-md fw-light text-danger" v-if="has_in_cart">{{ trans.imate }} {{ has_in_cart }} {{trans.artikala_u_kosarici }}.</p>
--> </div>

        <div class="form-check mb-3" v-if="min_cart > 1">
            <input class="form-check-input" type="checkbox"  @change="onChangeProcessed($event)" id="ex-check-1">
            <label class="form-check-label" for="ex-check-1">{{ trans.add_to_cart_combo }}  {{ Math.floor(this.min_cart) }} =  {{ fullprice }} </label>
        </div>

    </div>
</template>

<script>
export default {
    props: {
        id: String,
        available: String,
        min_cart:String,
      fullprice:String
    },

    data() {
        return {
            quantity: 1,
            has_in_cart: 0,
            disabled: false,
           trans: window.trans,
        }
    },

    mounted() {
        let cart = this.$store.state.storage.getCart();

        if (cart){
            for (const key in cart.items) {
                if (this.id == cart.items[key].id) {
                    this.has_in_cart = Math.floor(cart.items[key].quantity);
                }
            }
        }

        if (this.available == undefined) {
            this.available = 0;
        }

        this.checkAvailability();
    },

    methods: {
        add() {
            this.checkAvailability(true);

            if (this.has_in_cart) {
                this.updateCart();
            } else {
                this.addToCart();
            }
        },

        onChangeProcessed(e) {
            if (e.target.checked == true) {
                this.quantity = Math.floor(this.min_cart);
            } else {
                this.quantity = 1;
            }
        },

        /**
         *
         */
        addToCart() {
            let item = {
                id: this.id,
                quantity: this.quantity
            }

            this.$store.dispatch('addToCart', item);
        },

        /**
         *
         */
        updateCart() {
            /*if (parseFloat(this.quantity) > parseFloat(this.available)) {
                this.quantity = this.available;
            }*/

            let item = {
                id: this.id,
                quantity: this.quantity,
                relative: true
            }

            this.$store.dispatch('updateCart', item);
        },

        checkAvailability(add = false) {
            if (add) {
                this.has_in_cart = parseFloat(this.has_in_cart) + parseFloat(this.quantity);
            }

            if (this.available <= this.has_in_cart) {
                this.disabled = true;
                this.has_in_cart = this.available;
            }
        }
    }
};
</script>
