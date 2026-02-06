@extends('layouts.admin')

@section('title', 'Data Users')

@section('content')
<div class="container">
    <h1 class="mb-4">Data Users</h1>

    <!-- Tombol tambah user -->
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Tambah User</a>

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
@endsection
