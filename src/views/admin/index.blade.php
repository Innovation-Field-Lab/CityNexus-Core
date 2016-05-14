@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <a href="/{{config('citynexus.root_directory')}}/admin/refresh-geocoding" class="btn btn-primary">Refresh Geo Coding</a>

    <form action="/{{config('citynexus.root_directory')}}/admin/edit-table">
        {{csrf_field()}}
        <select name="table_name" id="table_name" class="form-control">
            <option value="">Select One</option>
            @foreach($tables as $i)
                <option value="{{$i->table_name}}">{{$i->table_name}}</option>
                @endforeach
        </select>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>


    <a href="/{{config('citynexus.root_directory')}}/admin/merge-properties" class="btn btn-primary">Merge Properties</a>

    <br>
    <br>
    <a href="{{action('\CityNexus\CityNexus\Http\AdminController@getMigratePropertiesToLocations')}}" class="btn btn-primary">Migrate Properties to Locations</a>
    <br>
    <br>
    <a href="{{action('\CityNexus\CityNexus\Http\AdminController@getMigrateTimeStamps')}}" class="btn btn-primary">Migrate Timestamps</a>


@stop