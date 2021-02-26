<div  class="table-responsive">
  <table  class="table table-bordered table-hover table-striped table-striped">
    <thead class="bg-primary">
      <tr class="text-center align-items-center">
        <th rowspan="2" class="text-white">No</th>
        <th rowspan="2" class="text-white">Nama Barang</th>
        <th rowspan="2" class="text-white">Spesifikasi</th>
        <th colspan="2" class="text-white">Stok Barang</th>
        <th rowspan="2" class="text-white">Satuan</th>
        <th rowspan="2" class="text-white">Gambar</th>
        <th rowspan="2" class="text-white">#</th>
      </tr>
      <tr class="text-center align-items-center">
        <th class="text-white">Awal</th>
        <th class="text-white">Akhir</th>
      </tr>

    </thead>
    <tbody>
      @foreach ($records as $key => $row)
        <tr class="bg-white">
          <td class="text-center">{{ $key + $records->firstItem() }}</td>
          <td>{{ $row->product_name }}</td>
          <td>{{ $row->spesification }}</td>
          <td>{{ $row->stock_awal }}</td>
          <td>{{ $row->stock_akhir }}</td>
          <td class="text-center">{{ $row->satuan }}</td>
          <td class="text-center">
            @if (count($row->fileImages) > 0)
              <img src="{{ asset('storage/'. $row->fileImages[0]->filepath) }}" width="80" height="50"  />
            @else
              NO IMAGE FILE
            @endif
          </td>

          <td class="text-center">
            <a href="{{ url('products/'.$row->id.'/edit') }}" class="btn btn-sm btn-info">
              <i class="fas fa-edit"></i> Ubah
            </a>
            
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{ $row->id }}')">
              <i class="fas fa-trash"></i> Hapus
            </button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{ $records->links('customs.pagination') }}