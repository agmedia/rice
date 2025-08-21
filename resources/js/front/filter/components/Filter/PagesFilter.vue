<template>
    <div class="widget widget-categories" v-if="categories">
        <div class="accordion" id="shop-categories">
            <div class="accordion-item border-bottom">
                <h3 class="accordion-header px-grid-gutter bg-default">
                    <a :href="url" class="nav-link-style d-block fs-md fw-normal py-3" role="link">
                        <span class="d-flex align-items-center"><i class="fa fa-blog"></i> {{ title }} </span>
                    </a>
                </h3>
            </div>
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
    name: 'PagesFilterView',
    props: {
        ids: String,
        group: String,
        cat: String,
        subcat: String,
        title: String,
        url: String,
    },

    //
    data() {
        return {
            categories: [],
            category: null,
            subcategory: null,
            search_query: '',
            origin: location.origin + '/',
            trans: window.trans,
        }
    },

    //
    mounted() {
        this.checkCategory();
        this.getCategories();
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
         */
        setParams() {
            let params = {
                ids: this.ids,
                group: this.group,
                cat: this.category ? this.category.id : this.cat,
                subcat: this.subcategory ? this.subcategory.id : this.subcat
            };

            return params;
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
