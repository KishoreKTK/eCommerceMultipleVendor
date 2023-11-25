<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FAQ</title>
</head>
<body>
    <div class="container-fluid">
        <h1>FAQ</h1>
        <hr size="2">
        @if($result['status'] == true)
        <div class="row">
            @foreach ($result['data'] as $data)
            <div class="col-md-12 col-sm-12 m-2">
                <h4>{!! $data->question !!}</h4>
                <p>{!! $data->answer !!}</p>
            </div>
            @endforeach
        </div>
        @else
        <p><center>Something Went Wrong. Please Come Again Later</center></p>
        @endif
    </div>

</body>
</html>
