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
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Transaction Type</th>
            <th>Category</th>
            <th>Description</th>
            <th>Remarks</th>
            <th>Reference Number</th>
            <th>User Created</th>
            <th>Approved By</th>
            <th>Declined By</th>
            <th>Approved At</th>
            <th>Declined At</th>
            <th>Current Balance</th>
            <th>Available Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record['transaction_date'] }}</td>
            <td>{{ $record['account_number'] }}</td>
            <td>{{ $record['first_name'] }}</td>
            <td>{{ $record['middle_name'] }}</td>
            <td>{{ $record['last_name'] }}</td>
            <td>{{ $record['status'] }}</td>
            <td>{{ $record['transaction_type'] }}</td>
            <td>{{ $record['category'] }}</td>
            <td>{{ $record['description'] }}</td>
            <td>{{ $record['remarks'] }}</td>
            <td>{{ $record['reference_number'] }}</td>
            <td>{{ $record['user_created'] }}</td>
            <td>{{ $record['approved_by_name'] }}</td>
            <td>{{ $record['declined_by_name'] }}</td>
            <td>{{ $record['approved_at'] }}</td>
            <td>{{ $record['declined_at'] }}</td>
            <td>{{ $record['current_balance'] }}</td>
            <td>{{ $record['available_balance'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>