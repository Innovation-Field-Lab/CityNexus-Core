<?php
$pagename = 'Create Dropbox Sync';
$section = 'dataset';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-12 form-horizontal">
            <div class="card-box table-responsive" id="connection_setup">

                <div class="form-group">
                    <label for="settings[dropbox_token]" class="control-label col-sm-4">Dropbox Token <i class="ti-help" style="cursor: pointer" onclick="getHelp('tabler.dropbox.token')" ></i>
                    </label>

                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="settings_dropbox_token" name="settings[dropbox_token]" value=""/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="settings[path]" class="control-label col-sm-4">Dropbox Path</label>

                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="settings_path" name="settings[dropbox_path]"
                               value=""/>
                    </div>
                </div>

                <input type="submit" id='test_connection' value="Test Connection" class="btn btn-primary" onclick="checkConnection()">
                <div id="connection_results"></div>
            </div>
            <div id="schedule_sync"></div>
        </div>
    </div>

@stop

@push('js_footer')

<script>


    function checkConnection()
    {
        var token = $('#settings_dropbox_token').val();
        var path = $('#settings_path').val();

        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\DatasetController@postCreateDropboxSync')}}",
            type: 'POST',
            data: {
                _token: "{{csrf_token()}}",
                settings: {
                    dropbox_token: token,
                    dropbox_path: path
                },
                dataset_id:"{{$dataset_id}}"
            }
        }).success(function(data){
            $('#connection_results').html(data);
            $('#test_connection').val('Refresh Connection');
        }).fail(function(){
            Command: toastr["warning"]('Uh oh. Something has gone wrong. Please check your internet connection.')
        });
    }

    function processUpload(download) {

        var token = $('#settings_dropbox_token').val();
        var path = $('#settings_path').val();

        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\DatasetController@postProcessDropboxSync')}}",
            type: 'POST',
            data: {
                _token: "{{csrf_token()}}",
                settings: {
                    dropbox_token: token,
                    dropbox_path: path
                },
                download: download,
                dataset_id:"{{$dataset_id}}"

            }
        }).success(function (data) {
            $('#schedule_sync').html(data);
            $('#connection_setup').addClass('hidden');
            $('#final_settings_dropbox_token').val(token);
            $('#final_settings_dropbox_path').val(path);
            $('#final_dataset_id').val({{$dataset_id}});
        }).fail(function(){
            Command: toastr["warning"]('Uh oh. Something has gone wrong. Please check your internet connection.')
        });
    }




</script>

@endpush