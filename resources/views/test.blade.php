<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Report</title>
</head>
<body>
<h1>Task Report</h1>
<h2>Report Day:{{\Carbon\Carbon::now()->format('Y-m-d')}}</h2>

@if(!$completed_tasks->isEmpty())
    <h2>Tasks completed today: </h2>
    @foreach($completed_tasks as $task)
        <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
            <h2>Task #{{ $task->id }}: {{ $task->title }}</h2>
            <p><strong>Description:</strong> {{ $task->description }}</p>
            <p><strong>Type:</strong> {{ $task->type }}</p>
            <p><strong>Status:</strong> {{ $task->status }}</p>
            <p><strong>Priority:</strong> {{ $task->priority }}</p>
            <p><strong>Due Date:</strong> {{ $task->due_date ?? 'No due date' }}</p>
        </div>
    @endforeach
@endif
<hr>
@if($uncompleted_tasks->isEmpty())
    <p>Good job! All tasks are completed.</p>
@else
    <h2>Tasks uncompleted today: </h2>

    @foreach($uncompleted_tasks as $item)
        <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
            <h2>Task #{{ $item->id }}: {{ $item->title }}</h2>
            <p><strong>Description:</strong> {{ $item->description }}</p>
            <p><strong>Type:</strong> {{ $item->type }}</p>
            <p><strong>Status:</strong> {{ $item->status }}</p>
            <p><strong>Priority:</strong> {{ $item->priority }}</p>
            <p><strong>Due Date:</strong> {{ $item->due_date ?? 'No due date' }}</p>

            @if(!$item->taskStatusUpdates->isEmpty())
                <h3>Status Updates</h3>
                <ul>
                    @foreach($task->taskStatusUpdates as $update)
                        <li>
                            <p><strong>Status:</strong> {{ $update->current_status }}</p>
                            <p><strong>Description:</strong> {{ $update->description }}</p>
                            <p><strong>Updated At:</strong> {{ $update->updated_at }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif

        </div>
    @endforeach
@endif
</body>
</html>
