@extends('backend.layouts.app')

@section('content')
    @if(env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
        <div class="">
            <div class="alert alert-danger d-flex align-items-center">
                {{translate('Please Configure SMTP Setting to work all email sending functionality')}},
                <a class="alert-link ml-2"
                   href="{{ route('smtp_settings.index') }}">{{ translate('Configure Now') }}</a>
            </div>
        </div>
    @endif
    @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
        <form class="" action="" id="dashboard_filter" method="GET">
            <div class="row gutters-10">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="col-lg-4">
                                <div class="form-group mb-0">
                                    <input type="text" class="aiz-date-range form-control"
                                           value="{{$from}} to {{$to}}"
                                           name="date"
                                           id="filter_date" placeholder="{{ translate('Filter by date') }}"
                                           data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true"
                                           autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row gutters-10">
            <div class="col-lg-3 col-6">
                <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            <span class="fs-12 d-block">{{ translate('Total Customers') }}</span>
                        </div>
                        <div class="h3 fw-700 mb-3">
                            {{ $cached_data['total_customers'] }}
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                              d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                    </svg>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="bg-grad-4 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            <span class="fs-12 d-block">{{ translate('Total Orders') }}</span>
                        </div>
                        <div class="h3 fw-700 mb-3">{{ $cached_data['total_orders'] }}</div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                              d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                    </svg>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            <span class="fs-12 d-block">{{ translate('Total Sales') }}</span>
                        </div>
                        <div class="h3 fw-700 mb-3">{!! single_price($cached_data['total_sales'])  !!}</div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                              d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                    </svg>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            <span class="fs-12 d-block">{{ translate('Total Pending Payment') }}</span>
                        </div>
                        <div class="h3 fw-700 mb-3">{!! single_price($cached_data['total_pending_payment'])  !!}</div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                              d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="row gutters-10">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fs-14">{{ translate('Summary') }}</h6>
                    </div>
                    <div class="card-body">
                        <table class="table aiz-table mb-0">
                            <thead>
                            <tr>
                                <th>{{ translate('Community') }}</th>
                                <th data-breakpoints="md" class="text-center">{{ translate('Total Customers') }}</th>
                                <th data-breakpoints="md" class="text-center">Total Orders</th>
                                <th data-breakpoints="md" class="text-right">{{ translate('Total Sales') }}</th>
                                <th data-breakpoints="md" class="text-right">{{ translate('Deliveried') }}</th>
                                <th data-breakpoints="md" class="text-right">{{ translate('Pending Deliveries') }}</th>
                                <th data-breakpoints="md" class="text-right">{{ translate('Total Pending Payment') }}</th>
                                <th data-breakpoints="md" class="text-right">{{ translate('Average Order Value') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($cached_data['community_data'] as $key => $community_data)
                                @php
                                    $aov = 0;
                                    if ($community_data->orders->sum('grand_total') > 0 && $community_data->orders->count() > 0) {
                                        $aov = floatval($community_data->orders->sum('grand_total') / $community_data->orders->count());
                                    }

                                    $pendingDel = $community_data->orders->count() - $community_data->delivered_orders->count();
                                @endphp
                                <tr>
                                    <td>
                                        {{ $community_data->name }}
                                    </td>
                                    <td class="text-center">
                                        {{ $community_data->customers_count }}
                                    </td>
                                    <td class="text-center">
                                        {{ $community_data->orders->count() }}
                                    </td>
                                    <td class="text-right text-nowrap">
                                        {!! single_price($community_data->orders->sum('grand_total'))  !!}
                                    </td>
                                    <td class="text-right">
                                        {{ $community_data->delivered_orders->count() }}
                                    </td>
                                    <td class="text-right">
                                        {{ $pendingDel }}
                                    </td>
                                    <td class="text-right text-nowrap">
                                        {!! single_price($community_data->unpaid_orders->sum('grand_total'))  !!}
                                    </td>
                                    <td class="text-right text-nowrap">
                                        {!! single_price(round($aov, 2))  !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
        <div class="row gutters-10">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="mb-2 fs-14">Sales Chart</h6>
                            </div>
                            <div class="col-md-12">
                                <select id="sales_line_chart_year" class="from-control aiz-selectpicker" name="sales_line_chart_year">
                                    <option value="{{ date("Y", strtotime("-3 year")) }}">{{ date("Y", strtotime("-3 year")) }}</option>
                                    <option value="{{ date("Y", strtotime("-2 year")) }}">{{ date("Y", strtotime("-2 year")) }}</option>
                                    <option value="{{ date("Y", strtotime("-1 year")) }}">{{ date("Y", strtotime("-1 year")) }}</option>
                                    <option value="{{ date("Y") }}" @if($cur_year == date("Y")) selected @endif > {{ date("Y") }}</option>
                                </select>
                                <select id="sales_line_chart_type" class="from-control aiz-selectpicker" name="sales_line_chart_type">
                                    <option value="month">Monthly</option>
                                    <option value="week">Weekly</option>
                                </select>
                                <button type="button" class="btn btn-primary" onclick="filterSalesLineChart()"> Filter </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="sales_line_chart" class="w-100" height="350"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="mb-2 fs-14">Customer Acquisition</h6>
                            </div>
                            <div class="col-md-12 text-right">
                                <select id="customer_bar_chart_year" class="from-control aiz-selectpicker" name="customer_bar_chart_year">
                                    <option value="{{ date("Y", strtotime("-3 year")) }}">{{ date("Y", strtotime("-3 year")) }}</option>
                                    <option value="{{ date("Y", strtotime("-2 year")) }}">{{ date("Y", strtotime("-2 year")) }}</option>
                                    <option value="{{ date("Y", strtotime("-1 year")) }}">{{ date("Y", strtotime("-1 year")) }}</option>
                                    <option value="{{ date("Y") }}" @if($cur_year == date("Y")) selected @endif > {{ date("Y") }}</option>
                                </select>
                                <!--                            <select id="sales_line_chart_type" class="from-control aiz-selectpicker" name="sales_line_chart_type">
                                                                <option value="month">Monthly</option>
                                                                <option value="week">Weekly</option>
                                                            </select>-->
                                <button type="button" class="btn btn-primary" onclick="filterCustomerStackBarChart()"> Filter </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="user_monthly_bar_chart" class="w-100" height="350"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
        <div class="row gutters-10">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fs-14">{{ translate('Category wise product sale') }}</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="graph-1" class="w-100" height="500"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">{{ translate('Top 12 Products') }}</h6>
        </div>
        <div class="card-body">
            <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5" data-lg-items="4"
                 data-md-items="3" data-sm-items="2" data-arrows='true'>
                @foreach (filter_products(\App\Models\Product::where('published', 1)->orderBy('num_of_sale', 'desc'))->limit(12)->get() as $key => $product)
                    <div class="carousel-box">
                        <div class="aiz-card-box border border-light rounded shadow-sm hov-shadow-md mb-2 has-transition bg-white">
                            <div class="position-relative">
                                <a href="{{ route('wholesale_product_edit.admin', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] ) }}"
                                   class="d-block">
                                    <img
                                            class="img-fit lazyload mx-auto h-210px"
                                            src="{{ static_asset('assets/img/no-image-found.jpg') }}"
                                            data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                            alt="{{  $product->getTranslation('name')  }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                    >
                                </a>
                            </div>
                            <div class="p-md-3 p-2 text-left">
                                <div class="rating rating-sm mt-1">
                                    {{ renderStarRating($product->rating) }}
                                </div>
                                <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0">
                                    <a href="{{ route('wholesale_product_edit.admin', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] ) }}"
                                       class="d-block text-reset">{{ $product->getTranslation('name') }}</a>
                                </h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script type="text/javascript">
        let salesOrderLineChart, customerStackBarChart;

        AIZ.plugins.chart('#graph-1', {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($root_categories as $key => $category)
                        '{{ $category->getTranslation('name') }}',
                    @endforeach
                ],
                datasets: [{
                    label: '{{ translate('Number of sale') }}',
                    data: [
                        {{ $cached_data['num_of_sale_data'] }}
                    ],
                    backgroundColor: [
                        @foreach ($root_categories as $key => $category)
                            'rgba(55, 125, 255, 0.4)',
                        @endforeach
                    ],
                    borderColor: [
                        @foreach ($root_categories as $key => $category)
                            'rgba(55, 125, 255, 1)',
                        @endforeach
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        gridLines: {
                            color: '#f2f3f8',
                            zeroLineColor: '#f2f3f8'
                        },
                        ticks: {
                            fontColor: "#8b8b8b",
                            fontFamily: 'Poppins',
                            fontSize: 10,
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            color: '#f2f3f8'
                        },
                        ticks: {
                            fontColor: "#8b8b8b",
                            fontFamily: 'Poppins',
                            fontSize: 10
                        }
                    }]
                },
                legend: {
                    labels: {
                        fontFamily: 'Poppins',
                        boxWidth: 10,
                        usePointStyle: true
                    },
                    onClick: function () {
                        return '';
                    },
                }
            }
        });

        filterSalesLineChart();

        filterCustomerStackBarChart();

        function filterSalesLineChart() {
            let chartId = 'sales_line_chart';
            $.ajax({
                type:"POST",
                url: '{{ route('admin.sales_line_chart') }}',
                data: {year: $('#sales_line_chart_year').val(), chart_type: $('#sales_line_chart_type').val(), _token: AIZ.data.csrf},
                success: function(data){
                    initSalesLineChart(chartId, data.data);
                }
            });
        }

        function initSalesLineChart(chartId, data) {
            if (data) {
                if (typeof (salesOrderLineChart) !== 'undefined') {
                    salesOrderLineChart.destroy();
                }

                salesOrderLineChart = new Chart($('#'+chartId), {
                    type: 'line',
                    data: data,
                    options: {
                        responsive: true,
                        elements: {
                            line: {
                                tension: 0
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                title: function (t, d) {
                                    return 'Sales: ' + d.datasets[0]['extraData'][t[0].index];
                                },
                                label: function (t, d) {
                                    return '';
                                }
                            }
                        }
                    },
                });
            }
        }

        function filterCustomerStackBarChart() {
            $.ajax({
                type:"POST",
                url: '{{ route('admin.customer_bar_chart') }}',
                data: {year: $('#customer_bar_chart_year').val(), chart_type: 'month', _token: AIZ.data.csrf},
                success: function(data){
                    initCustomerStackBarChart('user_monthly_bar_chart', data.data);
                }
            });
        }

        function initCustomerStackBarChart(chartId, data) {
            if (data) {
                if (typeof (customerStackBarChart) !== 'undefined') {
                    customerStackBarChart.destroy();
                }

                customerStackBarChart = new Chart($('#'+chartId), {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Chart.js Bar Chart - Stacked'
                            },
                        },
                        scales: {
                            x: {
                                stacked: true,
                            },
                            y: {
                                stacked: true
                            },
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    },
                });
            }
        }

        $('#filter_date').on('apply.daterangepicker', function (ev, picker) {
            $('form#dashboard_filter').submit();
        });
    </script>
@endsection
