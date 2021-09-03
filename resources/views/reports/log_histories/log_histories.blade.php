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
            <th>Date of Transaction</th>
            <th>Customer ID</th>
            <th>Customer Name</th>
            <th>Current Balance</th>
            <th>Type (DR/CR)</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Transaction Description</th>
            <th>Available Balance</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>
                {{ Carbon\Carbon::parse($record['0'])->format('F d, Y G:i A') }}
            </td>
            <td>
                {{ $record['10'] }}
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
            <td>
                {{ $record['8'] }}
            </td>
            <td>
                {{ $record['9'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>