<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Monitoring Ruang Server Head Office</title>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%; 
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      box-sizing: border-box;
    } 

    .container {
      width: 100vw;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      font-family: Arial, sans-serif;
      padding: 20px;
      box-sizing: border-box;
      color: #000;
      background-color: #fff;
    }

    .company-logo img {
      width: 300px;
    }

    .header {
      position: relative; 
      display: flex;
      align-items: flex-start;
      margin-bottom: 10px;
      width: 100%;
    }

    .document-info {
      position: absolute; 
      right: 0; 
      top: 0; 
      font-family: Arial, sans-serif;
      font-size: 15px;
      margin-bottom: 10px;
      text-align: left;
    }

    .document-info table {
      width: auto;
      border-collapse: collapse;
    }

    .document-info td {
      padding: 8px;
      border: 1px solid #000;
    }

    .document-info td:first-child {
      font-weight: bold;
      text-align: left;
    }

    .document-info tr:nth-child(even) {
      background-color: #ffffff;
    }

    .title {
      text-align: center;
      font-weight: bold;
      margin: 10px 0;
      font-size: 26px;
      border-top: 2px solid #000;
      border-bottom: 2px solid #000;
      border-left: 2px solid #000;
      border-right: 2px solid #000;
      padding: 10px;
      box-sizing: border-box;
    }

    .room-info {
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: flex-start;
      font-size: 15px;
      margin-bottom: 10px;
    }

    .room-info table {
      width: auto;
      border-collapse: collapse;
    }

    .room-info td {
      padding: 8px;
      border: 1px solid #000;
    }

    .room-info td:first-child {
      font-weight: bold;
      text-align: left;
    }

    .room-info tr:nth-child(even) {
      background-color: #ffffff;
    }

    .monitoring-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 12px;
      table-layout: auto;
    }

    .monitoring-table th,
    .monitoring-table td {
      border: 1px solid #000;
      padding: 8px;
      text-align: center;
      white-space: nowrap;
    }

    .monitoring-table th {
      background-color: #f2f2f2;
      font-weight: bold;
    }

    .monitoring-table td {
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .footer {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .notes ul {
      padding-left: 20px;
      list-style: none;
      margin: 0;
    }

    .signatures {
      width: 35%;
      margin-top: 35px;
      margin-left: 0;
    }

    .signatures td {
      width: 50%;
      vertical-align: top;
      padding: 0 20px;
    }

    .signatures p {
      margin: 0;
      text-align: left;
    }

    .created-by {
      text-align: center;
    }

    .approved-by {
      text-align: center;
    }

    @media print {
      .container {
        padding: 0;
        width: 100%;
        height: 100%;
        max-width: none;
        max-height: none;
      }

      .monitoring-table th, .monitoring-table td {
        padding: 3px;
      }

      .header, .footer, .notes, .signatures {
        margin: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <header class="header">
      <div class="company-logo">
        <img src="{{ public_path('images/company/pangansari.png') }}" alt="Company Logo" />
      </div>
      <div class="document-info">
        <table>
          <tr>
            <td>Doc. No</td>
            <td>: 12345</td>
          </tr>
          <tr>
            <td>Revisi No</td>
            <td>: 1</td>
          </tr>
          <tr>
            <td>Tanggal dibuat</td>
            <td>: {{ $today }}</td>
          </tr>
        </table>
      </div>
    </header>

    <h2 class="title">MONITORING RUANG SERVER HEAD OFFICE</h2>

    <div class="room-info">
      <table>
        <tr>
          <td>Asset</td>
          <td>: {{ $alat_name }}</td>
        </tr>
        <tr>
          <td>Bulan/Tahun</td>
          <td>: {{ $date }}</td>
        </tr>
      </table>
    </div>

    <table class="monitoring-table">
      <thead>
          <tr>
              <th rowspan="2">Tanggal</th>
              <th colspan="{{ $daysInMonth }}">Tanggal Bulan Ini</th>
          </tr>
          <tr>
              <!-- Generating days of the month dynamically -->
              @for ($day = 1; $day <= $daysInMonth; $day++)
                  <th>{{ $day }}</th>
              @endfor
          </tr>
      </thead>
      <tbody>
        @foreach ($result as $index => $values)
            <tr>
                <td>{{ $komponen[$index] ?? 'Unknown' }}</td>
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    <td>
                        {{ $values[$day - 1] ?? ' ' }}
                    </td>
                @endfor
            </tr>
        @endforeach
    </tbody>
  </table>

    <footer class="footer">
      <div class="notes">
        <p>Catatan:</p>
        <ul>
          <li>1. Berilah Tanda bila normal dan tanda bila tidak, pada kolom hari (1 s/d 31)</li>
        </ul>
      </div>
      <table class="signatures">
        <tr>
          <td>
          <p>Dibuat Oleh</p>
          <br></br>
          <br></br>
          <br></br>
          <p>{{ $maker }}</p>
          </td>
        <td>
          <p class="diketahui">Diketahui Oleh</p>
          <br></br>
          <br></br>
          <br></br>
          <p>{{ $pic }} </p>
        </td>
      </tr> 
      </table>        
    </footer>
  </div>
</body>
</html>