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
            <th>Transaction Date</th>
            <th>Account Number</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Total Amount</th>
            <th>Reference Number</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->manila_time_transaction_date }}</td>
            <td>{{ $record->account_number }}</td>
            <td>{{ $record && $record->user_detail ? $record->user_detail->last_name : '' }}</td>
            <td>{{ $record && $record->user_detail ? $record->user_detail->first_name : '' }}</td>
            <td>{{ $record->total_amount }}</td>
            <td>{{ $record->reference_number }}</td>
            <td>{{ $record->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>