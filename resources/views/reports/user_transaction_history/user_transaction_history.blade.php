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
                <h3>Customers Transaction History</h3>
            </th>
            <th>
                <h3>
                    {{ Carbon\Carbon::parse($from)->format('F d, Y') }} to {{ Carbon\Carbon::parse($to)->format('F d, Y') }}</th>
                </h3>
        </tr>
    </thead>
</table>

<table class="table_data">
    <thead>
        <tr>
            <th>Customer Account ID</th>
            <th>Date of Transaction</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datas as $data)
        <tr>
            <td>
                {{ $data->user_account_id }}
            </td>
            <td>
                {{ Carbon\Carbon::parse($data->transaction_date)->format('F d, Y G:i A') }}
            </td>
            <td>
                {{ $data->amount }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>