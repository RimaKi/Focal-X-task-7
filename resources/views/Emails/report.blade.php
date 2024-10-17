<!DOCTYPE html>
<html>
<head>
    <title>Daily Report</title>
</head>
<body>
<h1>Daily Incomplete Tasks Report</h1>

<h2>Report Day:{{\Carbon\Carbon::now()->format('Y-m-d')}}</h2>

@if($tasks->isEmpty())
    <p>Good job! All tasks are completed.</p>
@else
    <ul>
        <h1>Rima rima</h1>
        @foreach($tasks as $task)
            <li>{{$task}}</li>
        @endforeach
    </ul>
@endif
<p></p>

</body>
</html>
