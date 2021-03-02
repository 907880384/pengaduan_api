<div class="table-responsive table-responsive-md">
  <table  class="table table-responsive-md table-bordered table-hover table-striped table-striped">
    <thead class="bg-primary">
      <tr class="text-center align-items-center">
        <th class="text-white">No</th>
        <th class="text-white">Nama Barang</th>
        <th class="text-white">Qty</th>
        <th class="text-white">Satuan</th>
        <th class="text-white">Status</th>
        <th class="text-white">Pemesan</th>
        <th class="text-white">#</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($records as $key => $row)
        <tr class="bg-white">
          <td class="text-center">{{ $key + $records->firstItem() }}</td>
          <td>{{ $row->product->product_name }}</td>
          <td>{{ $row->qty }}</td>
          <td>{{ $row->product->satuan }}</td>
          <td>{{ $row->status }}</td>
          <td>{{ $row->user->name }}</td>

          <td class="text-center">
            <button type="button" class="btn btn-primary btn-sm" onclick="deleteRow('{{ $row->id }}')">
              <i class="fas fa-check"></i> Agree
            </button>
            
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{ $row->id }}')">
              <i class="fas fa-times"></i> Cancel
            </button>

            <a href="{{ url('orders/'.$row->id) }}" class="btn btn-sm btn-info">
              <i class="fas fa-eye"></i> View
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{ $records->links('customs.pagination') }}