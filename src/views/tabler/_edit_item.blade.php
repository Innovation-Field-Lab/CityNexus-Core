<tr>
    <td>{{$key}}</td>

    <td><input type="text" id="name-{{$key}}" name="map[{{$key}}][name]" class="form-control" value="{{$item->name}}"></td>
    <input type="hidden" name="map[{{$key}}][key]" value="{{$key}}">
    <td>
        {{$item->type}}
        <input type="hidden" value="{{$item->type}}" name="map[{{$key}}][type]">
    </td>
    <td>
        <select name="map[{{$key}}][sync]" id="" class="form-control">
            <option value="null"></option>
            @foreach(config('citynexus.sync') as $k => $i)
                <option value="{{$k}}" @if(isset($item->sync) && $item->sync == $k) selected @endif>{{$i}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="checkbox" name="map[{{$key}}][skip]" @if(isset($item->skip)) checked @endif>
    </td>
    <td><input type="checkbox" name="map[{{$key}}][show]" @if(isset($item->show)) checked @endif></td>
    <td>
        <div class="btn btn-primary btn-sm" onclick="addConfig('{{$key}}')">Edit</div>

        <input type="hidden" name="map[{{$key}}][meta]" id="metadata-{{$key}}" @if(isset($item->meta)) value="{{$item->meta}}" @endif>
        <input type="hidden" name="map[{{$key}}][filter]" id="datafilter-{{$key}}" @if(isset($item->filter)) value="{{$item->filter}}" @endif>
    </td>
</tr>
