<div  class="table-responsive-md">
  <table  class="table table-sm table-striped table-hover table-bordered">
    <thead>
      <tr class="text-center bg-primary text-white">
        <th>#</th>
        <th>Nama</th>
        <th>Unique ID/Username</th>
        <th>Roles</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      
      @foreach ($records as $key => $row)
        <tr>
          <td class="text-center">{{ $key + $records->firstItem() }}</td>
          <td>{{ $row->name }}</td>
          <td>{{ $row->username }}</td>
          <td class="text-center">{{ $row->roles()->first()->name }}</td>
          <td class="text-center">
            <button type="button" class="btn btn-danger" onclick="deleteRow('{{ $row->id }}')">
              <i class="fas fa-trash"></i> Delete
            </button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{ $records->links('customs.pagination') }}