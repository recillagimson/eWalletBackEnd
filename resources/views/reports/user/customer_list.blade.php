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
            <th>Email Address</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Mobile Number</th>
            <th>Account Status</th>
            <th>Profile Status</th>
            <th>Tier</th>
            <th>Registration Date</th>
            <th>Verified Date</th>
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
                {{ $record->email }}
            </td>
            <td>
                {{ $record && $record->profile ? $record->profile->first_name : '' }}
            </td>
            <td>
                {{ $record && $record->profile ? $record->profile->middle_name : '' }}
            </td>
            <td>
                {{ $record && $record->profile ? $record->profile->last_name : '' }}
            </td>
            <td>
                {{ $record->mobile_number }}
            </td>
            <td>
                {{ $record->is_active == 1 ? 'Active' : 'Inactive' }}
            </td>
            <td>
                {{ $record && $record->profile ? $record->profile->verification_status : '' }}
            </td>
            <td>
                {{ $record->tier->tier_class }}
            </td>
            <td>
                {{ $record->manila_time_created_at }}
            </td>
            <td>
                {{ $record && $record->lastTierApproval ? $record->lastTierApproval->manila_time_approved_at : '' }}
            </td>
        </tr>

        @endforeach
    </tbody>
</table>