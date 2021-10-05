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
            @foreach($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->reference_number }}</td>
            <td>{{ $record->manila_time_transaction_date }}</td>
            <td>{{ $record->rsbsa_number }}</td>
            <td>{{ $record->amount_claimed }}</td>
            <td>{{ $record->city_municipality }}</td>
            <td>{{ $record->province_State }}</td>
            <td>{{ $record->cash_out_partner }}</td>
        </tr>
        @endforeach
    </tbody>
</table>