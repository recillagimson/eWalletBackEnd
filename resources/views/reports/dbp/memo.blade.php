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
            <td>{{ $record->rsbsa_number }}</td>
            <td>{{ $record->account_number }}</td>
            <td>{{ $record->first_name }}</td>
            <td>{{ $record->middle_name }}</td>
            <td>{{ $record->last_name }}</td>
            <td>{{ $record->Type }}</td>
            <td>{{ $record->manila_time_transaction_date }}</td>
            <td>{{ $record->reference_number }}</td>
            <td>{{ $record->category }}</td>
            <td>{{ $record->total_amount }}</td>
            <td>{{ $record->description }}</td>
            <td>{{ $record->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>