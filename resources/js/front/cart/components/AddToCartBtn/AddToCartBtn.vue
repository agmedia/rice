<template>
    <div class="cart d-flex flex-wrap align-items-center pt-2 pb-2 mb-3">
        <input class="form-control me-3 mb-1" type="number" inputmode="numeric" pattern="[0-9]*" v-model="quantity" min="1" :max="available" style="width: 5rem;">


      <button class="btn btn-primary btn-shadow me-3 mb-1 " @click="add()" :disabled="disabled"><i class="ci-cart"></i> {{trans.add_to_cart }}</button>
      <p style="width: 100%;" class="fs-md fw-light text-danger" v-if="has_in_cart">{{ trans.imate }} {{ has_in_cart }} {{trans.artikala_u_kosarici }}.</p>


    </div>
</template>

<script>
export default {
    props: {
        id: String,
        available: String
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
            if(cart){

                for (const key in cart.items) {
                    if (this.id == cart.items[key].id) {
                        this.has_in_cart = cart.items[key].quantity;
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
