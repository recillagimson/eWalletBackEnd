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
            <th>Total Amount</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>
                {{ $record['account_number'] }}
            </td>
            <td>
                {{ $record['first_name'] . " " . $record['middle_name'] . " " . $record['last_name'] }}
            </td>
            <td>
                {{ $record['reference_number'] }}
            </td>
            <td>
                {{ $record['transaction_date'] }}
            </td>
            <td>
                {{ $record['billers_name'] }}
            </td>
            <td>
                {{ $record['total_amount'] }}
            </td>
            <td>
                {{ $record['status'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>