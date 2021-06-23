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
                <h3>Billers History</h3>
            </th>
            <th>
                <h3>
                    {{ Carbon\Carbon::now()->format('F d, Y') }}</th>
                </h3>
        </tr>
    </thead>
</table>

<table class="table_data">
    <thead>
        <tr>
            <th>Customer Account ID</th>
            <th>Customer Name</th>
            <th>Reference Number</th>
            <th>Date of Transaction</th>
            <th>Biller</th>
            <th>Amount</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datas as $data)
        <tr>
            <td>
                {{ $data->user_account_id }}
            </td>
            <td>
                {{ ucwords($data->user_detail->first_name) }} {{ ucwords($data->user_detail->last_name) }}
            </td>
            <td>
                {{ $data->reference_number }}
            </td>
            <td>
                {{ Carbon\Carbon::parse($data->transaction_date)->format('F d, Y G:i A') }}
            </td>
            <td>
                {{ ucwords($data->billers_name) }}
            </td>
            <td>
                {{ $data->total_amount }}
            </td>
            <td>
                {{ ($data->status) ? 'Paid' : 'Not Paid' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>