<div class="modal fade" id="shipment-modal-gls" tabindex="-1" role="dialog" aria-labelledby="modal-shipment-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
        <div class="modal-content rounded">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary">
                    <h3 class="block-title">{{ __('back/app.shipping.cod') }}</h3>
                    <div class="block-options">
                        <a class="text-muted font-size-h3" href="#" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row justify-content-center">
                        <div class="col-md-10">

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    @include('back.layouts.partials.language-inputs', [
                                                    'type' => 'input',
                                                    'title' => __('back/app.shipping.input_title'),
                                                    'tab' => 'gls-tab-title',
                                                    'input' => 'title',
                                                    'id' => 'gls-title'
                                                    ])
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gls-price">{{ __('back/app.shipping.trosak') }}</label>
                                        <input type="text" class="form-control" id="gls-price" name="data['price']">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="dm-post-edit-slug">{{ __('back/app.shipping.geo_zone') }} <span class="small text-gray">{{ __('back/app.shipping.geo_zone_label') }}</span></label>
                                    <select class="js-select2 form-control" id="gls-geo-zone" name="geo_zone" style="width: 100%;" data-placeholder="{{ __('back/app.shipping.select_geo') }}">
                                        <option></option>
                                        @foreach ($geo_zones as $geo_zone)
                                            <option value="{{ $geo_zone->id }}" {{ ((isset($shipping)) and ($shipping->geo_zone == $geo_zone->id)) ? 'selected' : '' }}>{{ isset($geo_zone->title->{current_locale()}) ? $geo_zone->title->{current_locale()} : $geo_zone->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label for="gls-time">{{ __('back/app.shipping.trajanje') }} <span class="small text-gray">{{ __('back/app.shipping.trajanje_label') }}</span></label>
                                <input type="text" class="form-control" id="gls-time" name="data['time']">
                            </div>

                            @include('back.layouts.partials.language-inputs', [
                                            'type' => 'textarea',
                                            'title' => __('back/app.shipping.short_desc') . '<span class="small text-gray">' . __('back/app.shipping.short_desc_label') . '</span>',
                                            'tab' => 'gls-tab-short-description',
                                            'input' => 'short_description',
                                            'id' => 'gls-short-description'
                                            ])

                            <div class="form-group mb-4 d-none">
                                <label for="gls-description">{{ __('back/app.shipping.long_desc') }} <span class="small text-gray">{{ __('back/app.shipping.long_desc_label') }}</span></label>
                                <textarea class="form-control" id="gls-description" name="data['description']" rows="4"></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gls-price">{{ __('back/app.shipping.sort_order') }}</label>
                                        <input type="text" class="form-control" id="gls-sort-order" name="sort_order">
                                    </div>
                                </div>
                                <div class="col-md-6 text-right" style="padding-top: 37px;">
                                    <div class="form-group">
                                        <label class="css-control css-control-sm css-control-success css-switch res">
                                            <input type="checkbox" class="css-control-input" id="gls-status" name="status">
                                            <span class="css-control-indicator"></span> {{ __('back/app.shipping.status_title') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="gls-code" name="code" value="gls">
                        </div>
                    </div>
                </div>
                <div class="block-content block-content-full text-right bg-light">
                    <a class="btn btn-sm btn-light" data-dismiss="modal" aria-label="Close">
                        {{ __('back/app.shipping.cancel') }} <i class="fa fa-times ml-2"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="event.preventDefault(); create_gls();">
                        {{ __('back/app.shipping.save') }} <i class="fa fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('shipment-modal-js')
    <script>
        $(() => {
            $('#gls-geo-zone').select2({
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
        });
        /**
         *
         */
        function create_gls() {
            let titles = {};
            let short = {};
            let desc = {};

            {!! ag_lang() !!}.forEach(function(lang) {
                titles[lang.code] = document.getElementById('gls-title-' + lang.code).value;
                short[lang.code] = document.getElementById('gls-short-description-' + lang.code).value;
                desc[lang.code] = null; //document.getElementById('flat-description-' + lang.code).value;
            });

            let item = {
                title: titles,
                code: $('#gls-code').val(),
                data: {
                    price: $('#gls-price').val(),
                    time: $('#gls-time').val(),
                    short_description: short,
                    description: desc,
                },
                geo_zone: $('#gls-geo-zone').val(),
                status: $('#gls-status')[0].checked,
                sort_order: $('#gls-sort-order').val()
            };

            axios.post("{{ route('api.shipping.store') }}", {data: item})
            .then(response => {
                if (response.data.success) {
                    location.reload();
                } else {
                    return errorToast.fire(response.data.message);
                }
            });
        }

        /**
         *
         * @param item
         */
        function edit_gls(item) {
            $('#gls-price').val(item.data.price);
            $('#gls-time').val(item.data.time);
            $('#gls-sort-order').val(item.sort_order);
            $('#gls-code').val(item.code);

            {!! ag_lang() !!}.forEach((lang) => {
                if (item.title && typeof item.title[lang.code] !== undefined) {
                    $('#gls-title-' + lang.code).val(item.title[lang.code]);
                }
                if (item.data.short_description && typeof item.data.short_description[lang.code] !== undefined) {
                    $('#gls-short-description-' + lang.code).val(item.data.short_description[lang.code]);
                }
                if (item.data.description && typeof item.data.description[lang.code] !== undefined) {
                    $('#gls-description-' + lang.code).val(item.data.description[lang.code]);
                }
            });

            if (item.status) {
                $('#gls-status')[0].checked = item.status ? true : false;
            }
        }
    </script>
@endpush
