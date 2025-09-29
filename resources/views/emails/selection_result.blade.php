<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>
<body>
    <p>Halo {{ $applicant->name ?? 'Peserta' }},</p>

    {!! $body !!}

</body>
</html>
