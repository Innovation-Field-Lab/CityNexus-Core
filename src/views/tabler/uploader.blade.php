<?php
$pagename = 'Create New Dataset From Upload';
$section = 'datasets';
?>


@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                    <i style="cursor: pointer" onclick="getHelp('tabler.uploader')" class="ti-help pull-right"></i>
            <form action="/{{config('citynexus.tabler_root')}}/uploader" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="alert alert-info col-sm-10">
                        Please upload a csv file with field titles in the first row.
                    </div>
                    <input type="file" name="file">
                    <br>
                    <input type="submit" value="Upload" id="upload" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>

@stop