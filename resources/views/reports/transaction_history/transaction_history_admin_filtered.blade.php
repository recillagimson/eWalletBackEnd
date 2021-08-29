<style>
    table {
        text-align: center;
        margin: auto;
    }
    .table_data {
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
            <th>Customer Name</th>
            <th>Date of Transaction</th>
            <th>Transaction</th>
            <th>Type of Memo</th>
            <th>Reference Number</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>
                {{ $record->account_number }}
            </td>
            <td>
                {{ $record->first_name . " " . $record->last_name}}
            </td>
            <td>
                {{ 
                    $record->transaction_date_manila_time
                }}
            </td>
            <td>
                {{ $record->Description }}
            </td>
            <td>
                {{ $record->Type }}
            </td>
            <td>
                {{ $record->reference_number }}
            </td>
            <td>
                {{ $record->total_amount }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>