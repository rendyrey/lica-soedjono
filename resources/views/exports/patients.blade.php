<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Medrec</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Birthdate</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @foreach($patients as $key => $patient)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $patient->medrec }}</td>
            <td>{{ $patient->name }}</td>
            <td>{{ $patient->gender }}</td>
            <td>{{ $patient->birthdate }}</td>
            <td>{{ $patient->address }}</td>
            <td>{{ $patient->phone }}</td>
            <td>{{ $patient->email }}</td>
        </tr>
    @endforeach
    </tbody>
</table>