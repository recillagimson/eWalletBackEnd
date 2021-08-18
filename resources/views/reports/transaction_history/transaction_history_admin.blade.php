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

<table>
    <thead>
        <tr>
            <th colspan="7">
                <h3>
                    Transaction History {{ Carbon\Carbon::parse($from)->format('F d, Y') }} to {{ Carbon\Carbon::parse($to)->format('F d, Y') }}
                </h3>
            </th>
        </tr>
    </thead>
</table>
<table class="table_data">
    <thead>
        <tr>
            <th>Account Number</th>
            <th>RSBSA Number</th>
            <th>Customer Name</th>
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
                {{ $record->account_number }}
            </td>
            <td>
                {{ $record->rsbsa_number }}
            </td>
            <td>
                {{ $record->first_name . " " . $record->last_name}}
            </td>
            <td>
                {{ Carbon\Carbon::parse($record->transaction_date)->format('F d, Y G:i A') }}
            </td>
            <td>
                {{ $record->Description }}
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