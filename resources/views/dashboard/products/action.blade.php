<div class="d-flex jutify-content-center">
    <a href="{{ route('products.edit', $id) }}" class="btn btn-sm btn-warning"><i class="fas fa-pencil-alt"></i></a>
    <form id="delete-form-{{ $id }}" action="{{ route('products.destroy', $id) }}" class="d-none" method="post">
        @csrf
        @method('DELETE')
    </form>
    <button onclick="deleteForm('{{ $id }}')" class="btn btn-sm btn-danger"><i
            class="fas fa-trash"></i></button>
</div>

<script>
    function deleteForm(id) {
        Swal.fire({
            title: 'Hapus data',
            text: "Anda akan menghapus data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Batal!'
        }).then((result) => {
            if (result.isConfirmed) {
                $(`#delete-form-${id}`).submit()
            }
        })
    }
</script>
