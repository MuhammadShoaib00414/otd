@extends('groups.layout')

@section('stylesheets')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('inner-content')
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">@lang('general.reports')</h3>
  </div>
  <div class="mb-2">
    <a href="/groups/{{ $group->slug }}/reports/demographics" class="btn btn-secondary mr-2">@lang('reports.demographics')</a>
    <a href="/groups/{{ $group->slug }}/reports/behavior" class="">@lang('general.behavior')</a>
  </div>
    <div class="card">
        <div class="card-body">
            <p class="font-weight-bold mb-0 text-center">@lang('reports.breakdown_by_group')</p>
            <canvas id="byGroupChart" width="100%" height="80"></canvas>
            <p class="mb-0 text-center"><a href="/groups/{{ $group->slug }}/reports/demographics/groups">@lang('reports.group_breakdown_details') <i class="fas fa-angle-right"></i></a></p>
        </div>
    </div>
    @if($is_departments_enabled)
    <div class="card">
        <div class="card-body">
            <p class="font-weight-bold mb-0 text-center">@lang('reports.breakdown_by_department')</p>
            <canvas id="byDepartmentsChart" width="100%" height="80"></canvas>
            <p class="mb-0 text-center"><a href="/groups/{{ $group->slug }}/reports/demographics/departments">@lang('reports.department_breakdown_details') <i class="fas fa-angle-right"></i></a></p>
        </div>
    </div>
    @endif
    @foreach($taxonomies as $taxonomy)
    <div class="card">
        <div class="card-body">
            <p class="font-weight-bold mb-0 text-center">Top {{ $taxonomy->name }}</p>
            <canvas id="taxonomyChart{{ $taxonomy->id }}" width="100%" height="80"></canvas>
            <p class="mb-0 text-center"><a href="/groups/{{ $group->slug }}/reports/demographics/taxonomies/{{ $taxonomy->id }}">{{ $taxonomy->name }} @lang('reports.breakdown_details') <i class="fas fa-angle-right"></i></a></p>
        </div>
    </div>
    @endforeach
    <div class="card">
        <div class="card-body">
            <p class="font-weight-bold mb-0 text-center">Mentor Status</p>
            <canvas id="mentorStatusChart" width="100%" height="80"></canvas>
            <p class="mb-0 text-center"><a href="/groups/{{ $group->slug }}/reports/demographics/mentors">@lang('reports.mentors_breakdown_details') <i class="fas fa-angle-right"></i></a></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="font-weight-bold mb-0 text-center">Made an Introduction</p>
            <canvas id="introductionMadeChart" width="100%" height="80"></canvas>
            <p class="mb-0 text-center"><a href="/groups/{{ $group->slug }}/reports/demographics/introductions">@lang('reports.introductions_breakdown_details') <i class="fas fa-angle-right"></i></a></p>
        </div>
    </div>
    @if($is_management_chain_enabled)
        <hr>
        <h4>Management Chain</h4>
        @foreach($titles as $title)
            <div class="card">
                <div class="card-body">
                    <p class="font-weight-bold mb-0 text-center">{{ $title->name }}</p>
                    <p class="mb-0 text-center">(# @lang('reports.active_users_based_on') {{ $title->name }})</p>
                    <canvas id="ManagementChainChart{{ $title->id }}" width="100%" height="80"></canvas>
                    <p class="mb-0 text-center"><a href="/groups/{{ $group->slug }}/reports/demographics/titles/{{ $title->id}}">{{ $title->name }} @lang('reports.breakdown_details') <i class="fas fa-angle-right"></i></a></p>
                </div>
            </div>
        @endforeach
    @endif
        </div>
    </div>
  </div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
    <script>
        var myDoughnutChart = new Chart(document.getElementById('byGroupChart'), {
            type: 'bar',
            data: {
                datasets: [{
                    data: JSON.parse('{!! json_encode($groupData->pluck('count')) !!}'),
                    backgroundColor: [
                        "rgb(244,143,130)",
                        "rgb(29, 102, 191, 0.8)",
                        "rgb(29, 102, 191, 0.6)",
                        "rgb(29, 102, 191, 0.4)",
                        "rgb(29, 102, 191, 0.2)",
                    ],
                }],
                labels: $.parseJSON('{!! str_replace("'", "\\'", json_encode($groupData->pluck('name'))) !!}'),
            },
            options: {
                responsive: true,
                legend: {
                    display: false,
                },
                title: {
                    display: false,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                        }
                    }]
                },
            }
        });
        @foreach($taxonomies as $taxonomy)
            var taxonomyChart{{ $taxonomy->id }} = new Chart(document.getElementById('taxonomyChart{{ $taxonomy->id }}'), {
                type: 'bar',
                data: {
                    datasets: [{
                        data: JSON.parse('{!! json_encode($taxonomy->chartData->pluck('count')) !!}'),
                        backgroundColor: [
                            "rgb(244,143,130)",
                            "rgb(29, 102, 191, 1)",
                            "rgb(29, 102, 191, 0.9)",
                            "rgb(29, 102, 191, 0.8)",
                            "rgb(29, 102, 191, 0.7)",
                            "rgb(29, 102, 191, 0.6)",
                            "rgb(29, 102, 191, 0.5)",
                            "rgb(29, 102, 191, 0.4)",
                            "rgb(29, 102, 191, 0.3)",
                            "rgb(29, 102, 191, 0.2)",
                        ],
                    }],
                    labels: JSON.parse('{!! json_encode($taxonomy->chartData->pluck("name"), JSON_HEX_QUOT   | JSON_HEX_APOS) !!}'),
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    }
                }
            });
        @endforeach
        @if($is_departments_enabled)
        var myBarChart = new Chart(document.getElementById('byDepartmentsChart'), {
            type: 'bar',
            data: {
                datasets: [{
                    data: JSON.parse('{!! json_encode($departmentBreakdown->pluck('count')) !!}'),
                    backgroundColor: [
                        "rgb(244,143,130)",
                        "rgb(29, 102, 191, 0.8)",
                        "rgb(29, 102, 191, 0.6)",
                        "rgb(29, 102, 191, 0.4)",
                        "rgb(29, 102, 191, 0.2)",
                    ],
                    label: 'Dataset 1'
                }],
                labels: JSON.parse('{!! json_encode($departmentBreakdown->pluck('name')) !!}'),
            },
            options: {
                responsive: true,
                legend: {
                    display: false,
                },
                title: {
                    display: true,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                        }
                    }]
                }
            }
        });
        @endif
        var mentorStatusChart = new Chart(document.getElementById('mentorStatusChart'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: JSON.parse('{!! json_encode($mentorBreakdown->pluck('count')) !!}'),
                    backgroundColor: [
                        "rgb(244,143,130)",
                        "rgba(244,143,130, 0.5)",
                        "rgb(29, 102, 191, 0.6)",
                        "rgb(29, 102, 191, 0.4)",
                        "rgb(29, 102, 191, 0.2)",
                    ],
                }],
                labels: ['Is Mentor', 'Is Not Mentor'],
            },
            options: {
                responsive: true,
                legend: {
                    position: 'right',
                },
                title: {
                    display: false,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        var introductionMadeChart = new Chart(document.getElementById('introductionMadeChart'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $introductionsMade }}, {{ $totalCount - $introductionsMade }}],
                    backgroundColor: [
                        "rgb(244,143,130)",
                        "rgba(244,143,130, 0.5)",
                        "rgb(29, 102, 191, 0.6)",
                        "rgb(29, 102, 191, 0.4)",
                        "rgb(29, 102, 191, 0.2)",
                    ],
                }],
                labels: ['Made an introduction', 'Has not made an introduction'],
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
        @if($is_management_chain_enabled)
            @foreach($titles as $title)
            var myDoughnutChart = new Chart(document.getElementById('ManagementChainChart{{ $title->id }}'), {
                type: 'bar',
                data: {
                    datasets: [{
                        data: JSON.parse('{!! json_encode($title->stats->pluck('count')) !!}'),
                        backgroundColor: [
                            "rgb(244,143,130)",
                            "rgb(29, 102, 191, 0.8)",
                            "rgb(29, 102, 191, 0.6)",
                            "rgb(29, 102, 191, 0.4)",
                            "rgb(29, 102, 191, 0.2)",
                        ],
                    }],
                    labels: JSON.parse('{!! json_encode($title->stats->pluck('name')) !!}'),
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    }
                }
            });

            @endforeach
        @endif
    </script>
@endsection