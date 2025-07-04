@extends('back.layouts.backend')
@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/dropzone/min/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/slim/slim.css') }}">
    @stack('product_css')
@endpush
@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">{{ __('back/products.artikl_edit') }}</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('products') }}">{{ __('back/products.artikli') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('back/products.novi_artikl') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Page Content -->
    <div class="content content-full">
        @include('back.layouts.partials.session')
        <!--tabs start-->
        <!-- tabs end-->
        <form action="{{ isset($product) ? route('products.update', ['product' => $product]) : route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($product))
                {{ method_field('PATCH') }}
            @endif
            <!-- Block Tabs Default Style -->
            <div class="block block-rounded">
                <ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#osnovno"><i class="si si-settings"></i> {{ __('back/products.info') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#atributi"><i class="si si-settings"></i> {{ __('back/products.atributi') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#slike"><i class="si si-picture"></i> {{ __('back/products.slike') }}</a>
                    </li>
                    @if ($data['combo'])
                        <li class="nav-item">
                            <a class="nav-link" href="#combo"><i class="si si-flag"></i> Product combo</a>
                        </li>
                    @endif
                    <li class="nav-item ">
                        <a class="nav-link" href="#seo">
                            <i class="si si-link"></i> {{ __('back/products.seo') }}
                        </a>
                    </li>
                </ul>
                <div class="block-content tab-content">
                    <div class="tab-pane active" id="osnovno" role="tabpanel">
                        <div class="block">
                            <div class="block-header block-header-default">
                                <a class="btn btn-light" href="{{ route('products') }}">
                                    <i class="fa fa-arrow-left mr-1"></i> {{ __('back/products.povratak') }}
                                </a>
                                <div class="block-options">
                                    <div class="dropdown">
                                        <div class="d-none custom-control custom-switch custom-control-info block-options-item ml-4">
                                            <input type="checkbox" class="custom-control-input" id="product-gift-switch" name="gift"{{ (isset($product->gift) and $product->gift) ? 'checked' : '' }}>
                                            <label class="custom-control-label pt-1" for="product-gift-switch">Poklon Bon</label>
                                        </div>
                                        <div class=" d-none custom-control custom-switch custom-control-info block-options-item ml-4">
                                            <input type="checkbox" class="custom-control-input" id="product-decrease-switch" name="decrease"{{ (isset($product->decrease) and $product->decrease) ? '' : 'checked' }}>
                                            <label class="custom-control-label pt-1" for="product-decrease-switch">Neograničena Količina</label>
                                        </div>
                                        <div class="custom-control custom-switch custom-control-success block-options-item ml-4">
                                            <input type="checkbox" class="custom-control-input" id="product-switch" name="status"{{ (isset($product->status) and $product->status) ? 'checked' : '' }}>
                                            <label class="custom-control-label pt-1" for="product-switch">{{ __('back/products.aktiviraj') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="row justify-content-center push">
                                    <div class="col-md-12">
                                        <div class="form-group row items-push mb-3">
                                            <div class="col-md-8">
                                                <label for="dm-post-edit-title">{{ __('back/products.naziv') }} <span class="text-danger">*</span></label>
                                                <ul class="nav nav-pills float-right">
                                                    @foreach(ag_lang() as $lang)
                                                        <li @if ($lang->code == current_locale()) class="active" @endif>
                                                            <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#name-{{ $lang->code }}">
                                                                <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @foreach(ag_lang() as $lang)
                                                        <div id="name-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                            <input type="text" class="form-control" id="name-input-{{ $lang->code }}" name="name[{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="{{ isset($product) ? $product->translation($lang->code)->name : old('name.*') }}" onkeyup="SetSEOPreview()">
                                                            @error('name')
                                                            <span class="text-danger font-italic">{{ __('back/products.naziv_je_potreban') }}</span>
                                                            @enderror
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="size_value-input">Pack g/ml </label>
                                                <input type="text" maxlength="3" class="form-control" id="size_value-input" name="size_value" placeholder="" value="{{ isset($product) ? $product->size_value : old('size_value') }}" >
                                            </div>
                                            <div class="col-md-2">
                                                <label for="size_value-input">g || ml </label>

                                                <select class=" form-control" id="size_type" name="size_type" style="width: 100%;" data-placeholder="g || ml">
                                                    <option> </option>

                                                        <option value="g" @if(isset($product) and $product->size_type == 'g') selected="selected" @endif > g </option>
                                                    <option value="ml"  @if(isset($product) and $product->size_type == 'ml') selected="selected" @endif> ml </option>

                                                </select>


                                            </div>
                                        </div>
                                        <div class="form-group row items-push mb-3">
                                            <div class="col-md-3">
                                                <label for="price-input">{{ __('back/products.cijena') }} <span class="text-danger">*</span> <span class="small text-gray">({{ __('back/products.s_pdvom') }})</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="price-input" name="price" placeholder="00.00" value="{{ isset($product) ? $product->price : old('price') }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">€</span>
                                                    </div>
                                                </div>
                                                @error('price')
                                                <span class="text-danger font-italic">{{ __('back/products.cijena_je_potrebna') }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label for="quantity-input">{{ __('back/products.kolicina') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="quantity-input" name="quantity" placeholder="{{ __('back/products.upisite_kolicinu') }}" value="{{ isset($product) ? $product->quantity : ( ! isset($product) ? 1 : old('quantity')) }}">
                                                @error('quantity ')
                                                <span class="text-danger font-italic">{{ __('back/products.kolicina_je_potrebna') }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label for="sku-input">{{ __('back/products.sifra') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="sku-input" name="sku" placeholder="{{ __('back/products.upisite_sifru') }}" value="{{ isset($product) ? $product->sku : old('sku') }}">
                                                @error('sku')
                                                <span class="text-danger font-italic">{{ __('back/products.sifra_je_potrebna') }}</span>
                                                @enderror
                                                @error('sku_dupl')
                                                <span class="text-danger small font-italic">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label for="polica-input">{{ __('back/products.sifra_police') }}</label>
                                                <input type="text" class="form-control" id="polica-input" name="polica" placeholder="{{ __('back/products.upisite_sifru_police') }}" value="{{ isset($product) ? $product->polica : old('polica') }}" >
                                            </div>
                                        </div>
                                        <div class="form-group row items-push mb-3">
                                            <div class="col-md-3">
                                                <label for="special-input">{{ __('back/products.akcija') }}</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="special-input" name="special" placeholder="00.00" value="{{ isset($product) ? $product->special : old('special') }}" readonly>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">€</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="special-from-input">{{ __('back/products.akcija_vrijedi') }}</label>
                                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                                    <input type="text" class="form-control" id="special-from-input" name="special_from" placeholder="{{ __('back/products.od') }}" value="{{ (isset($product->special_from) && $product->special_from != '0000-00-00 00:00:00') ? \Carbon\Carbon::make($product->special_from)->format('d.m.Y') : '' }}" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                                    <div class="input-group-prepend input-group-append">
                                                        <span class="input-group-text font-w600"><i class="fa fa-fw fa-arrow-right"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" id="special-to-input" name="special_to" placeholder="{{ __('back/products.do') }}" value="{{ (isset($product->special_from) && $product->special_from != '0000-00-00 00:00:00') ? \Carbon\Carbon::make($product->special_to)->format('d.m.Y') : '' }}" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="price-input">{{ __('back/products.porez') }}</label>
                                                <select class="js-select2 form-control" id="tax-select" name="tax_id" style="width: 100%;" data-placeholder="{{ __('back/products.odaberite_porez') }}">
                                                    <option></option>
                                                    @foreach ($data['taxes'] as $tax)
                                                        <option value="{{ $tax->id }}" {{ ((isset($product)) and ($tax->id == $product->tax_id)) ? 'selected' : (( ! isset($product) and ($tax->id == 1)) ? 'selected' : '') }}>{{ isset($tax->title->{current_locale()}) ? $tax->title->{current_locale()} : $tax->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!-- CKEditor 5 Classic (js-ckeditor5-classic in Helpers.ckeditor5()) -->
                                        <!-- For more info and examples you can check out http://ckeditor.com -->
                                        <div class="form-group row mb-4">
                                            <div class="col-md-12">
                                                <label for="description-editor">{{ __('back/products.opis') }}</label>
                                                <ul class="nav nav-pills float-right">
                                                    @foreach(ag_lang() as $lang)
                                                        <li @if ($lang->code == current_locale()) class="active" @endif>
                                                            <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#description-{{ $lang->code }}">
                                                                <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                <div class="tab-content">
                                                    @foreach(ag_lang() as $lang)
                                                        <div id="description-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                            <textarea id="description-editor-{{ $lang->code }}" name="description[{{ $lang->code }}]" placeholder="{{ $lang->code }}">{!! isset($product) ? $product->translation($lang->code)->description : old('description.*') !!}</textarea>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="atributi" role="tabpanel">
                        <div class="block">
                            <div class="block-content">
                                <div class="row justify-content-center push">
                                    <div class="col-md-12">
                                        <div class="form-group row items-push mb-4">
                                            <div class="col-md-6">
                                                <label for="categories">{{ __('back/products.odaberi_kategorije') }} @include('back.layouts.partials.required-star')</label>
                                                <select class="form-control" id="category-select" name="category[]" style="width: 100%;" multiple>
                                                    <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                                    @foreach ($data['categories'] as $group => $cats)
                                                        @foreach ($cats as $id => $category)
                                                            <option value="{{ $id }}" class="font-weight-bold small" {{ ((isset($product)) and (in_array($id, $product->categories()->pluck('id')->toArray()))) ? 'selected' : '' }}>{{ $category['title'] }}</option>
                                                            @if ( ! empty($category['subs']))
                                                                @foreach ($category['subs'] as $sub_id => $subcategory)
                                                                    <option value="{{ $sub_id }}" class="pl-3 text-sm" {{ ((isset($product) && $product->subcategory()) and ($sub_id == $product->subcategory()->id)) ? 'selected' : '' }}>{{ $category['title'] . ' >> ' . $subcategory['title'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                                @error('category')
                                                <span class="text-danger font-italic">{{ __('back/products.kategorija_je_obavezna') }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dm-post-edit-slug">Brand</label>
                                                @livewire('back.layout.search.author-search', ['brand_id' => isset($product) ? $product->brand_id : 0])
                                            </div>
                                        </div>
                                        <div class="form-group row items-push mb-4">
                                            <div class="col-md-12">
                                                <label for="sastojci-editor">{{ __('back/products.sastojci') }}</label>
                                                <ul class="nav nav-pills float-right">
                                                    @foreach(ag_lang() as $lang)
                                                        <li @if ($lang->code == current_locale()) class="active" @endif>
                                                            <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#sastojci-{{ $lang->code }}">
                                                                <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @foreach(ag_lang() as $lang)
                                                        <div id="sastojci-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                            <textarea id="sastojci-editor-{{ $lang->code }}" name="sastojci[{{ $lang->code }}]" placeholder="{{ $lang->code }}">{!! isset($product) ? $product->translation($lang->code)->sastojci : old('sastojci.*') !!}</textarea>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row items-push mb-4">
                                            <div class="col-md-12">
                                                <label for="podaci-editor">{{ __('back/products.podaci_o_prehrani') }}</label>
                                                <ul class="nav nav-pills float-right">
                                                    @foreach(ag_lang() as $lang)
                                                        <li @if ($lang->code == current_locale()) class="active" @endif>
                                                            <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#podaci-{{ $lang->code }}">
                                                                <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @foreach(ag_lang() as $lang)
                                                        <div id="podaci-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                            <textarea id="podaci-editor-{{ $lang->code }}" name="podaci[{{ $lang->code }}]" placeholder="{{ $lang->code }}">{!! isset($product) ? $product->translation($lang->code)->podaci : old('podaci.*') !!}</textarea>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row items-push mb-4">
                                            <div class="col-md-3">
                                                <div class="custom-control custom-switch custom-control-success block-options-item ml-4">
                                                    <input type="checkbox" class="custom-control-input" id="product-switch" name="vegan"{{ (isset($product->vegan) and $product->vegan) ? 'checked' : '' }}>
                                                    <label class="custom-control-label pt-1" for="product-switch">Vegan</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="custom-control custom-switch custom-control-success block-options-item ml-4">
                                                    <input type="checkbox" class="custom-control-input" id="product-switch" name="vegetarian"{{ (isset($product->vegetarian) and $product->vegetarian) ? 'checked' : '' }}>
                                                    <label class="custom-control-label pt-1" for="product-switch">Vegeterian</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="custom-control custom-switch custom-control-success block-options-item ml-4">
                                                    <input type="checkbox" class="custom-control-input" id="product-switch" name="glutenfree"{{ (isset($product->glutenfree) and $product->glutenfree) ? 'checked' : '' }}>
                                                    <label class="custom-control-label pt-1" for="product-switch">Glutein Free</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="slike" role="tabpanel">
                        <div class="block">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">{{ __('back/products.slike') }}</h3>
                            </div>
                            <div class="block-content block-content-full">
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <!-- Dropzone (functionality is auto initialized by the plugin itself in js/plugins/dropzone/dropzone.min.js) -->
                                        <!-- For more info and examples you can check out http://www.dropzonejs.com/#usage -->
                                        <!--                            <div class="dropzone">
                                                                        <div class="dz-message" data-dz-message><span>Klikni ovdje ili dovuci slike za uplad</span></div>
                                                                    </div>-->
                                        @include('back.catalog.product.edit-photos', ['resource' => isset($product) ? $product : null, 'existing' => $data['images'], 'delete_url' => route('products.destroy.image')])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($data['combo'])
                        <input type="hidden" value="1" name="combo">
                        <div class="tab-pane" id="combo" role="tabpanel">
                            <div class="block" id="action-list-view">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title">Product combo</h3>
                                </div>
                                @if (isset($product))
                                    @foreach($product->combos as $key => $combo)
                                        <div class="block-content card mb-3 mt-3">
                                            <div class="row justify-content-center">
                                                <div class="col-md-12 card-body">
                                                    <label for="dm-post-edit-title">{{ __('back/products.naziv') }} <span class="text-danger">*</span></label>
                                                    <ul class="nav nav-pills float-right">
                                                        @foreach(ag_lang() as $lang)
                                                            <li @if ($lang->code == current_locale()) class="active" @endif>
                                                                <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif" data-toggle="pill" href="#title-{{ $lang->code }}-{{ $key+1 }}">
                                                                    <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <div class="tab-content">
                                                        @foreach(ag_lang() as $lang)
                                                            <div id="title-{{ $lang->code }}-{{ $key+1 }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                                <input type="text" class="form-control" id="title-input-{{ $lang->code }}" name="combo_title[{{ $key+1 }}][{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="{{ isset($combo->value['title'][$lang->code]) ? $combo->value['title'][$lang->code] : old('title.*') }}">
                                                                @error('name')
                                                                <span class="text-danger font-italic">{{ __('back/products.naziv_je_potreban') }}</span>
                                                                @enderror
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    @if (isset($combo->products) && ! empty($combo->products))
                                                        @livewire('back.marketing.action-group-list', ['group' => 'product', 'count' => $key+1, 'list' => json_decode($combo->products, true)])
                                                    @else
                                                        @livewire('back.marketing.action-group-list', ['group' => 'product', 'count' => $key+1])
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @for ($i = 0; $i < (5 - $product->combos->count()); $i++)
                                        <div class="block-content card mb-3 mt-3">
                                            <div class="row justify-content-center">
                                                <div class="col-md-12 card-body">
                                                    <label for="dm-post-edit-title">{{ __('back/products.naziv') }} <span class="text-danger">*</span></label>
                                                    <ul class="nav nav-pills float-right">
                                                        @foreach(ag_lang() as $lang)
                                                            <li @if ($lang->code == current_locale()) class="active" @endif>
                                                                <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif" data-toggle="pill" href="#title-{{ $lang->code }}-{{ $i+$product->combos->count()+1 }}">
                                                                    <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <div class="tab-content">
                                                        @foreach(ag_lang() as $lang)
                                                            <div id="title-{{ $lang->code }}-{{ $i+$product->combos->count()+1 }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                                <input type="text" class="form-control" id="title-input-{{ $lang->code }}" name="combo_title[{{ $i+$product->combos->count()+1 }}][{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="">
                                                                @error('name')
                                                                <span class="text-danger font-italic">{{ __('back/products.naziv_je_potreban') }}</span>
                                                                @enderror
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    @livewire('back.marketing.action-group-list', ['group' => 'product', 'count' => $i+$product->combos->count()+1])
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                @else
                                    @for ($i = 0; $i < 6; $i++)
                                        <div class="block-content card mb-3 mt-3">
                                            <div class="row justify-content-center">
                                                <div class="col-md-12 card-body">
                                                    <label for="dm-post-edit-title">{{ __('back/products.naziv') }} <span class="text-danger">*</span></label>
                                                    <ul class="nav nav-pills float-right">
                                                        @foreach(ag_lang() as $lang)
                                                            <li @if ($lang->code == current_locale()) class="active" @endif>
                                                                <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif" data-toggle="pill" href="#title-{{ $lang->code }}-{{ $i+1 }}">
                                                                    <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <div class="tab-content">
                                                        @foreach(ag_lang() as $lang)
                                                            <div id="title-{{ $lang->code }}-{{ $i+1 }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                                <input type="text" class="form-control" id="title-input-{{ $lang->code }}" name="combo_title[{{ $i+1 }}][{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="">
                                                                @error('name')
                                                                <span class="text-danger font-italic">{{ __('back/products.naziv_je_potreban') }}</span>
                                                                @enderror
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    @livewire('back.marketing.action-group-list', ['group' => 'product', 'count' => $i+1])
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="tab-pane" id="seo" role="tabpanel">
                        <div class="block">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">{{ __('back/products.meta_data_seo') }}</h3>
                            </div>
                            <div class="block-content">
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="meta-title-input">{{ __('back/products.meta_naslov') }}</label>
                                            <ul class="nav nav-pills float-right">
                                                @foreach(ag_lang() as $lang)
                                                    <li @if ($lang->code == current_locale()) class="active" @endif>
                                                        <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#meta_title-{{ $lang->code }}">
                                                            <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <div class="tab-content">
                                                @foreach(ag_lang() as $lang)
                                                    <div id="meta_title-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                        <input type="text" class="js-maxlength form-control" id="meta-title-input-{{ $lang->code }}" name="meta_title[{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="{{ isset($product) ? $product->translation($lang->code)->meta_title : old('meta_title.*') }}" maxlength="70" data-always-show="true" data-placement="top">
                                                        <small class="form-text text-muted">
                                                            {{ __('back/products.70_znakova_max') }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="meta-description-input">{{ __('back/products.meta_opis') }}</label>
                                            <ul class="nav nav-pills float-right">
                                                @foreach(ag_lang() as $lang)
                                                    <li @if ($lang->code == current_locale()) class="active" @endif>
                                                        <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#meta-description-{{ $lang->code }}">
                                                            <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <div class="tab-content">
                                                @foreach(ag_lang() as $lang)
                                                    <div id="meta-description-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                        <textarea class="js-maxlength form-control" id="meta-description-input-{{ $lang->code }}" name="meta_description[{{ $lang->code }}]" placeholder="{{ $lang->code }}" rows="4" maxlength="160" data-always-show="true" data-placement="top">{{ isset($product) ? $product->translation($lang->code)->meta_description : old('meta_description.*') }}</textarea>
                                                        <small class="form-text text-muted">
                                                            {{ __('back/products.160_znakova_max') }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="slug-input"> {{ __('back/products.seo_url') }}</label>
                                            <ul class="nav nav-pills float-right">
                                                @foreach(ag_lang() as $lang)
                                                    <li @if ($lang->code == current_locale()) class="active" @endif>
                                                        <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#slug-input-{{ $lang->code }}">
                                                            <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <div class="tab-content">
                                                @foreach(ag_lang() as $lang)
                                                    <div id="slug-input-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                        <input type="text" name="slug[{{ $lang->code }}]" class="form-control" id="slug-input-{{ $lang->code }}" placeholder="{{ $lang->code }}" value="{{ isset($product) ? $product->translation($lang->code)->slug : old('slug.*') }}">
                                                        {{--<input type="hidden" name="slug[{{ $lang->code }}]" value="{{ isset($product) ? $product->translation($lang->code)->slug : old('slug.*') }}">--}}

                                                        @if ($data['slugs']->where('lang', $lang->code)->count())
                                                                <div class="row mt-4">
                                                                    <div class="col-md-12 pb-2"><label>Arhiva SEO url-ova</label></div>
                                                            @foreach ($data['slugs']->where('lang', $lang->code)->all() as $slug)
                                                                    <div class="col-md-9"><p class="pl-3">{{ $slug->slug }}</p></div>
                                                                    <div class="col-md-3 text-right">
                                                                        <a class="btn btn-sm btn-alt-danger" href="{{ route('products.delete.slug', ['slug' => $slug->id]) }}">
                                                                            <i class="fa fa-fw fa-trash-alt"></i>
                                                                        </a>
                                                                    </div>
                                                            @endforeach
                                                                </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Block Tabs Default Style -->
            <div class="block">
                <div class="block-content bg-body-light">
                    <div class="row justify-content-center push">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-hero-success my-2">
                                <i class="fas fa-save mr-1"></i> {{ __('back/products.snimi') }}
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            @if (isset($product))
                                <a href="{{ route('products.destroy', ['product' => $product]) }}" type="submit" class="btn btn-hero-danger my-2 js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Obriši" onclick="event.preventDefault(); document.getElementById('delete-product-form{{ $product->id }}').submit();">
                                    <i class="fa fa-trash-alt"></i> {{ __('back/products.obrisi') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @if (isset($product))
            <form id="delete-product-form{{ $product->id }}" action="{{ route('products.destroy', ['product' => $product]) }}" method="POST" style="display: none;">
                @csrf
                {{ method_field('DELETE') }}
            </form>
        @endif
    </div>
@endsection
@push('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/ckeditor5-classic/build/ckeditor.js?v=1') }}"></script>
    <script src="{{ asset('js/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery.maskedinput/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('js/plugins/slim/slim.kickstart.js') }}"></script>
    <!-- Page JS Helpers (CKEditor 5 plugins) -->
    <script>jQuery(function(){Dashmix.helpers(['datepicker']);});</script>
    <script>
        $(() => {
            {!! ag_lang() !!}.forEach(function(item) {
                ClassicEditor
                .create(document.querySelector('#description-editor-' + item.code ), {
                    image: {
                        styles: ['alignLeft', 'alignRight', 'alignCenter', 'block', 'side'],
                        toolbar: [
                            'imageTextAlternative', 'imageStyle:full', 'imageStyle:side', "imageStyle:alignLeft", "imageStyle:alignCenter", 'imageStyle:alignRight'
                        ]
                    },
                    ckfinder: {
                        uploadUrl: '{{ route('products.description.image.upload') }}?_token=' + document.querySelector('meta[name="csrf-token"]').getAttribute('content') + '&product_id={{ (isset($product->id) && $product->id) ?: 0 }}',
                    }
                })
                .then(editor => {
                    console.log(editor);
                })
                .catch(error => {
                    console.error(error);
                });
            });
            {!! ag_lang() !!}.forEach(function(item) {
                ClassicEditor
                .create(document.querySelector('#podaci-editor-' + item.code))
                .then(editor => {
                    console.log(editor);
                })
                .catch(error => {
                    console.error(error);
                });
            });
            {!! ag_lang() !!}.forEach(function(item) {
                ClassicEditor
                .create(document.querySelector('#sastojci-editor-' + item.code))
                .then(editor => {
                    console.log(editor);
                })
                .catch(error => {
                    console.error(error);
                });
            });
            $('#category-select').select2({});
            $('#grupa-select').select2({
                placeholder: '{{ __('back/products.odaberite') }}',
                minimumResultsForSearch: Infinity
            });
            $('#tax-select').select2({});
            $('#action-select').select2({
                placeholder: '{{ __('back/products.odaberite') }}',
                minimumResultsForSearch: Infinity
            });
            $('#author-select').select2({
                tags: true
            });
            $('#publisher-select').select2({
                tags: true
            });
            $('#letter-select').select2({
                tags: true
            });
            $('#binding-select').select2({
                tags: true
            });
            $('#shipping_time-select').select2({
                tags: true
            });
            $('#condition-select').select2({
                tags: true
            });
            Livewire.on('success_alert', () => {
            });
            Livewire.on('error_alert', (e) => {
            });
        })
    </script>
    <script>
        function SetSEOPreview() {
            let title = $('#name-input').val();
            $('#slug-input').val(slugify(title));
        }
    </script>
    @stack('product_scripts')
@endpush
