@extends('dashboard/layouts/main')

@section('content')
    <div class="w-4/12 absolute -top-4 z-10 flex items-end right-0">
        @include('dashboard/components.alerd')
    </div>
    <h1 class="text-3xl text-black pb-6">List User</h1>
    <form action="{{ route('delete') }}" method="POST" id="deleteForm">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 text-white btn btn-sm mb-4 hidden">Delete</button>
        <div class="overflow-x-auto">
            <table id="list" class="min-w-full text-xs rounded">
                <thead class="rounded-t-lg text-white bg-gray-800">
                    <tr class="text-right">
                        <th class="px-4 py-2 text-sm">
                            <div class="flex gap-3">
                                <span class="label-text text-sm text-white">All</span>
                                <input type="checkbox" id="checkAll" class=" checkbox-info" />
                            </div>
                        </th>
                        <th class="px-4 border py-2 text-sm">Name</th>
                        <th class="px-4 border py-2 text-sm">Username</th>
                        <th class="px-4 border py-2 text-sm">Kelas</th>
                        <th class="px-4 border py-2 text-sm">Status</th>
                        <th class="px-4 border py-2 text-sm">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="text-black">
                            <td class="border px-4 py-2">
                                <input type="checkbox" name="selected_users[]" value="{{ $user->id }}"
                                    class="checkItem">
                            </td>
                            <td class="border px-4 py-2 text-sm"><span>{{ $user->name }}</span></td>
                            <td class="border px-4 py-2 text-sm"><span>{{ $user->username }}</span></td>
                            <td class="border px-4 py-2 text-sm"><span>{{ $user->kelas }}</span></td>
                            <td class="border px-4 py-2 text-sm"><span>{{ $user->status }}</span></td>
                            <td class="border px-4 py-2 text-sm">
                                <!-- You can add individual action buttons here if needed -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            $('#list').DataTable({});
        });

        function toggleDeleteButton() {
            const checkItems = document.querySelectorAll('.checkItem');
            const deleteButton = document.querySelector('button[type="submit"]');
            let isAnyChecked = false;

            checkItems.forEach(item => {
                if (item.checked) {
                    isAnyChecked = true;
                }
            });

            // Show the button if any checkbox is checked, otherwise hide it
            if (isAnyChecked) {
                deleteButton.classList.remove('hidden');
            } else {
                deleteButton.classList.add('hidden');
            }
        }
        // Script to handle the "Check All" functionality
        document.getElementById('checkAll').addEventListener('click', function(e) {
            let checkItems = document.querySelectorAll('.checkItem');
            checkItems.forEach(item => item.checked = e.target.checked);
            toggleDeleteButton();
        });

        // Handle individual checkbox click events
        let checkItems = document.querySelectorAll('.checkItem');
        checkItems.forEach(item => {
            item.addEventListener('click', function() {
                toggleDeleteButton();
            });
        });
    </script>
@endsection
