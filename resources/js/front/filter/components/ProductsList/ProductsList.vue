<template>
    <section class="col">
        <!-- Toolbar-->
        <div class="d-flex justify-content-between align-items-center pt-2 pb-4 pb-sm-2">
            <div class="d-flex flex-wrap">
                <div class="d-flex align-items-center flex-nowrap me-0 me-sm-4 pb-3">
                    <select class="form-select pe-2" style="min-width: 130px;" v-model="sorting">
                        <option value="">{{ trans.sortiraj }}</option>
                        <option value="novi">{{ trans.najnovije }}</option>
                        <option value="price_up">{{ trans.najmanja_cijena }}</option>
                        <option value="price_down">{{ trans.najveca_cijena }}</option>
                        <option value="naziv_up">{{ trans.a_z }}</option>
                        <option value="naziv_down">{{ trans.z_a }}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex pb-3"><span class="fs-sm text-dark btn btn-white btn-sm text-nowrap ms-0 d-block">{{ products.total ? Number(products.total).toLocaleString('hr-HR') : 0 }} {{ trans.artikala }}</span></div>
            <div class="d-flex d-sm-none pb-3">
                <button class="btn btn-icon btn-sm nav-link-style  me-1" v-on:click="tworow()" ><i class="ci-view-grid"></i></button>

                <button class="btn btn-icon btn-sm nav-link-style " v-on:click="onerow()"><i class="ci-view-list"></i></button>
            </div>
        </div>
        <!-- Products grid-->
        <div class="row row-cols-xxxl-5 row-cols-xxl-4 row-cols-xl-4 row-cols-lg-3 row-cols-md-3 row-cols-sm-2 row-cols-2 g-0 mx-n2 mb-5"  id="product-grid" v-if="products.total">
            <div class="px-2 mb-4 d-flex align-items-stretch" v-for="product in products.data">
                <div class="card product-card card-static pb-3">

                    <div class="btn-wishlist-block">

                        <button class="btn-wishlist me-1 " v-if="product.vegan" type="button" v-tooltip:top="'Vegan'"><img src="image/vegan.svg" alt="Vegan" width="25px"/></button>

                        <button class="btn-wishlist me-1 "  v-if="product.vegetarian" type="button" v-tooltip:top="'Vegetarian'"><img src="image/vegeterian.svg" alt="Vegeterian"  width="25px"/></button>

                        <button class="btn-wishlist  me-1"  v-if="product.glutenfree" type="button" v-tooltip:top="'Gluten Free'"><img src="image/gluten-free.svg" alt="Gluten Free"  width="35px"/></button>
                    </div>



                    <span class="badge bg-warning mt-1 ms-1 badge-end"  v-if="product.quantity <= 0">{{ trans.rasprodano }}</span>
                    <span class="badge rounded-pill bg-primary mt-1 ms-1 badge-shadow" v-if="product.main_price > product.main_special && product.action && !product.action.min_cart">-{{ ($store.state.service.getDiscountAmount(product.main_price, product.main_special)) }}%</span>

                    <a class="card-img-top d-block pb-2 overflow-hidden " :href="origin + product.url"><img loading="lazy" :src="product.image.replace('.webp', '-thumb.webp')" width="400" height="400" :alt="product.alt.alt" :title="product.alt.title">
                    </a>
                    <div class="card-body py-2 px-2 pt-0">

                        <h3 class="product-title fs-sm text-truncate"><a :href="origin + product.url">{{ product.name }} </a></h3>
                        <div class="d-flex flex-wrap justify-content-between align-items-center" v-if="product.category_string">
                            <div class="fs-sm me-2"><span v-html="product.category_string"></span></div>
                        </div>
                        <div class="product-price">
                            <span class="fs-sm text-muted"  v-if="product.main_price > product.main_special && product.action && !product.action.min_cart"><small>NC 30 dana: {{ product.main_price_text }} </small> <small v-if="product.secondary_price_text">{{ product.secondary_price_text }} </small></span>
                        </div>
                        <div class="product-price">
                            <span class="text-red fs-md" v-if="product.main_price > product.main_special && product.action && !product.action.min_cart">{{ product.main_special_text }} <span class="text-muted"><strike>{{ product.main_price_text }}</strike></span> <small v-if="product.secondary_special_text">{{ product.secondary_special_text }} </small></span>
                        </div>
                        <div class="product-price">
                            <span class="text-dark fs-md" v-if="!product.special">{{ product.main_price_text }} <small v-if="product.secondary_price_text ">{{ product.secondary_price_text }} </small></span>
                        </div>

                        <div class="star-rating" v-if="product.stars">
                            <span v-for="item in 5 ">
                                <i  v-if="Math.floor(product.stars) - item >= 0" class="star-rating-icon ci-star-filled active"></i>

                                <i v-else-if="product.stars - item > -1 " class="star-rating-icon ci-star-half active"></i>



                               <i v-else class="star-rating-icon ci-star"></i>
                            </span>
                        </div>
                    </div>
                    <div v-if="(product.url).includes('smrznuti-proizvodi') || (product.url).includes('frozen-products')">
