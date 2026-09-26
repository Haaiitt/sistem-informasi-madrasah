<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td { padding: 4px 0; vertical-align: top; }
        td.label { width: 180px; color: #555; }
    </style>
</head>
<body>
    <h1>Bukti Pendaftaran PPDB</h1>
    <p>No. Registrasi: <strong>{{ $applicant->registration_number }}</strong></p>
    <p>Dicetak: {{ now()->translatedFormat('d M Y, H:i') }}</p>

    <table>
        <tr><td class="label">Jenis Pendaftaran</td><td>{{ $applicant->type === 'baru' ? 'Siswa Baru' : 'Pindahan' }}</td></tr>
        <tr><td class="label">Nama Lengkap</td><td>{{ $applicant->full_name }}</td></tr>
        <tr><td class="label">Jenis Kelamin</td><td>{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
        <tr><td class="label">Tempat, Tanggal Lahir</td><td>{{ $applicant->birth_place }}, {{ $applicant->birth_date->translatedFormat('d M Y') }}</td></tr>
        <tr><td class="label">NISN</td><td>{{ $applicant->nisn ?? '-' }}</td></tr>
        <tr><td class="label">Alamat</td><td>{{ $applicant->address }}</td></tr>
        <tr><td class="label">Waktu Dikirim</td><td>{{ $applicant->submitted_at->translatedFormat('d M Y, H:i') }}</td></tr>
    </table>

    <p style="margin-top:16px;"><strong>Orang Tua/Wali</strong></p>
    <table>
        @foreach ($applicant->guardians as $guardian)
            <tr><td class="label">{{ ucfirst($guardian->relation) }}</td><td>{{ $guardian->full_name }} ({{ $guardian->phone ?? '-' }})</td></tr>
        @endforeach
    </table>
</body>
</html>
