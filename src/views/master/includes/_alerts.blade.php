@if(\Illuminate\Support\Facades\Session::get('flash_success') != null)
    @include('citynexus::master.includes.alerts._success', ['alert' => \Illuminate\Support\Facades\Session::get('flash_success')])

@elseif(\Illuminate\Support\Facades\Session::get('flash_info') != null)
    @include('citynexus::master.includes.alerts._info', ['alert' => \Illuminate\Support\Facades\Session::get('flash_info')])

@elseif(\Illuminate\Support\Facades\Session::get('flash_warning') != null)
    @include('citynexus::master.includes.alerts._warning', ['alert' => \Illuminate\Support\Facades\Session::get('flash_warning')])

@elseif(\Illuminate\Support\Facades\Session::get('flash_danger') != null)
    @include('citynexus::master.includes.alerts._danger', ['alert' => \Illuminate\Support\Facades\Session::get('flash_danger')])

@endif