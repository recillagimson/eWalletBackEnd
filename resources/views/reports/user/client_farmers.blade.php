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
            <th>Customer ID</th>
            <th>RSBSA Number</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Account Status</th>
            <th>Profile Status</th>
            <th>Tier</th>
            <th>Registration Date</th>
            <th>Verified Date</th>
            <th>On Boarding Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <!-- <td>
                {{ Carbon\Carbon::parse($record['0'])->format('F d, Y G:i A') }}
            </td> -->
            <td>
                {{ $record->account_number }}
            </td>
            <td>
                {{ $record->rsbsa_number }}
            </td>
            <td>
                {{ $record->first_name }}
            </td>
            <td>
                {{ $record->middle_name }}
            </td>
            <td>
                {{ $record->last_name }}
            </td>
            <td>
                {{ $record->account_status }}
            </td>
            <td>
                {{ $record->profile_status }}
            </td>
            <td>
                {{ $record->tier_class }}
            </td>
            <td>
                {{ $record->original_created_at }}
            </td>
            <td>
                {{ $record->original_approved_date }}
            </td>
            <td>
                {{ $record->on_boarding_status }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>