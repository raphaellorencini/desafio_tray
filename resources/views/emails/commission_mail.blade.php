<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
<body>
<table>
    {{--<tr>
        <td>
            <img src="{{ asset('path-to-your-logo.png') }}" alt="Your Logo">
        </td>
    </tr>--}}
    <tr>
        <td>
            {{$data['test']}}
            {{--<h1>{{ $greeting }}</h1>
            <p>{{ $intro }}</p>
            <p>{{ $content }}</p>
            <p>{{ $outro }}</p>
            <p>{{ $signature }}</p>--}}
        </td>
    </tr>
</table>
</body>
</html>
