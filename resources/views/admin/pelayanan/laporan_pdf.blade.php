<!DOCTYPE html>
<html>

<head>
    <title>Laporan Jadwal Pelayanan</title>
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

        .header p {
            margin: 10px 0 0;
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
    <div class="header" style="text-align: center;">
        <img src="{{ public_path('images/logo hkbp.png') }}" style="width: 120px; height: auto;">
        <h2 style="margin: 10px;">HURIA KRISTEN BATAK PROTESTAN</h2>
        <h3 style="margin: 0;">HKBP KAYU TINGGI RESORT KAYU TINGGI</h3>
        <p style="margin: 0;">Jl. Cempaka I Dalam No. 65 RT.013/RW.09 Cakung Timur, Jakarta Timur 13910</p>
        <p style="margin: 0;">Telp: 021-4609233</p>
        <hr style="border: 1px solid #000; margin-top: 10px;">
    </div>

    <h2 style="text-align: center;">Laporan Jadwal Pelayanan</h2>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Pemusik</th>
                <th>Song Leader 1</th>
                <th>Song Leader 2</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $jadwal)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($jadwal->date)->format('d/m/Y') }}</td>
                    <td>{{ $jadwal->jadwal }}</td>
                    <td>{{ $jadwal->pemusik->name ?? '-' }}</td>
                    <td>{{ $jadwal->songLeader1->name ?? '-' }}</td>
                    <td>{{ $jadwal->songLeader2->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
    </div>
</body>

</html>
