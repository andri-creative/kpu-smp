@extends('dashboard/layouts/main')

@section('content')
    <h1 class="text-3xl text-black pb-6">Tambah User</h1>
    <div class="w-max md:hidden flex gap-6">
        <button class="px-10 py-1 bg-blue-400 rounded" id="show-user-data">User Data</button>
        <button class="px-10 py-1 bg-blue-400 rounded" id="show-user-exsel">User Exsel</button>
    </div>
    <div class="w-4/12 absolute -top-4 z-10 flex items-end right-0">
        @include('dashboard/components.alerd')
    </div>
    <div class="grid md:grid-cols-2 grid-col-1 gap-2">
        <div class="w-full" id="user-data">
            <form action="{{ route('tambah') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="name">
                        Nama
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="name" type="text" placeholder="Your Name..." name="name">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="nis">
                        NIS
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="nis" type="text" placeholder="Your NIS..." name="username">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="kelas">
                        Kelas
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="kelas" type="text" placeholder="Your Kelas..." name="kelas">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="password">
                        Password
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="password" type="password" placeholder="Your Password..." name="password">
                </div>
                <button type="submit" class="w-max p-2 bg-blue-400 rounded">Submit</button>
            </form>
        </div>
        <div class="w-full hidden md:block" id="user-exsel">
            <form action="{{ route('importData') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="name">
                        Import Data Exsel
                    </label>
                    <input type="file" id="upload" name="upload"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline file-input file-input-bordered"
                        required />
                    @error('upload')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-active btn-primary">Primary</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function setActiveButton(buttonId) {
                $('#show-user-data, #show-user-exsel').removeClass('bg-blue-700 text-white').addClass(
                    'bg-blue-400 text-black');
                $(buttonId).removeClass('bg-blue-400 text-black').addClass('bg-blue-700 text-white');
            }

            $('#show-user-data').on('click', function() {
                $('#user-data').show();
                $('#user-exsel').hide();
                setActiveButton('#show-user-data');
            });

            $('#show-user-exsel').on('click', function() {
                $('#user-data').hide();
                $('#user-exsel').show();
                setActiveButton('#show-user-exsel');
            });

            $(window).resize(function() {
                if ($(window).width() >= 768) {
                    $('#user-data').show();
                    $('#user-exsel').show();
                    $('#show-user-data, #show-user-exsel').removeClass('bg-blue-700 text-white').addClass(
                        'bg-blue-400 text-black');
                } else {
                    $('#user-data').show();
                    $('#user-exsel').hide();
                    setActiveButton('#show-user-data');
                }
            }).resize(); // Trigger resize event on page load
        });
    </script>
@endsection
