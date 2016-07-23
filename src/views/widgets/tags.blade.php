<?php
    $tags = \CityNexus\CityNexus\Tag::find($widget->setting->tag_id)->properties->take(20);

?>

<div class="col-sm-4">
    <div class="card-box">

        <h4 class="header-title m-t-0 m-b-30">Tagged <a href="{{action('\CityNexus\CityNexus\Http\TagController@getList', [$widget->setting->tag_id])}}"><div class="label label-default">{{\CityNexus\CityNexus\Tag::find($widget->setting->tag_id)->tag}}</div></a></h4>

        <div class="inbox-widget nicescroll" style="height: 315px;">
            @foreach($tags as $i)
                <a href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$i->id])}}">
                    <div class="inbox-item">
                        <p class="inbox-item-author">{{ucwords($i->full_address)}}</p>
                        <p class="inbox-item-text">Tagged by {{\App\User::find($i->pivot->created_by)->fullName()}}</p>
                        <p class="inbox-item-date">{{$i->pivot->created_at->diffForHumans()}}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div><!-- end col -->