<!--                        <div class="product-floating-btn">
                            <a href="#" class="btn btn-primary btn-shadow btn-sm" ></a>
                        </div>-->
                    </div>
                    <div v-else>
                        <div class="product-floating-btn" v-if="product.quantity > 0 && product.combo == 0">
                            <button class="btn btn-primary btn-shadow btn-sm" :disabled="product.disabled" v-on:click="add(product.id, product.quantity)" type="button"><i class="ci-cart fs-base ms-0"></i></button>
                        </div>

                        <div class="product-floating-btn" v-if="product.quantity > 0 && product.combo == 1">
                            <a :href="origin + product.url" class="btn btn-primary btn-shadow btn-sm" >+<i class="ci-cart fs-base ms-1"></i></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <pagination :data="products" align="center" :show-disabled="true" :limit="4" @pagination-change-page="getProductsPage"></pagination>

        <div class="row" v-if="!products_loaded">
            <div class="col-md-12 d-flex justify-content-center mt-4">
                <div class="spinner-border text-primary opacity-75" role="status" style="width: 9rem; height: 9rem;"></div>
            </div>
        </div>
        <div class="col-md-12 d-flex justify-content-center mt-4" v-if="products.total">
            <p class="fs-sm">{{ trans.prikazano }}
                <span class="font-weight-bolder mx-1">{{ products.from ? Number(products.from).toLocaleString('hr-HR') : 0 }}</span> {{ trans.do }}
                <span class="font-weight-bolder mx-1">{{ products.to ? Number(products.to).toLocaleString('hr-HR') : 0 }}</span> {{ trans.od }}
                <span class="font-weight-bold mx-1">{{ products.total ? Number(products.total).toLocaleString('hr-HR') : 0 }}</span> {{ trans.rezultata }}
            </p>
        </div>
        <div class="col-md-12 px-2 mb-4" v-if="products_loaded && search_zero_result">
            <h2>{{ trans.nema_rezultata }}</h2>
            <p> {{ trans.vasa_pretraga }} <mark>{{ search_query }}</mark> {{ trans.pronasla_nula }}.</p>
            <h4 class="h5">{{ trans.s1 }}</h4>
            <ul class="list-style">
                <li>{{ trans.s2 }}</li>
                <li>{{ trans.s3 }}</li>
                <li>{{ trans.s4 }}</li>
            </ul>
            <hr class="d-sm-none">
        </div>
        <div class="col-md-12  mb-4" v-if="products_loaded && navigation_zero_result">
            <h2>{{ trans.t1 }}</h2>
            <p> {{ trans.t2 }}</p>
            <hr class="d-sm-none">
        </div>

        <script type="application/ld+json" v-if="products.data && products.data.length" v-html="renderItemListSchema()"></script>
    </section>
