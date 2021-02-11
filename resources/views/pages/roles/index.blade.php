@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Master Roles</h1>
  </div>

  <div class="section-body">
    <h2 class="section-title">List Roles</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body p-1">
            
            <div class="table-responsive-md">
              <table class="table table-sm table-striped table-hover table-bordered">
                <thead>
                  <tr class="text-center bg-primary text-white">
                    <th>#</th>
                    <th>Nama</th>
                    <th>Slug</th>
                  </tr>
                </thead>
                <tbody>
                  
                  @foreach ($records as $key => $row)
                    <tr>
                      <td class="text-center">{{ $key + $records->firstItem() }}</td>
                      <td>{{ $row->name }}</td>
                      <td>{{ $row->slug }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
	            
            </div>
            
          </div>

          {{ $records->links('customs.pagination') }}

        </div>
      </div>

    </div>
  </div>
</section>
@endsection