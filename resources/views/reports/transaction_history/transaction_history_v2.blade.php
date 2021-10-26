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

<table>
    <thead>
        <tr>
            <th>
                <h3>Transaction History</h3>
            </th>
        </tr>
    </thead>
</table>

<table class="table_data">
    <thead>
        <tr>
            <th>Date of Transaction</th>
            <th>Transaction</th>
            <th>Reference Number</th>
            <th>Amount</th>
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
        </tr>
        @endforeach
    </tbody>
</table>