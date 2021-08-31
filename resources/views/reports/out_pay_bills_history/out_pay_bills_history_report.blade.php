<style>
    table {
        text-align: center;
        margin: auto;
    }
    .table_data {
        margin-top: 50px;
        border: 2px solid #000;
        border-collapse: collapse;
    }
    .table_data tbody tr td, .table_data thead tr th {
        border: 2px solid #000;
        padding: 10px;
    }
</style>

<table class="table_data">
    <thead>
        <tr>
            <th>Account Number</th>
            <th>Name</th>
            <th>Reference Number</th>
            <th>Transactionm Date</th>
            <th>Biller</th>
            <th>Biller Reference No</th>
            <th>Total Amount</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>
                {{ $record['0'] }}
            </td>
            <td>
                {{ $record['1'] }}
            </td>
            <td>
                {{ $record['2'] }}
            </td>
            <td>
                {{ $record['3'] }}
            </td>
            <td>
                {{ $record['4'] }}
            </td>
            <td>
                {{ $record['5'] }}
            </td>
            <td>
                {{ $record['6'] }}
            </td>
            <td>
                {{ $record['7'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>