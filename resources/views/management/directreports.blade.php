@extends('layouts.app')

@section('stylesheets')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
<div style="background-color: #565d6b;">
  <div class="container-fluid py-3" style="position: relative;">
    <a href="/home" class="d-inline-block mb-2" style="font-size: 14px; position: absolute; top: 50%; color: #fff; transform: translateY(-50%);"><i class="icon-chevron-small-right"></i> @lang('messages.my-dashboard')</a>
    <h4 class="mb-0 text-center no-underline" style="color: #fff;">Management Dashboard</h4>
  </div>
</div>
<div class="main-container bg-lightest-brand py-4">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card"> 
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <a href="/management/my-direct-reports" class="btn btn-secondary">My Direct Reports</a>
                <a href="/management/organization" class="btn btn-outline-secondary">My Organization</a>
              </div>
              <input type="text" name="daterange" value="{{ Carbon\Carbon::now()->subDays(30)->format('m/d/Y') }} - {{ Carbon\Carbon::now()->format('m/d/Y') }}" class="form-control" style="width: 300px;" />
            </div>
            <hr>
            <h4 class="card-title">Building Networks &amp; Community</h4>
            <hr class="my-2">
            <div class="row">
              <div class="col-md-6 mb-3">
                <p class="text-center font-weight-bold">Three-Way Introductions Made</p>
                <canvas id="introductionsMade" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6 mb-3">
                <p class="text-center font-weight-bold">Three-Way Introductions Responded To</p>
                <canvas id="introductionsRespondedTo" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6">
                <p class="text-center font-weight-bold">Shoutouts Given</p>
                <canvas id="shoutoutsMade" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="/management/breakdowns/shoutouts-made">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6">
                <p class="text-center font-weight-bold">Shoutouts Received</p>
                <canvas id="shoutoutsReceived" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
            </div>
          </div>
        </div>


        <div class="card"> 
          <div class="card-body">
            <h4 class="card-title">Growing Others</h4>
            <hr class="my-2">
            <div class="row">
              <div class="col-md-6 mb-3">
                <p class="text-center font-weight-bold">Mentoring</p>
                <canvas id="mentorStatusChart" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6 mb-3">
                <p class="text-center font-weight-bold">Skillsets Offered Per Direct Report</p>
                <canvas id="skillsetsOfferedPerDirectReport" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6">
                <p class="text-center font-weight-bold">Mentoring Skillsets</p>
                <canvas id="mentorSkillsets" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6">
                <p class="text-center font-weight-bold">Seeking Mentorship</p>
                <canvas id="seekingMentorship" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
            </div>
          </div>
        </div>

        <div class="card"> 
          <div class="card-body">
            <h4 class="card-title">One-on-one Engagement</h4>
            <hr class="my-2">
            <div class="row justify-content-center">
              <div class="col-md-6">
                <p class="text-center font-weight-bold">Messages Sent</p>
                <canvas id="messagesSentChart" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
            </div>
          </div>
        </div>

        <!-- <div class="card"> 
          <div class="card-body">
            <h4 class="card-title">Helping Chart the Course</h4>
            <hr class="my-2">
            <div class="row">
              <div class="col-md-6">
                <p class="text-center font-weight-bold">Ideations</p>
              </div>
            </div>
          </div>
        </div> -->

        <div class="card"> 
          <div class="card-body">
            <h4 class="card-title">Get To Know Your Workforce</h4>
            <hr class="my-2">
            <div class="row">
              <div class="col-md-6 mb-4">
                <p class="text-center font-weight-bold">Breakdown by Group</p>
                <canvas id="byGroupChart" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6 mb-4">
                <p class="text-center font-weight-bold">Breakdown by Department</p>
                <canvas id="byDepartmentsChart" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6 mb-4">
                <p class="text-center font-weight-bold">Top Interests</p>
                <canvas id="topKeywordsChart" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
              <div class="col-md-6 mb-4">
                <p class="text-center font-weight-bold">Top Skillsets</p>
                <canvas id="topSkillsetsChart" width="100%" height="80"></canvas>
                <p class="mb-0 text-center"><a href="#">Breakdown details <i class="icon-chevron-small-right"></i></a></p>
              </div>
            </div>
          </div>
        </div>

      </div>
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

    var backgroundColors = [
      "rgb(255, 108, 85)",
      "rgb(29, 102, 191)",
      "rgb(128, 169, 92)",
      "rgb(233, 108, 34)",
      "rgb(211,  36, 72)",
      "rgb(26,  189, 187)",
      "rgba(255, 108, 85,  0.75)",
      "rgba(29,  102, 191, 0.75)",
      "rgba(128, 169, 92,  0.75)",
      "rgba(233, 108, 34,  0.75)",
      "rgba(211,  36, 72,  0.75)",
      "rgba(26,  189, 187, 0.75)",
      "rgba(255, 108, 85,  0.5)",
      "rgba(29,  102, 191, 0.5)",
      "rgba(128, 169, 92,  0.5)",
      "rgba(233, 108, 34,  0.5)",
      "rgba(211,  36, 72,  0.5)",
      "rgba(26,  189, 187, 0.5)",
      "rgba(255, 108, 85,  0.35)",
      "rgba(29,  102, 191, 0.35)",
      "rgba(128, 169, 92,  0.35)",
      "rgba(233, 108, 34,  0.35)",
      "rgba(211,  36, 72,  0.35)",
      "rgba(26,  189, 187, 0.35)",
      "rgba(255, 108, 85,  0.20)",
      "rgba(29,  102, 191, 0.20)",
      "rgba(128, 169, 92,  0.20)",
      "rgba(233, 108, 34,  0.20)",
      "rgba(211,  36, 72,  0.20)",
      "rgba(26,  189, 187, 0.20)",
      "rgba(255, 108, 85,  0.05)",
      "rgba(29,  102, 191, 0.05)",
      "rgba(128, 169, 92,  0.05)",
      "rgba(233, 108, 34,  0.05)",
      "rgba(211,  36, 72,  0.05)",
      "rgba(26,  189, 187, 0.05)",
    ]
    
    var messagesSentChart = new Chart(document.getElementById('messagesSentChart'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($messagesSent->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
          }],
          labels: JSON.parse('{!! json_encode($messagesSent->pluck('name')) !!}'),
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
    var introductionsMade = new Chart(document.getElementById('introductionsMade'), {
      type: 'bar',
      data: {
         datasets: [{
              data: JSON.parse('{!! json_encode($introductionsMade->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
          }],
          labels: JSON.parse('{!! json_encode($introductionsMade->pluck('name'), JSON_HEX_APOS) !!}'),
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
    var introductionsRespondedTo = new Chart(document.getElementById('introductionsRespondedTo'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($introductionsRespondedTo->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
          }],
          labels: JSON.parse('{!! json_encode($introductionsRespondedTo->pluck('name'), JSON_HEX_APOS) !!}'),
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
    var shoutoutsMade = new Chart(document.getElementById('shoutoutsMade'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($shoutoutsMade->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
          }],
          labels: JSON.parse('{!! json_encode($shoutoutsMade->pluck('name'), JSON_HEX_APOS) !!}'),
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
    var shoutoutsReceived = new Chart(document.getElementById('shoutoutsReceived'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($shoutoutsReceived->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
          }],
          labels: JSON.parse('{!! json_encode($shoutoutsReceived->pluck('name'), JSON_HEX_APOS) !!}'),
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
    var mentorStatusChart = new Chart(document.getElementById('mentorStatusChart'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: JSON.parse('{!! json_encode($mentorBreakdown->pluck('count')) !!}'),
                    backgroundColor: backgroundColors,
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
    var skillsetsOfferedPerDirectReport = new Chart(document.getElementById('skillsetsOfferedPerDirectReport'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($skillsetsPerPerson->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
          }],
          labels: JSON.parse('{!! json_encode($skillsetsPerPerson->pluck('name'), JSON_HEX_APOS) !!}'),
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
                  beginAtZero: true
              }
          }]
        }
      }
    });
    var mentorSkillsets = new Chart(document.getElementById('mentorSkillsets'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($mentorSkillsets->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
          }],
          labels: JSON.parse('{!! json_encode($mentorSkillsets->pluck('name'), JSON_HEX_APOS) !!}'),
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
    var seekingMentorship = new Chart(document.getElementById('seekingMentorship'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($seekingMentorship->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
          }],
          labels: JSON.parse('{!! json_encode($seekingMentorship->pluck('name'), JSON_HEX_APOS) !!}'),
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
                    beginAtZero: true
                }
            }]
          }
      }
    });
    var groupChart = new Chart(document.getElementById('byGroupChart'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($groupData->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
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
    var byDepartmentsChart = new Chart(document.getElementById('byDepartmentsChart'), {
      type: 'bar',
      data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($departmentBreakdown->pluck('count')) !!}'),
              backgroundColor: backgroundColors,
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
    var topKeywordsChart = new Chart(document.getElementById('topKeywordsChart'), {
        type: 'bar',
        data: {
            datasets: [{
                data: JSON.parse('{!! json_encode($keywordBreakdown->pluck('count')) !!}'),
                backgroundColor: backgroundColors,
            }],
            labels: JSON.parse('{!! json_encode($keywordBreakdown->pluck('name')) !!}'),
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
    var topSkillsetsChart = new Chart(document.getElementById('topSkillsetsChart'), {
        type: 'bar',
        data: {
            datasets: [{
                data: JSON.parse('{!! json_encode($skillsBreakdown->pluck('count')) !!}'),
                backgroundColor: backgroundColors,
            }],
            labels: JSON.parse('{!! json_encode($skillsBreakdown->pluck('name')) !!}'),
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

   $(function() {
      $('input[name="daterange"]').daterangepicker({
        opens: 'left',
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
        // loadGraph();
      });

    });
  </script>
@endsection