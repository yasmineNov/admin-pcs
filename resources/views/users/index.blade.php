@extends('layouts.admin')

@section('title', 'Data Users')

@section('content')
    <div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <h2>Master Users</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary mb-2"> + Tambah User</a>
        </div>
    </div>
    <div class="card-body">
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Email</th>
                {{-- <th>Role</th> --}}
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    {{-- <td>{{ $user->role ? $user->role->name : '-' }}</td> --}}
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus user ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data user</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
