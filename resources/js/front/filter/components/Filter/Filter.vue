<template>


        <!-- Categories-->
        <div class="widget widget-categories" v-if="categories">
            <div class="accordion" id="shop-categories">
                <div class="accordion-item border-bottom" v-for="cat in categories">

                    <h3 class="accordion-header px-grid-gutter bg-default" v-if="category && (category.id == cat.id)" >
                        <button class="accordion-button collapsed py-3" v-if="cat.subs" type="button" data-bs-toggle="collapse" :data-bs-target="'#id' + cat.id" aria-expanded="false" :aria-controls="'id'+ cat.id">
                            <span class="d-flex align-items-center"> {{ cat.title }} </span>
                        </button>
                        <a :href="cat.url" v-if="!cat.subs" class="nav-link-style d-block fs-md fw-normal py-3" :class="{'active': (category.id == cat.id)}" role="link">
                            <span class="d-flex align-items-center"><span v-html="cat.icon"></span> {{ cat.title }} </span>
                        </a>
                    </h3>

                    <h3 class="accordion-header px-grid-gutter" v-else>
                        <button class="accordion-button collapsed py-3" v-if="cat.subs " type="button" data-bs-toggle="collapse" :data-bs-target="'#id' + cat.id" aria-expanded="false" :aria-controls="'id'+ cat.id">
                            <span class="d-flex align-items-center"> {{ cat.title }} </span>
                        </button>
                        <a :href="cat.url" v-if="!cat.subs" class="nav-link-style d-block fs-md fw-normal py-3" role="link">
                            <span class="d-flex align-items-center"><span v-html="cat.icon"></span> {{ cat.title }}</span>
                        </a>
                    </h3>

                    <div class="collapse show" :id="'id'+ cat.id" v-if="cat.subs && category && (category.id == cat.id)" data-bs-parent="#shop-categories">
                        <div class="px-grid-gutter pt-1 pb-4">
                            <div class="widget widget-links">
                                <ul class="widget-list" v-for="sub in cat.subs" >
                                    <li class="widget-list-item pb-1 pt-1" :class="{'active': (subcategory && subcategory.id == sub.id)}">
                                        <a class="widget-list-link" :href="sub.url">{{ sub.title }} </a>
                                    </li>
                                </ul>
                                <ul class="widget-list mt-2" >
                                    <li class="widget-list-item"><a class="widget-list-link" :href="cat.url">{{ trans.pogledaj_sve }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>


                    <div class="collapse" :id="'id'+ cat.id"  v-else data-bs-parent="#shop-categories">
                        <div class="px-grid-gutter pt-1 pb-4 ">
                            <div class="widget widget-links">

                                <ul class="widget-list" v-for="subcategory in cat.subs" >
                                    <li class="widget-list-item pb-1 pt-1"><a class="widget-list-link" :href="subcategory.url">{{ subcategory.title }} </a></li>
                                </ul>
                                <ul class="widget-list mt-2" >
                                    <li class="widget-list-item"><a class="widget-list-link" :href="cat.url">{{ trans.pogledaj_sve }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>




</template>


<script>


export default {
    props: {
        ids: String,
        group: String,
        cat: String,
        subcat: String,
        author: String,
        brand: String,
        publisher: String,
    },


    //
    data() {
        return {
            categories: [],
            category: null,
            subcategory: null,
            blog_categories: [],
            recipe_categories: [],
            authors: [],
            brands: [],
            publishers: [],
            selectedAuthors: [],
            selectedBrands: [],
            selectedPublishers: [],
            start: '',
            end: '',
            autor: '',
            brend: '',
            nakladnik: '',
            search_query: '',
            searchAuthor: '',
            searchBrand: '',
            searchPublisher: '',
            show_authors: false,
            authors_loaded: false,
            show_brands: false,
            brands_loaded: false,
            show_publishers: false,
            publishers_loaded: false,
            origin: location.origin + '/',
            trans: window.trans,
        }
    },
    //
    watch: {
        start(currentValue) {
            this.setQueryParam('start', currentValue);
        },
        end(currentValue) {
            this.setQueryParam('end', currentValue);
        },
        selectedAuthors(value) {
            this.autor = value.join('+');
            this.setQueryParamOther('autor', this.autor);
        },
        selectedBrands(value) {
            this.brend = value.join('+');
            this.setQueryParamOther('brand', this.brend);
        },
        selectedPublishers(value) {
            this.nakladnik = value.join('+');
            this.setQueryParamOther('nakladnik', this.nakladnik);
        },
        searchAuthor(value) {
            if (value.length > 2 || value == '') {
                return this.getAuthors();
            }
        },
        searchPublisher(value) {
            if (value.length > 2 || value == '') {
                return this.getPublishers();
            }
        },
      searchBrand(value) {
        if (value.length > 2 || value == '') {
          return this.getBrands();
        }
      },
        $route(params) {
            this.checkQuery(params);
        }
    },

    //
    mounted() {

        this.checkQuery(this.$route);
        this.checkCategory();
        this.getCategories();



        if (this.author == '') {
            this.show_authors = true;
            this.getAuthors();
        }
        if (this.brend == '') {
            this.show_brands = true;
            this.getBrands();
        }

        if (this.publisher == '') {
            this.show_publishers = true;
            this.getPublishers();
        }

        this.preselect();
    },

    methods: {
        /**
         *
         **/
        getCategories() {
            let params = this.setParams();

            axios.post('filter/getCategories', { params }).then(response => {
                this.categories = response.data;
                console.log(this.categories);
            });
        },

        /**
         *
         **/
        getBlogCategories() {
            let params = this.setParams();
            params.group = 'blog';

            axios.post('filter/getCategories', { params }).then(response => {
                this.blog_categories = response.data;
                console.log(this.blog_categories);
            });
        },

        /**
         *
         **/
        checkCategory() {
            if (this.cat != '') {
                this.category = JSON.parse(this.cat);
            }
            if (this.subcat != '') {
                this.subcategory = JSON.parse(this.subcat);
            }
        },

        /**
         *
         **/
        getAuthors() {
            this.authors_loaded = false;
            let params = this.setParams();

            axios.post('filter/getAuthors', { params }).then(response => {
                this.authors_loaded = true;
                this.authors = response.data;
            });
        },

        /**
         *
         **/
        getBrands() {
            this.brands_loaded = false;
            let params = this.setParams();

            axios.post('filter/getBrands', { params }).then(response => {
                this.brands_loaded = true;
                this.brands = response.data;
            });
        },

        /**
         *
         **/
        getPublishers() {
            this.publishers_loaded = false;
            let params = this.setParams();

            axios.post('filter/getPublishers', { params }).then(response => {
                this.publishers_loaded = true;
                this.publishers = response.data;
            });
        },

        /**
         *
         **/
        setQueryParam(type, value) {
            if (value.length > 3 && value.length < 5) {
                this.closeWindow();
                this.$router.push({query: this.resolveQuery()}).catch(()=>{});
            }

            if (value == '') {
                this.closeWindow();
                this.$router.push({query: this.resolveQuery()}).catch(()=>{});
            }
        },

        /**
         *
         **/
        setQueryParamOther(type, value) {
            this.closeWindow();
            this.$router.push({query: this.resolveQuery()}).catch(()=>{});

            if (value == '') {
                this.$router.push({query: this.resolveQuery()}).catch(()=>{});
            }
        },

        /**
         *
         **/
        resolveQuery() {
            let params = {
                start: this.start,
                end: this.end,
                autor: this.autor,
                brand: this.brend,
                nakladnik: this.nakladnik,
                page: this.page,
                pojam: this.search_query,
            };

            this.checkNoFollowQuery(params);

            return Object.entries(params).reduce((acc, [key, val]) => {
                if (!val) return acc
                return { ...acc, [key]: val }
            }, {});
        },

        /**
         *
         */
        checkNoFollowQuery(param) {
            if (param.nakladnik || param.autor || param.brand || param.start || param.end) {
                if (!document.querySelectorAll('meta[name="robots"]').length > 0) {
                    $('head').append('<meta name=robots content=noindex,nofollow>');
                }
            } else {
                if (document.querySelectorAll('meta[name="robots"]').length > 0) {
                    document.querySelector("[name='robots']").remove()
                }
            }
        },

        /**
         *
         **/
        checkQuery(params) {
            this.start = params.query.start ? params.query.start : '';
            this.end = params.query.end ? params.query.end : '';
            this.autor = params.query.autor ? params.query.autor : '';
            this.brend = params.query.brand ? params.query.brand : '';
            this.nakladnik = params.query.nakladnik ? params.query.nakladnik : '';
            this.search_query = params.query.pojam ? params.query.pojam : '';
        },

        /**
         *
         */
        setParams() {
            let params = {
                ids: this.ids,
                group: this.group,
                cat: this.category ? this.category.id : this.cat,
                subcat: this.subcategory ? this.subcategory.id : this.subcat,
                author: this.author,
                brand: this.brend,
                publisher: this.publisher,
                search_author: this.searchAuthor,
                search_publisher: this.searchPublisher,
                search_brand: this.searchBrand,
                pojam: this.search_query
            };

            if (this.author != '') {
                params.author = this.author;
            }
            if (this.brend != '') {
                params.brand = this.brend;
            }
            if (this.publisher != '') {
                params.publisher = this.publisher;
            }

            return params;
        },

        /**
         *
         */
        preselect() {
            if (this.autor != '') {
                if ((this.autor).includes('+')) {
                    this.selectedAuthors = (this.autor).split('+');
                } else {
                    this.selectedAuthors = [this.autor];
                }
            }
            if (this.nakladnik != '') {
                if ((this.nakladnik).includes('+')) {
                    this.selectedPublishers = (this.nakladnik).split('+');
                } else {
                    this.selectedPublishers = [this.nakladnik];
                }
            }

            if (this.brend != '') {
                if ((this.brend).includes('+')) {
                    this.selectedBrands = (this.brend).split('+');
                } else {
                    this.selectedBrands = [this.brend];
                }
            }
        },

        /**
         *
         */
        cleanQuery() {
            this.$router.push({query: {}}).catch(()=>{});
            this.selectedAuthors = [];
            this.selectedPublishers = [];
            this.selectedBrands = [];
            this.start = '';
            this.end = '';
        },

        /**
         *
         */
        closeWindow() {
            $('#shop-sidebar').removeClass('collapse show');
        }
    }
};
</script>


<style>

</style>
