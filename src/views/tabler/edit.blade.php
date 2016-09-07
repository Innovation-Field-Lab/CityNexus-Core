@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Create New Schema
            </div>
            <div class="panel-body">
                <form action="/{{config('citynexus.tabler_root')}}/update-table/{{$table->id}}" method="post">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-sm-8">
                            <label for="table_title">Table Title</label>
                            <input type="text" name="table_title" class="form-control" value="{{$table->table_title}}"required>
                            <label for="description">Table Description</label>
                            <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{$table->description}}</textarea>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="timestamp" class="control-label col-sm-4">Time Stamp</label>

                                <div class="col-sm-8">
                                    <select name="settings[timestamp]" class="form-control" id="timestamp">
                                        <option value="">Use Today's Date</option>
                                        @foreach($scheme as $key => $item)
                                            <option value="{{$key}} @if(isset($settings->timestamp) && $settings->timestamp == $key) selected @endif">{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="timestamp" class="control-label col-sm-4">Unique ID</label>
                                <div class="col-sm-8">
                                    <select name="settings[unique_id]" class="form-control" id="unique_id">
                                        <option value="">None</option>

                                        @foreach($scheme as $key => $item)
                                            <option value="{{$key}}" @if(isset($settings->unique_id) && $settings->unique_id == $key) selected @endif>{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="timestamp" class="control-label col-sm-4">Property ID</label>

                                <div class="col-sm-8">
                                    <select name="settings[property_id]" class="form-control" id="property_id">
                                        <option value="">None</option>
                                        @foreach($scheme as $key => $item)
                                            <option value="{{$key}}" @if(isset($settings->property_id) && $settings->property_id == $key) selected @endif >{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <br><br>
                    <label for="">Table Elements</label>
                    <table class="table" id="table">
                        <thead>
                        <td>Key</td>
                        <td>Field Name
                            <i class="ti-help" style="cursor: pointer" onclick="getHelp('tabler.uploader.fieldname')" ></i>
                        </td>
                        <td>Field Type</td>
                        <td>Sync
                            <i class="ti-help" style="cursor: pointer" onclick="getHelp('tabler.uploader.sync')" ></i>
                        </td>
                        <td>Ignore</td>
                        <td>Visible</td>
                        <td>Config</td>
                        </thead>

                        <tbody>
                            @foreach($scheme as $key => $item)
                                @include('citynexus::tabler._edit_item')
                            @endforeach
                        </tbody>
                    </table>
                    <input type="submit" class="btn btn-primary" value="Update Table">
                </form>
            </div>
        </div>
    </div>

    <div id="config-modal" class="modal-demo">
        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">Close</span>
        </button>
        <h4 class="custom-modal-title" id="config-modal-title">Add Meta Data for </h4>
        <div id="config-modal-text" class="custom-modal-text">
        </div>
    </div>

@stop

@push('js_footer')

<script>
    function addConfig( id )
    {
        var field = $("#name-" + id).val();
        $("#config-modal-title").html('Configuration settings for "' + field + '"');

        var meta = $("#metadata-" + id).val();
        var modalBody = $("#config-modal-text");

        var metaData = "<div class='form-group'><label>Meta Data</label><textarea id='metadata' class='form-control'>" +
        meta +
        '</textarea></div><br>';

        var filter = "<div class='form-group'><label>Data Filters</label>" +
                "<div style='height: 75px; border-color: #0a6aa1; border-width: 1px; overflow: scroll'>" +
                "<input type='checkbox' name='new-filters' value='1'> <label>Test Filter 1</label><br>" +
                "<input type='checkbox' name='new-filters' value='2'> <label>Test Filter 2</label><br>" +
                "<input type='checkbox' name='new-filters' value='3'> <label>Test Filter 3</label><br>" +
                "<input type='checkbox' name='new-filters' value='4'> <label>Test Filter 4</label><br>" +
                "<input type='checkbox' name='new-filters' value='5'> <label>Test Filter 5</label><br>" +
                "</div>" +
                "</div>";
        var save = '<div class="btn btn-primary" onClick="Custombox.close(); saveConfig(\'' + id + '\')">Save</div>';
        var content = metaData + save;

        modalBody.html(content);

        Custombox.open({
            target: '#config-modal',
            effect: 'fadein'
        });
    }

    function addFilter()
    {
        triggerModal('test', 'test');
    }

    function saveConfig( key )
    {
        var entry = $("#metadata").val();
        $('#metadata-' + key).val( entry );
    }
</script>

@endpush