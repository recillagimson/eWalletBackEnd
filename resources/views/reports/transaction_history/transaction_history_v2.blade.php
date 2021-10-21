<style>
    table {
        text-align: center;
        margin: auto;
        border: 1px solid #ccc;
        border-radius: 20px;
        width: 500;
        font-family: arial;
        font-size: 11pt;
    }
    .table_data {
        border: 2px solid #ccc;
        /* margin-top: 50px; */
        /* border: 2px solid #000; */
        border-collapse: collapse;
        border-radius: 20px;
    }
    .table_data tbody tr td, .table_data thead tr th {
        /* border: 2px solid #000; */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 2px;
        /* border-collapse: collapse; */
    }
    #header {
        width: 500;
        margin: auto;
        display: grid;
        grid-template-columns: 150 150 150;
        vertical-align: middle;
        font-family: Arial, Helvetica, sans-serif;
    }
    #header img {
        width: 200px;
        margin: 0 0 0 30px;
    }
    #header div {
        text-align: center;
    }
    #header div h4 {
        text-align: right;
    }
    #header div h3 {
        text-align: center;
    }
</style>

<div id="header">
    <div style="width: 150;">
        <img src="{{ asset('logo.png') }}"/>
    </div>
    <div style="width: 150;">
        <h3>
            Squidpay Transaction History
        </h3>
    </div>
    <div style="width: 150; font-size: 11pt">
        <h4>
            Date: {{ Carbon\Carbon::now()->format('m/d/Y') }}
        </h4>
    </div>
</div>
<br/>
<br/>
<table class="table_data">
    <thead>
        <tr>
            <th>Date and Time</th>
            <th>Description</th>
            <th>Reference No.</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>
                {{ $record->manila_time_transaction_date }}
            </td>
            <td>
                {{ $record->name }}
            </td>
            <td>
                {{ $record->reference_number }}
            </td>
            <td>
                {{ $record->type == 'DR' ?  number_format($record->total_amount, 2) : '' }}
            </td>
            <td>
                {{ $record->type == 'CR' ?  number_format($record->total_amount, 2) : '' }}
            </td>
            <td>
                {{ number_format($record->available_balance, 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>