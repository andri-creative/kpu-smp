@extends('dashboard/layouts/main')

@section('content')
    <div class="w-4/12 absolute -top-4 z-10 flex justify-end right-0">
        @include('dashboard/components.alerd')
    </div>
    <div class="relative h-screen">
        <h1 class="text-3xl text-black pb-6">Tambah Kandidat</h1>
        <button class="btn bg-blue-400 text-white btn-outline mb-4" onclick="new_kandidat.showModal()">New Kandidat</button>
        <div
            class="w-fit mx-auto grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 justify-items-center justify-center gap-y-20 gap-10 mt-10 mb-5 relative ">
            @foreach ($kandidat as $item)
                <div class="w-72 bg-white shadow-md rounded-xl duration-500 hover:scale-105 hover:shadow-xl relative">
                    <a href="#">
                        <img src="{{ asset('images/' . $item->image_calon) }}" alt="Product"
                            class="h-80 w-72 object-cover rounded-t-xl" />
                        <div class="px-4 py-3 w-72 flex justify-between text-white">
                            <button onclick="document.getElementById('show{{ $item->id }}').showModal()"
                                class="px-5 py-1 border-2 rounded bg-blue-500">
                                <i class="fa-duotone fa-solid fa-eye"></i>
                            </button>
                            <button onclick="document.getElementById('edit{{ $item->id }}').showModal()"
                                class="px-5 py-1 border-2 rounded bg-blue-500"><i
                                    class="fa-duotone fa-solid fa-pen-to-square"></i></button>
                            <button onclick="document.getElementById('delete{{ $item->id }}').showModal()"
                                class="px-5 py-1 border-2 rounded bg-blue-500">
                                <i class="fa-duotone fa-solid fa-trash"></i>
                            </button>

                        </div>
                    </a>
                </div>
                <!-- You can open the modal using ID.showModal() method -->

                <!-- Delete -->
                <dialog id="delete{{ $item->id }}" class="modal">
                    <div class="modal-box">
                        <form method="dialog">
                            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 text-3xl"><i
                                    class="fa-duotone fa-solid fa-circle-xmark"></i></button>
                        </form>
                        <form action="{{ route('kandidat.delete', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="flex flex-col">
                                <div class="mb-4 text-black">Apakah Anda yakin ingin menghapus kandidat ini?</div>
                                <button type="submit" class="btn btn-error w-max text-white">Hapus</button>
                            </div>
                        </form>

                    </div>
                </dialog>

                <!-- Detail -->
                <dialog id="show{{ $item->id }}" class="modal">
                    <div class="modal-box">
                        <h3 class="text-lg font-bold">Hello!</h3>
                        <div class="modal-action">
                            <div class="flex flex-col">
                                <div class="px-4 py-3 w-full bg-gray-300 rounded-lg mb-3">
                                    <label for="">Nama Kandidat</label>
                                    <p class="text-lg font-medium text-gray-800 mb-2">{{ $item->name_calon }}</p>
                                </div>
                                <div class="px-4 py-3 w-full bg-gray-300 rounded-lg mb-3">
                                    <label for="">Kelas</label>
                                    <p class="text-lg font-medium text-gray-800 mb-2">{{ $item->kelas_calon }}
                                    </p>
                                </div>
                                <div class="px-4 py-3 w-full bg-gray-300 rounded-lg mb-3">
                                    <label for="">Visi</label>
                                    <p class="text-lg font-medium text-gray-800 mb-2">{{ $item->visi_calon }}</p>
                                </div>
                                <div class="px-4 py-3 w-full bg-gray-300 rounded-lg mb-3">
                                    <label for="">Misi</label>
                                    <p class="text-lg font-medium text-gray-800 mb-2">{!! nl2br(e($item->misi_calon)) !!}</p>
                                </div>
                            </div>
                        </div>
                        <button class="btn"
                            onclick="document.getElementById('show{{ $item->id }}').close()">Close</button>
                    </div>
                </dialog>

                <!-- Edit -->
                <dialog id="edit{{ $item->id }}" class="modal">
                    <div class="modal-box">
                        <form method="dialog">
                            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 text-3xl"><i
                                    class="fa-duotone fa-solid fa-circle-xmark"></i></button>
                        </form>
                        <form action="{{ route('edit.kandidat', $item->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-2">
                                <label for="formFile" class="mb-2 inline-block  text-black ">Foto Calon</label>
                                <input
                                    class="relative m-0 block w-full min-w-0 flex-auto cursor-pointer rounded border border-solid border-secondary-500 bg-transparent bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-surface transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:me-3 file:cursor-pointer file:overflow-hidden file:rounded-none file:border-0 file:border-e file:border-solid file:border-inherit file:bg-transparent file:px-3  file:py-[0.32rem] file:text-surface focus:border-primary focus:text-gray-700 focus:shadow-inset focus:outline-none "
                                    type="file" id="formFile" accept=".jpg,.jpeg,.png" name="image_calon" />
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-white">
                                <div class="mb-4 mx-auto ">
                                    <label class="block text-gray-700 font-bold mb-2 text-white" for="name">
                                        Name
                                    </label>
                                    <input
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight "
                                        id="name" type="text" value="{{ $item->name_calon }}" name="name_calon">
                                </div>
                                <div class="mb-4 mx-auto">
                                    <label class="block text-gray-700 font-bold mb-2 text-white" for="kelas">
                                        Kelas
                                    </label>
                                    <input
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight "
                                        id="kelas" type="text" value="{{ $item->kelas_calon }}" name="kelas_calon">
                                </div>
                            </div>
                            <div class="mb-2 ">
                                <label class="block text-gray-700 font-bold mb-2 text-white" for="visi">
                                    Visa Calon
                                </label>
                                <textarea
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    name="visi_calon" id="" cols="30" rows="3">{{ $item->visi_calon }}</textarea>
                            </div>
                            <div class="mb-2">
                                <label class="block text-gray-700 font-bold mb-2 text-white" for="misi">
                                    Misi Calon
                                </label>
                                <textarea
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    name="misi_calon" id="" cols="30" rows="7">{{ $item->misi_calon }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-8">

                                <button type="submit"
                                    class="btn bg-transparent hover:bg-transparent  text-white border border-white">Save</button>

                            </div>
                        </form>
                    </div>
                </dialog>

                {{-- @include('dashboard.model-kandidat') --}}
            @endforeach
        </div>

    </div>
    <dialog id="new_kandidat" class="modal">
        <div class="modal-box">
            <p class="py-4">New Kandidat</p>
            <form action="{{ route('kandidat.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label for="formFile" class="mb-2 inline-block text-neutral-500 ">Foto Calon</label>
                    <input
                        class="relative m-0 block w-full min-w-0 flex-auto cursor-pointer rounded border border-solid border-secondary-500 bg-transparent bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-surface transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:me-3 file:cursor-pointer file:overflow-hidden file:rounded-none file:border-0 file:border-e file:border-solid file:border-inherit file:bg-transparent file:px-3  file:py-[0.32rem] file:text-surface focus:border-primary focus:text-gray-700 focus:shadow-inset focus:outline-none "
                        type="file" id="formFile" accept=".jpg,.jpeg,.png" name="image_calon" />
                </div>

                <div class="flex flex-row gap-4">
                    <div class="mb-2 w-full">
                        <label class="block text-gray-700 font-bold mb-2" for="name">
                            Nama Calon
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="name" type="text" placeholder="your Name Calon" name="name_calon">
                    </div>
                    <div class="mb-2 w-full">
                        <label class="block text-gray-700 font-bold mb-2" for="kelas">
                            Kelas Calon
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="kelas" type="text" placeholder="your Kelas Calon" name="kelas_calon">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="block text-gray-700 font-bold mb-2" for="visi">
                        Visa Calon
                    </label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        name="visi_calon" id="" cols="30" rows="3"></textarea>
                </div>
                <div class="mb-2">
                    <label class="block text-gray-700 font-bold mb-2" for="misi">
                        Misi Calon
                    </label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        name="misi_calon" id="" cols="30" rows="5"></textarea>
                </div>
                <button type="submit" class="btn btn-primary me-5">Save</button>
                <button class="btn">Close</button>
            </form>
        </div>
    </dialog>
    <script>
        function showEditModal(id) {
            // Hide all other modals
            const modals = document.querySelectorAll('[id^="edit"]');
            modals.forEach(modal => {
                if (modal.id !== 'edit' + id) {
                    modal.classList.add('hidden');
                }
            });

            // Show the selected modal
            document.getElementById('edit' + id).classList.remove('hidden');
        }

        function closeEditModal(id) {
            // Hide the current modal
            document.getElementById('edit' + id).classList.add('hidden');
        }
    </script>
@endsection
