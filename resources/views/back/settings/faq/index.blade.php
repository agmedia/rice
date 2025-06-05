@extends('back.layouts.backend')

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">{{ __('back/faq.naslov') }} </h1>
                <a class="btn btn-hero-success my-2" href="{{ route('faqs.create') }}">
                    <i class="far fa-fw fa-plus-square"></i><span class="d-none d-sm-inline ml-1">{{ __('back/faq.dodaj_novi') }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="content content-full">
    @include('back.layouts.partials.session')

        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">FAQ ({{ $faqs->total() }})</h3>
            </div>
            <div class="block-content">
                <table class="table table-striped table-borderless table-vcenter">
                    <thead class="thead-light">
                    <tr>
                        <th style="width: 70%;">{{ __('back/faq.pitanje') }}</th>

                        <th style="width: 20%;">{{ __('back/categories.kategorije') }}</th>
                        <th class="text-right"  class="text-center">{{ __('back/faq.uredi') }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse ($faqs as $faq)
                        <tr>
                            <td>
                                <a href="{{ route('faqs.edit', ['faq' => $faq]) }}">{{ $faq->translation->title }}</a>
                            </td>

                            <td>

                                @foreach ($categories as $group => $cats)
                                    @foreach ($cats as $id => $category)
                                    @if(isset($faq) && $faq->category_id == $id) {{ $category['title'] }}@endif
                                        @if ( ! empty($category['subs']))
                                            @foreach ($category['subs'] as $sub_id => $subcategory)
                                                @if(isset($faq) && $faq->category_id == $sub_id)     {{ $category['title'] . ' >> ' . $subcategory['title'] }}}@endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endforeach
                            </td>
                            <td class="text-right font-size-sm">
                                <a class="btn btn-sm btn-alt-secondary" href="{{ route('faqs.edit', ['faq' => $faq]) }}">
                                    <i class="fa fa-fw fa-pencil-alt"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="2">{{ __('back/faq.nema') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $faqs->links() }}
            </div>
        </div>
    </div>
@endsection

@push('js_after')

@endpush
