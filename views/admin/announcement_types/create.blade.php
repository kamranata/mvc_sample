@extends('admin._layouts.admin')

@section('title', __('New vacancy type'))

@section('content')

<form role="form" method="post" action="{{ route('admin.vacancy_types.store') }}" autocomplete="off">
    @csrf

    <div class="intro-y grid grid-cols-12 gap-5 mt-5">
        <div class="col-span-12 lg:col-span-6 box">

            <div class="post intro-y overflow-hidden">
                <div class="post__tabs nav nav-tabs flex-col sm:flex-row bg-gray-100 dark:bg-dark-2 text-gray-600 border-b" role="tablist">
                    @foreach($locales as $code)
                        <a data-toggle="tab" data-target="#locale-{{ $code }}" href="javascript:;" class="w-full sm:w-40 py-2 text-center flex justify-center items-center {{ ($code == $default_locale ? 'active' : '') }}" id="locale-{{ $code }}-tab" role="tab" aria-controls="locale-{{ $code }}" aria-selected="{{ $code == $default_locale }}">
                        @lang('locale_' . $code)
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="post__content tab-content">
                @foreach($locales as $code)
                <div id="locale-{{ $code }}" class="tab-pane p-5 {{ ($code == $default_locale ? 'active' : '') }}" role="tabpanel" aria-labelledby="locale-{{ $code }}-tab">
                    <div class="mb-3">
                        <label class="form-label">@lang('Title'):</label>
                        <input type="text" name="{{ $code }}[title]" value="{{ old($code . '.title') }}" class="form-control">
                    </div>
                </div>
                @endforeach
            </div>

            <div class="p-5 border-t border-gray-200 grid grid-cols-2 gap-5 items-end">
                <div>
                    <label class="form-label">@lang('Ordering'):</label>
                    <input type="text" name="ordering" value="{{ old('ordering') }}" class="form-control">
                </div>
                <div class="form-check">
                    <input id="changeable" name="changeable" value="1" class="form-check-switch" type="checkbox" {{ old('changeable') ? 'checked' : '' }}>
                    <label class="form-check-label" for="changeable">@lang('Changable')</label>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-6 intro-y box overflow-x-auto">
                <table class="table table--sm">
                    <thead>
                        <tr class="bg-gray-100 text-sm text-gray-600 uppercase">
                            <th>@lang('Country')</th>
                            <th>@lang('Amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($countries as $country)
                        <tr>
                            <td>
                                <label for="country-{{ $country->id }}" class="form-label">{{ $country->name }}:</label>
                                <input type="hidden" name="country_id" value="{{ $country->id }}">
                            </td>
                            <td>
                                <input id="country-{{ $country->id }}" type="text" name="amount[{{ $country->id }}]" value="{{ old('amount', $values[$country->id] ?? 0) }}" class="form-control">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    </div>

    <div class="box p-5 mt-5">
        <div class="flex justify-between">
            <div class="form-check">
                <input id="status" name="status" value="1" class="form-check-switch" type="checkbox" {{ old('status') ? 'checked' : '' }}>
                <label class="form-check-label" for="status">@lang('Active')</label>
            </div>
            <div class="flex gap-5">
                <a href="{{ route('admin.vacancy_types.index') }}" class="btn btn-default mr-2">@lang('Cancel')</a>
                <button type="submit" class="btn btn-primary">@lang('Create')</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script type="text/javascript">
    pApi.init({
        country: {
            route: '{{ route('api.countries.index') }}',
            defaultText: '&ndash; @lang('Select') &ndash;',
            selectedValue: 1
        },
        city: {
            route: '{{ route('api.cities.index') }}',
            defaultText: '&ndash; @lang('Select') &ndash;',
            selectedValue: 1
        },
        company: {
            route: '{{ route('admin.companies.json') }}',
            defaultText: '&ndash; @lang('Select') &ndash;',
            selectedValue: '{{ old('company_id') }}'
        },
        vacancy: {
            route: '{{ route('admin.vacancies.json') }}',
            defaultText: '&ndash; @lang('Select') &ndash;',
            selectedValue: '{{ old('vacancy_id') }}'
        }
    });
</script>
@endpush