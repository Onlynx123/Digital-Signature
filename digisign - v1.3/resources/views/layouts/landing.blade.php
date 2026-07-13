<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DigiSign — Tanda Tangan Dokumen Digital yang Aman</title>
    <meta name="description" content="DigiSign adalah platform tanda tangan digital untuk dokumen bisnis. Unggah, kelola penandatangan, dan verifikasi keaslian dokumen dengan hash SHA-256.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/digisign.css') }}">
</head>
<body>
    {{ $slot ?? '' }}
    @yield('content')
</body>
</html>
