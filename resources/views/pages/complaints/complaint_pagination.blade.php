<div  class="table-responsive">
  <table  class="table table-md table-striped table-hover table-bordered">
    <thead>
      <tr class="text-center bg-primary text-white">
        <th>#</th>
        <th>Judul</th>
        <th>Pesan</th>
        <th>Perihal</th>
        <th>Status</th>
        <th>Pengirim</th>
        <th>Pelaksana</th>
        <th>Tujuan</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($records as $key => $row)
        <tr>
          {{-- No --}}
          <td class="text-center">{{ $key + $records->firstItem() }}</td>
          
          {{-- Judul --}}
          <td class="text-center">
            {{ strlen($row->title) > 50 ? substr($row->title, 0, 50) .'...(more)' : substr($row->title, 0, strlen($row->title)) }}
          </td>

          {{-- Pesan --}}
          <td>
            {{ strlen($row->messages) > 50 ? substr($row->messages, 0, 50) . '...(more)' : substr($row->messages, 0, strlen($row->messages)) }}
          </td>

          {{-- Perihal --}}
          <td class="text-center">
            @if ($row->is_urgent)
              <div class="badge badge-danger">
                PENTING
              </div>
            @else 
              <div class="badge badge-info">
                BIASA
              </div>
            @endif
          </td>

          {{-- Status --}}
          <td class="text-center">
            @if ($row->is_finished)
              <div class="badge badge-primary">
              SELESAI
              </div> 
            @else
            <div class="badge badge-secondary">
              BELUM SELESAI
            </div>
            @endif
          </td>
          
          {{-- Pengirim --}}
          <td class="text-center">{{ $row->sender->name }}</td>
          
          {{-- Pelaksana --}}
          <td class="text-center">
            @if ($row->executor)
              <b>{{ $row->executor->name }}</b>
            @else
              &nbsp;
            @endif
          </td>

          <td class="text-center">
            <b>{{ $row->types != null ? $row->types->name : 'MENUNGGU' }}</b>
          </td>

          <td class="text-center">
            <a href="{{ url('complaints/show/detail/' . $row->id) }}" class="btn btn-sm btn-info">
              <i class="fas fa-eye"></i> Detail
            </a>
            
            @if (auth()->user()->roles()->first()->slug === 'admin')
              @if (!$row->is_assigned && !$row->is_finished)
              &nbsp;
              <button class="btn btn-primary btn-sm" onclick="showAssignModal('{{ $row->id }}', '{{ $row->type_id }}')">
                <i class="fas fa-tag"></i> Tugaskan
              </button>
              @endif
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{ $records->links('customs.pagination') }}