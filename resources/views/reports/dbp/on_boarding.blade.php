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
            <td>{{ $record->account_number }}</td>
            <td>{{ $record->rsbsa_number }}</td>
            <td>{{ $record->first_name }}</td>
            <td>{{ $record->middle_name }}</td>
            <td>{{ $record->last_name }}</td>
            <td>{{ $record->name_extension }}</td>
            <td>{{ $record->birth_date }}</td>
            <td>{{ $record->city_municipality }}</td>
            <td>{{ $record->province_state }}</td>
            <td>{{ $record->profile_status }}</td>
            <td>{{ $record->manila_time_created_at }}</td>
            <td>{{ $record->manila_time_approved_at }}</td>
            <td>{{ $record->remarks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>