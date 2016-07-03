<div class="panel panel-default">
    <div class="panel-heading">
        Property Tasks
    </div>
    <div class="panel-body">
        <div class="list-group task-list" id="property_tags">
            @foreach($property->tasks->open() as $task)
                <div class="list-group-item">
                    <b>{{$task->task}}</b><br>
                    @if($task->assign_to != null)
                        Assigned to: {{$task->assignee->firstname()}}
                    @endif
                        {{$task->created_at->diffForHumans()}}
                    @if($task->due_at != null)
                        <br>
                        <b>Due: {{$task->due_at->diffForHumans()}}</b>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>