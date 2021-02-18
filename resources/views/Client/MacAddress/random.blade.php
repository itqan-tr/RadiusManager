@extends('layouts.template')

@section('title',"View MAC Address")

@section('head')
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{asset('dist/app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('dist/app-assets/vendors/css/extensions/sweetalert.css')}}">
@stop

<!-- content-body -->
@section('content-body')

    <!-- Main -->
    <section id="main">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Devices Registered to {{ Auth::user()->username }}</h4>
                        <h5>Where do I find my device's MAC address?<a class="btn btn-link" target="_blank"
                                                                       href="http://{{ env('MAC_URL','www.wikihow.com/Find-the-MAC-Address-of-Your-Computer') }}">Click
                                here !</a></h5>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                    </div>
                    <div class="card-content collpase show">
                        <div class="card-body card-dashboard">
                            <div class="row">
                                <div class="col-md-12">
                                    <h1 align="center">Randomized MAC
                                        Support</h1>
                                    <p align="center">The
                                        platform has detected your device is using a randomized MAC. <br>This
                                        feature can result in a loss of some functionality while logging into
                                        WiFi so we recommend you disable this feature.
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <br/>
                                    <br/>
                                    <hr style="margin: 5px 0 !important;">
                                    <br/>
                                    <br/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h1 align="center">How to disable MAC address randomization on iOS 14 and up</h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <center>
                                        1. Open the Settings App<br>
                                        2. Tap Wi-Fi<br>
                                        3. Tap the info icon associated with this WiFi network<br>
                                        4. Turn off the toggle for 'Private Address'<br>
                                    </center>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 center">
                                    <center>
                                        <img src="{{ asset('images/ios_random_mac.png') }}" style="max-width: 682px;" width="100%">
                                    </center>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <br/>
                                    <br/>
                                    <hr style="margin: 5px 0 !important;">
                                    <br/>
                                    <br/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h1 align="center">How to disable MAC address randomization on Android 10 and up</h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <center>
                                        1. Open the Settings App<br/>
                                        2. Tap Network & Internet<br/>
                                        3. Tap Wi-Fi<br/>
                                        4. Tap the gear icon associated with this WiFi network<br/>
                                        5. Tap Advanced<br/>
                                        6. Tap Privacy<br/>
                                        7. Tap 'Use Device Mac'<br/>
                                    </center>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 center">
                                    <center>
                                        <img src="{{ asset('images/android_random_mac.jpg') }}" width="100%" style="max-width: 682px;">
                                    </center>
                                </div>
                            </div>
                            <link href="//fonts.googleapis.com/css?family=Open Sans:300,400,600,800" rel="stylesheet"
                                  property="stylesheet" type="text/css">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Javascript sourced data -->
@stop

@section('js_script')

    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{asset('dist/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/js/scripts/ui/breadcrumbs-with-stats.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/js/scripts/modal/components-modal.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/js/scripts/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/js/scripts/inputmask/jquery.inputmask.bundle.js')}}"></script>

    <!-- END PAGE LEVEL JS-->
@stop