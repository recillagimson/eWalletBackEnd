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
            <th>Last Name</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Type</th>
            <th>Reference Number</th>
            <th>Amount</th>
            <th>Category</th>
            <th>Description</th>
            <th>Remarks</th>
            <th>Status</th>
            <th>Created By User</th>
            <th>Approved By</th>
            <th>Decliend By</th>
            <th>Transaction Date</th>
            <th>Approved at</th>
            <th>Declined at</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record['0'] }}</td>
            <td>{{ $record['1'] }}</td>
            <td>{{ $record['2'] }}</td>
            <td>{{ $record['3'] }}</td>
            <td>{{ $record['4'] }}</td>
            <td>{{ $record['5'] }}</td>
            <td>{{ $record['6'] }}</td>
            <td>{{ $record['7'] }}</td>
            <td>{{ $record['8'] }}</td>
            <td>{{ $record['9'] }}</td>
            <td>{{ $record['10'] }}</td>
            <td>{{ $record['11'] }}</td>
            <td>{{ $record['12'] }}</td>
            <td>{{ $record['13'] }}</td>
            <td>{{ $record['17'] }}</td>
            <td>{{ $record['18'] }}</td>
            <td>{{ $record['19'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>