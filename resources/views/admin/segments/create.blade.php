@extends('admin.layout')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Reports' => '/admin/segments',
        'New Report Segment' => '',
    ]])
    @endcomponent

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h5>New Segment</h5>

    <hr>
    
    <div class="row mb-5" id="filterBuilder">
        <div class="col-md-8">
            <form action="/admin/segments" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input maxlength="254" type="text" id="name" name="name" class="form-control" required style="max-width: 400px;">
                </div>
                <div class="form-group">
                    <label for="name">Date Range</label>
                    <input type="text" name="daterange" value="{{ Carbon\Carbon::now()->subDays(30)->format('m/d/Y') }} - {{ Carbon\Carbon::now()->format('m/d/Y') }}" class="form-control" style="width: 400px;" />
                    <input type="hidden" name="start_date" id="startDate">
                    <input type="hidden" name="end_date" id="endDate">
                </div>
                <hr>
                <div class="form-group">
                    <label for="description">Groups</label>
                    <div class="form-check mb-2">
                      <input class="form-check-input" type="checkbox" id="checkAll" checked="true">
                      <label class="form-check-label" for="checkAll">All</label>
                    </div>
                    @foreach($groups as $group)
                        @include('partials.groupcheckbox', ['group' => $group, 'checked' => true, 'segment' => false])
                    @endforeach
                </div>

                <hr>

                {{--
                <p class="font-weight-bold">Filters</p>

                <div class="filters">
                    <div v-for="(filter, index) in filterObject">
                        <div class="form-row mb-3 filter-row">
                            <div class="col-md-4">
                                <select class="custom-select" :name="'filters['+index+'][object]'" v-model="filter.type">
                                    <option v-for="type in types" v-bind:value="type.name">@{{ type.display }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="custom-select" :name="'filters['+index+'][expression]'" v-model="filter.operator">
                                    <option value="contains">contains</option>
                                    <option value="does-not-contain">does not contain</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <select class="custom-select" :name="'filters['+index+'][parameter]'" v-model="filter.value" v-if="doesTypeHaveOptions(filter.type)" required>
                                        <option v-for="option in getTypeOptions(filter.type)" v-bind:value="option">@{{ option }}</option>
                                    </select>
                                    <input type="text" v-if="!doesTypeHaveOptions(filter.type)" class="form-control" :name="'filters['+index+'][parameter]'" v-model="filter.value">
                                    <button @click.prevent="removeFilter(index)" class="btn btn-secondary ml-2 delete-button"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn btn-secondary" @click.prevent="addFilter()"><i class="fa fa-plus"></i> Add filter</button>
                </div>
                --}}

                <button type="submit" class="btn btn-primary">@lang('general.save')</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $('#checkAll').on('change', function (event) {
            console.log(event.target.getAttribute('checked'));
            $('input[type=checkbox]').prop('checked', event.target.checked);
        });
        $('input[type=checkbox]').on('change', function (event) {
            if (event.target.id != 'checkAll') {
                $(this).parent().parent().find('input[type=checkbox]').prop('checked', event.target.checked);
                if (!event.target.checked)
                    $('#checkAll').prop('checked', false);
            }
        });
        var startDate = '{{ Carbon\Carbon::now()->subDays(30)->format('m/d/Y') }}';
        var endDate = '{{ Carbon\Carbon::now()->format('m/d/Y') }}';

        $(function() {
          $('input[name="daterange"]').daterangepicker({
            opens: 'right',
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
          }, function(start, end, label) {
            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
            setInputs();
          });
          setInputs();
        });

        function setInputs() {
            $('#startDate').val(startDate);
            $('#endDate').val(endDate);
        }
    </script>
@endsection