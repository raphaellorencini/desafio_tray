<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
<body>

<table>
    <tr>
        <th>Comissão do dia - {{$data['date']}}</th>
    </tr>
    <tr>
        <td>
            @if(isset($data['name']) && filled($data['name']))
            <p><strong>Vendedor:</strong> {{$data['name']}}</p>
            <p><strong>Email:</strong> {{$data['email']}}</p>
            @endif
            <p><strong>Comissão:</strong> {{$data['commission']}}</p>
        </td>
    </tr>
</table>
</body>
</html>
