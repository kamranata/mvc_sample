@extends('admin._layouts.admin')

@section('title', __('Announcement types'))

@section('content')

    <div class="intro-y flex justify-between items-center mt-3">
        <h2 class="text-lg font-medium">@lang('Announcement types')</h2>

        <a href="{{ route('admin.vacancy_types.create') }}" class="btn btn-primary shadow-md">
            <i data-feather="plus-circle" class="w-5 h-5 mr-2"></i> @lang('Add new')
        </a>
    </div>

    <div class="intro-y box p-3 mt-5">
        <form action="{{ route('admin.vacancy_types.index') }}" method="get">
            <div class="grid sm:grid-cols-2 lg:grid-cols-10 gap-5 mb-5">
                <div class="sm:col-span-1 lg:col-span-2">
                    <select name="filter[status]" data-papi="status" class="tail-select w-full">
                        <option value="">&ndash; @lang('Status') &ndash;</option>
                        <option value="1" {{ (Request::input('filter.status') === '1' ? 'selected' : '') }}>@lang('Active')</option>
                        <option value="0" {{ (Request::input('filter.status') === '0' ? 'selected' : '') }}>@lang('Deactive')</option>
                    </select>
                </div>

                <div class="sm:col-span-2 text-right lg:text-left">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="filter" class="w-5 h-5 mr-2"></i> @lang('Filter')
                    </button>

                    <button type="submit" name="action" value="export" class="btn btn-primary">
                        <i data-feather="download" class="w-5 h-5 mr-2"></i> @lang('Export')
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($vacancy_types->count() > 0)
    <div class="intro-y box my-6 overflow-x-auto">
        <table class="table table--sm">
            <thead>
                <tr class="bg-gray-100 text-sm text-gray-600 uppercase">
                    <th width="50">@lang('ID')</th>
                    <th>@lang('Name')</th>
                    <th width="150" class="text-right">@lang('Status')</th>
                    <th width="100">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vacancy_types as $vacancy_type)
                <tr class="whitespace-nowrap border-b">
                    <td class="font-bold">{{ $vacancy_type->id }}</td>
                    <td>
                        <a href="{{ route('admin.vacancy_types.edit', $vacancy_type) }}" class="text-theme-1 font-medium">{{ $vacancy_type->title }}</a>
                    </td>
                    <td class="text-right">{{ $vacancy_type->status_formatted }}</td>
                    <td class="text-right">
                        <div class="flex items-center">
                            <form method="post" action="{{ route('admin.vacancy_types.destroy', $vacancy_type) }}" onsubmit="return confirm('@lang('Are you sure to delete?')');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm mr-2 flex items-center">
                                    <i data-feather="trash" class="w-4 h-4 mr-2"></i> @lang('Delete')
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="intro-y my-5 alert alert-dark show">
            <i data-feather="alert-triangle" class="w-6 h-6 mr-2"></i> @lang('No results found')
        </div>
    @endif

    <div class="intro-y mt-10">
        {{ $vacancy_types->appends( Request::all() )->links() }}
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    pApi.init({
        country: {
            route: '{{ route('api.countries.index') }}',
            defaultText: '&ndash; @lang('Country') &ndash;',
            selectedValue: '{{ Request::input('filter.country_id') }}'
        },
        city: {
            route: '{{ route('api.cities.index') }}',
            defaultText: '&ndash; @lang('City') &ndash;',
            selectedValue: '{{ Request::input('filter.city_id') }}'
        },
        company: {
            route: '{{ route('admin.companies.json') }}',
            defaultText: '&ndash; @lang('Company') &ndash;',
            selectedValue: '{{ Request::input('filter.company_id') }}'
        },
        vacancy: {
            route: '{{ route('admin.vacancies.json') }}',
            defaultText: '&ndash; @lang('Vacancy') &ndash;',
            selectedValue: '{{ Request::input('filter.vacancy_id') }}'
        },
        type: {
            route: '{{ route('admin.vacancy_types.json') }}',
            defaultText: '&ndash; @lang('Type') &ndash;',
            selectedValue: '{{ Request::input('filter.type_id') }}'
        }
    });
</script>
@endpush