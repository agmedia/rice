@extends('back.layouts.backend')

@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endpush

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">{{ __('back/categories.kategorija_edit') }}</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('categories') }}">{{ __('back/categories.kategorije') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('back/categories.nova_kategorija') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content content-full content-boxed">
        <!-- END Page Content -->
        @include('back.layouts.partials.session')
        <!-- New Post -->
        <form action="{{ isset($category) ? route('category.update', ['category' => $category]) : route('category.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($category))
                {{ method_field('PATCH') }}
            @endif
            <div class="block">
                <div class="block-header block-header-default">
                    <a class="btn btn-light" href="{{ route('categories') }}">
                        <i class="fa fa-arrow-left mr-1"></i> {{ __('back/categories.povratak') }}
                    </a>
                    <div class="block-options">
                        <div class="custom-control custom-switch custom-control-success">
                            <input type="checkbox" class="custom-control-input" id="category-switch" name="status" {{ (isset($category->status) and $category->status) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="category-switch">{{ __('back/categories.aktiviraj') }}</label>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row justify-content-center push">
                        <div class="col-md-10">

                            <div class="form-group">
                                <label for="title-input">{{ __('back/categories.naziv_kategorije') }}</label>

                                <ul class="nav nav-pills float-right">
                                    @foreach(ag_lang() as $lang)
                                        <li @if ($lang->code == current_locale()) class="active" @endif>
                                            <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#title-{{ $lang->code }}">
                                                <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content">
                                    @foreach(ag_lang() as $lang)
                                        <div id="title-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                            <input type="text" class="form-control" id="title-input-{{ $lang->code }}" name="title[{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="{{ isset($category) ? $category->translation($lang->code)->title : old('title.*') }}" onkeyup="SetSEOPreview()">
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="group-select">{{ __('back/categories.grupa') }}</label>
                                <select class="js-select2 form-control" id="group-select" name="group" style="width: 100%;">
                                    @foreach ($groups as $group)
                                        <option value="{{ $group['value'] }}" {{ (isset($category) && ($category->group == $group['value'])) ? 'selected' : '' }}>{{ $group['title'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sort_order-input">Sort</label>
                                <input type="text" class="form-control" id="sort_order-input" name="sort_order" placeholder="Sort" value="{{ isset($category) ? $category->sort_order : old('sort_order') }}"  required>
                            </div>


                            <div class="form-group" id="parent-select-group">
                                <label for="parent-select">{{ __('back/categories.glavna_kategorija') }}</label>
                                <select class="js-select2 form-control" id="parent-select" name="parent" style="width: 100%;">
                                    <option></option>
                                    <option value="0">...</option>
                                    @foreach ($parents as $id => $name)
                                        <option value="{{ $id }}" {{ (isset($category->parent_id) and $id == $category->parent_id) ? 'selected="selected"' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="slug-input">{{ __('back/categories.seo_url') }}</label>
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
                                            <input type="text" class="form-control" id="slug-input-{{ $lang->code }}" name="slug[{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="{{ isset($category) ? $category->translation($lang->code)->slug : old('slug.*') }}" >
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="dm-post-edit-slug">{{ __('back/categories.opis_kategorije') }}</label>
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
                                            <textarea id="description-editor-{{ $lang->code }}" name="description[{{ $lang->code }}]" placeholder="{{ $lang->code }}">{!! isset($category) ? $category->translation($lang->code)->description : old('description.*') !!}</textarea>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="short-description-input">{{ __('back/categories.kratki_opis') }}</label>
                                <ul class="nav nav-pills float-right">
                                    @foreach(ag_lang() as $lang)
                                        <li @if ($lang->code == current_locale()) class="active" @endif>
                                            <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#short-description-{{ $lang->code }}">
                                                <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content">
                                    @foreach(ag_lang() as $lang)
                                        <div id="short-description-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                            <textarea class="js-maxlength form-control" id="short-description-input-{{ $lang->code }}" name="short_description[{{ $lang->code }}]" placeholder="{{ $lang->code }}" rows="4" maxlength="160" data-always-show="true" data-placement="top">{{ isset($category) ? $category->translation($lang->code)->short_description : old('short_description.*') }}</textarea>
                                            <small class="form-text text-muted">
                                                {{ __('back/products.160_znakova_max') }}
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">{{ __('back/categories.meta_data_seo') }}</h3>
                </div>
                <div class="block-content">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="meta-title-input">{{ __('back/categories.meta_naslov') }}</label>
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
                                            <input type="text" class="js-maxlength form-control" id="meta-title-input-{{ $lang->code }}" name="meta_title[{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="{{ isset($category) ? $category->translation($lang->code)->meta_title : old('meta_title.*') }}" maxlength="70" data-always-show="true" data-placement="top">
                                            <small class="form-text text-muted">
                                                {{ __('back/products.70_znakova_max') }}
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="meta-description-input">{{ __('back/categories.meta_opis') }}</label>
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
                                            <textarea class="js-maxlength form-control" id="meta-description-input-{{ $lang->code }}" name="meta_description[{{ $lang->code }}]" placeholder="{{ $lang->code }}" rows="4" maxlength="160" data-always-show="true" data-placement="top">{{ isset($category) ? $category->translation($lang->code)->meta_description : old('meta_description.*') }}</textarea>
                                            <small class="form-text text-muted">
                                                {{ __('back/products.160_znakova_max') }}
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-xl-6">
                                    <label>{{ __('back/categories.open_graph_slika') }}</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image-input" name="image" data-toggle="custom-file-input" onchange="readURL(this);">
                                        <label class="custom-file-label" for="image-input">{{ __('back/categories.odaberite_sliku') }}</label>
                                    </div>
                                    <div class="mt-2">
                                        <img class="img-fluid" id="image-view" src="{{ isset($category) ? asset($category->image) : asset('media/img/lightslider.webp') }}" alt="">
                                    </div>
                                    <div class="form-text text-muted font-size-sm font-italic">{{ __('back/categories.slika_koja_se_pokazuje') }}</div>
                                </div>

                                <div class="col-xl-6">
                                    <label for="title-input" class="w-100">Naziv fotografije
                                        <ul class="nav nav-pills float-right">
                                            @foreach(ag_lang() as $lang)
                                                <li @if ($lang->code == current_locale()) class="active" @endif>
                                                    <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#image-title-{{ $lang->code }}">
                                                        <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </label>
                                    <div class="tab-content">
                                        @foreach(ag_lang() as $lang)
                                            <div id="image-title-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                <input type="text" class="form-control" id="title-input-{{ $lang->code }}" name="image_title[{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="{{ isset($category) ? $category->translation($lang->code)->image_title : old('image_title.*') }}">
                                            </div>
                                        @endforeach
                                    </div>

                                    <br>

                                    <label for="title-input" class="w-100">Alternativni tekst fotografije
                                        <ul class="nav nav-pills float-right">
                                            @foreach(ag_lang() as $lang)
                                                <li @if ($lang->code == current_locale()) class="active" @endif>
                                                    <a class="btn btn-sm btn-outline-secondary ml-2 @if ($lang->code == current_locale()) active @endif " data-toggle="pill" href="#alt-{{ $lang->code }}">
                                                        <img src="{{ asset('media/flags/' . $lang->code . '.png') }}" />
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </label>
                                    <div class="tab-content">
                                        @foreach(ag_lang() as $lang)
                                            <div id="alt-{{ $lang->code }}" class="tab-pane @if ($lang->code == current_locale()) active @endif">
                                                <input type="text" class="form-control" id="title-input-{{ $lang->code }}" name="image_alt[{{ $lang->code }}]" placeholder="{{ $lang->code }}" value="{{ isset($category) ? $category->translation($lang->code)->image_alt : old('image_alt.*') }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-content bg-body-light">
                    <div class="row justify-content-center push">
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-hero-success my-2">
                                <i class="fas fa-save mr-1"></i> {{ __('back/categories.snimi') }}
                            </button>
                        </div>
                        <div class="col-md-5 text-right">
                            @if (isset($category))

                                <a href="{{ route('category.destroy', ['category' => $category]) }}" type="submit" class="btn btn-hero-danger my-2 js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Obriši" onclick="event.preventDefault(); document.getElementById('delete-category-form{{ $category->id }}').submit();">
                                    <i class="fa fa-trash-alt"></i> {{ __('back/categories.obrisi') }}
                                </a>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- END New Post -->
        @if (isset($category))
            <form id="delete-category-form{{ $category->id }}" action="{{ route('category.destroy', ['category' => $category]) }}" method="POST" style="display: none;">
                @csrf
                {{ method_field('DELETE') }}
            </form>
        @endif
    </div>


@endsection

@push('js_after')
    <script src="{{ asset('js/plugins/ckeditor5-classic/build/ckeditor.js?v=1') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(() => {
            $('#group-select').select2({
                placeholder: '{{ __('back/categories.odaberite_ili_upisite_novu_grupu') }}',
                minimumResultsForSearch: Infinity
            });

            // $('#group-select').on('select2:select', function (e) {
            //     if (e.currentTarget.value == 'blog') {
            //         $('#parent-select-group').hide();
            //     } else {
            //         $('#parent-select-group').show();
            //     }
            // })
            //
            // if ($('#group-select').val() == 'blog') {
            //     $('#parent-select-group').hide();
            // } else {
            //     $('#parent-select-group').show();
            // }

            $('#parent-select').select2({
                placeholder: '{{ __('back/categories.ostavite_oprazno') }}'
            });

            {!! ag_lang() !!}.forEach(function(item) {
                ClassicEditor
                .create(document.querySelector('#description-editor-' + item.code ))

                .then(editor => {
                    console.log(editor);
                })
                .catch(error => {
                    console.error(error);
                });

            });
        })
    </script>

    <script>
        function SetSEOPreview() {
            let title = $('#title-input').val();
            $('#slug-input').val(slugify(title));
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#image-view')
                    .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

@endpush
