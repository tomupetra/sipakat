<!DOCTYPE html>
<html>

<head>
    <title>Laporan Peminjaman Ruangan</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
            color: #2c3e50;
        }

        .header h3 {
            margin: 0;
        }

        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #bdc3c7;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f6fa;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #7f8c8d;
        }

        @page {
            margin: 40px;
            size: A4;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/logo hkbp.png') }}" style="width: 120px; height: auto;">
        <h2>HURIA KRISTEN BATAK PROTESTAN</h2>
        <h3>HKBP KAYU TINGGI RESORT KAYU TINGGI</h3>
        <p>Jl. Cempaka I Dalam No. 65 RT.013/RW.09 Cakung Timur, Jakarta Timur 13910</p>
        <p>Telp: 021-4609233</p>
        <hr style="border: 1px solid #000; margin-top: 10px;">
    </div>

    <h2 style="text-align: center;">Laporan Peminjaman Ruangan</h2>

    <table>
        <thead>
            <tr>
                <th>Nama Peminjam</th>
                <th>Kegiatan</th>
                <th>Ruangan</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $item)
                <tr>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ $item->kegiatan }}</td>
                    <td>{{ $item->ruangan->name }}</td>
                    <td>{{ $item->start_time }}</td>
                    <td>{{ $item->end_time }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
    </div>
</body>

</html>