</template>

<script>

Vue.directive('tooltip', function(el, binding){
    $(el).tooltip({
        title: binding.value,
        placement: binding.arg,
        trigger: 'hover'
    })
})

export default {
    name: 'ProductsList',
    props: {
        ids: String,
        group: String,
        cat: String,
        subcat: String,
        author: String,
        publisher: String,
        brand: String,
    },
    //
    data() {
        return {
            products: {},
            autor: '',
            brend: '',
            nakladnik: '',
            start: '',
            end: '',
            sorting: '',
            search_query: '',
            page: 1,
            origin: location.origin + '/',
            hr_total: 'rezultata',
            products_loaded: false,
            search_zero_result: false,
            navigation_zero_result: false,
            trans: window.trans,
        }
    },
    //
    watch: {
        sorting(value) {
            this.setQueryParam('sort', value);
        },
        $route(params) {
            this.checkQuery(params);
        }
    },
    //
    computed: {
        itemListSchema() {
            if (!this.products.data || !this.products.data.length) {
                return null;
            }

            const schema = {
                '@context': 'https://schema.org',
                '@type': 'ItemList',
                'numberOfItems': this.products.data.length,
                'itemListElement': []
            };

            this.products.data.forEach((product, index) => {
                schema.itemListElement.push({
                    '@type': 'ListItem',
                    'position': index + 1,
                    'item': {
                        '@type': 'Product',
                        'name': product.name,
                        'url': this.origin + product.url,
                        'image': product.image,
                        'sku': product.sku || '',
                        'description': product.description || '',
                        'brand': {
                            '@type': 'Brand',
                            'name': product.brand ? product.brand.title : ''
                        },
                        'offers': {
                            '@type': 'Offer',
                            'priceValidUntil': new Date(new Date().getFullYear(), 11, 31),
                            'priceCurrency': 'EUR',
                            'price': product.main_special || product.main_price,
                            'availability': product.quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
                        }
                    }
                });
            });

            return schema;
        }
    },
    //
    mounted() {
        this.checkQuery(this.$route);
    },

    methods: {
        /**
         *
         */
        getProducts() {
            this.search_zero_result = false;
            this.navigation_zero_result = false;
            this.products_loaded = false;
            let params = this.setParams();

            console.log('tu sam...')
            console.log(params)

            axios.post('filter/getProducts', { params }).then(response => {
                this.products_loaded = true;
                this.products = response.data;
                this.checkHrTotal();
                this.checkSpecials();

                if (this.$store.state.storage.getCart()) {
                    this.checkAvailables();
                }

                console.log('Response::data.data')
                console.log(response.data.data)

                if (params.pojam != '' && !this.products.total) {
                    this.search_zero_result = true;
                }

                if (params.pojam == '' && !this.products.total) {
                    this.navigation_zero_result = true;
                }
            });
        },

        /**
         *
         * @param page
         */
        getProductsPage(page = 1) {
            this.products_loaded = false;
            this.page = page;
            this.setQueryParam('page', page);

            let params = this.setParams();
            window.scrollTo({top: 0, behavior: 'smooth'});

            axios.post('filter/getProducts?page=' + page, { params }).then(response => {
                this.products_loaded = true;
                this.products = response.data;
                this.checkHrTotal();
                this.checkSpecials();
                this.checkAvailables();
            });
        },

        /**
         *
         * @returns {string}
         */
        renderItemListSchema() {
            if (!this.itemListSchema) {
                return '';
            }

            return JSON.stringify(this.itemListSchema);
        },

        /**
         *
         * @param type
         * @param value
         */
        setQueryParam(type, value) {
            this.closeFilter();
            this.$router.push({query: this.resolveQuery()}).catch(()=>{});

            if (value == '' || value == 1) {
                this.$router.push({query: this.resolveQuery()}).catch(()=>{});
            }
        },

        /**
         *
         * @return {{}}
         */
        resolveQuery() {
            let params = {
                start: this.start,
                end: this.end,
                autor: this.autor,
                brand: this.brend,
                nakladnik: this.nakladnik,
                sort: this.sorting,
                pojam: this.search_query,
                page: this.page
            };

            return Object.entries(params).reduce((acc, [key, val]) => {
                if (!val) return acc
                return { ...acc, [key]: val }
            }, {});
        },

        /**
         *
         * @param params
         */
        checkQuery(params) {
            this.start = params.query.start ? params.query.start : '';
            this.end = params.query.end ? params.query.end : '';
            this.autor = params.query.autor ? params.query.autor : '';
            this.brend = params.query.brand ? params.query.brand : '';
            this.nakladnik = params.query.nakladnik ? params.query.nakladnik : '';
            this.page = params.query.page ? params.query.page : '';
            this.sorting = params.query.sort ? params.query.sort : '';
            this.search_query = params.query.pojam ? params.query.pojam : '';

            if (this.page != '') {
                this.getProductsPage(this.page);
            } else {
                this.getProducts();
            }
        },

        /**
         *
         * @return {{cat: String, start: string, pojam: string, subcat: String, end: string, sort: string, nakladnik: string, autor: string, group: String}}
         */
        setParams() {
            let params = {
                ids: this.ids,
                group: this.group,
                cat: this.cat,
                subcat: this.subcat,
                autor: this.autor,
                brand: this.brend ? this.brend : this.brand,
                nakladnik: this.nakladnik,
                start: this.start,
                end: this.end,
                sort: this.sorting,
                pojam: this.search_query
            };

            if (this.author != '') {
                params.autor = this.author;
            }

            if (this.brend != '') {
                params.brand = this.brend;
            }
            if (this.publisher != '') {
                params.nakladnik = this.publisher;
            }

            return params;
        },

        /**
         *
         */
        checkSpecials() {
            let now = new Date();

            for (let i = 0; i < this.products.data.length; i++) {
                if (Number(this.products.data[i].main_price) <= Number(this.products.data[i].main_special)) {
                    this.products.data[i].special = false;
                }
            }
        },

        /**
         *
         */
        checkAvailables() {
            let cart = this.$store.state.storage.getCart();
            if (cart) {
                for (let i = 0; i < this.products.data.length; i++) {
                    this.products.data[i].disabled = false;
                    for (const key in cart.items) {
                        if (this.products.data[i].id == cart.items[key].id) {
                            if (this.products.data[i].quantity <= cart.items[key].quantity) {
                                this.products.data[i].disabled = true;
                            }
                        }
                    }
                }
            }
        },

        /**
         *
         */
        checkHrTotal() {
            this.hr_total = 'rezultata';
            if ((this.products.total).toString().slice(-1) == '1') {
                this.hr_total = 'rezultat';
            }
        },

        /**
         *
         * @param id
         */
        add(id, product_quantity) {
            let cart = this.$store.state.storage.getCart();
            if (cart) {
                for (const key in cart.items) {
                    if (id == cart.items[key].id) {
                        if (product_quantity <= cart.items[key].quantity) {
                            return window.ToastWarning.fire('Nažalost nema dovoljnih količina artikla..!');
                        }
                    }
                }
            }

            this.$store.dispatch('addToCart', {
                id: id,
                quantity: 1
            })
        },

        /**
         *
         */
        closeFilter() {
            $('#shop-sidebar').removeClass('collapse show');
        },

        onerow() {
            $('#product-grid').removeClass('row-cols-2');
            $('#product-grid').addClass('row-cols-1');
        },

        tworow() {
            $('#product-grid').removeClass('row-cols-1');
            $('#product-grid').addClass('row-cols-2');
        }
    }
};
</script>

<style>
</style>
