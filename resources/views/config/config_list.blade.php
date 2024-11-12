@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách Config</h4>
                </div><!-- end card header -->
                
                <div class="card-body">
                    <div class="live-preview">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Logo header</th>
                                        <th scope="col">Logo footer</th>
                                        <th scope="col">main Color</th>
                                        <th scope="col">Icon</th>
                                        <th scope="col">Thumbnail</th>
                                        <th scope="col">Logo Admin</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                
                                    @foreach($configs as $config)
                                    <tr>
                                        <th scope="row"><a href="#" class="fw-medium">{{$config->id}}</a></th>
                                        <td style="max-width: 100px; height: 100px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                            <img src="{{ $config->logo_header }}" alt="Post Image" style="max-width: 100%; height: auto;">
                                        </td>
                                        <td style="max-width: 100px; height: 100px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                            <img src="{{$config->logo_footer}}" alt="Post Image" style="max-width: 100%; height: auto;">
                                        </td>
                                      
                                        <td>{{$config->main_color}}</td>
                                        <td style="max-width: 100px; height: 100px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                            <img src="{{ $config->icon }}" alt="Post Image" style="max-width: 50%; height: auto;">
                                        </td>
                                        <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                            {{ $config->thumbnail}}
                                        </td>
                                        <td style="max-width: 100px; height: 100px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                            <img src="{{ $config->logo_admin }}" alt="Post Image" style="max-width: 100%; height: auto;">
                                        </td>
                                        <td>              
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#editModal-{{ $config->id}}">
                                                <button type="button" class="btn btn-primary" title="Chỉnh sửa">
                                                    Chỉnh sửa
                                                </button>
                                            </a>
                                        
                                            <!-- Modal Chỉnh sửa -->
                                            <div class="modal fade" id="editModal-{{ $config->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $config->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editModalLabel-{{ $config->id }}">Chỉnh sửa config</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ route('config.update', ['id' => $config->id, 'tab' => 1, 'token' => auth()->user()->refesh_token]) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                
                                                                <!-- Logo Header -->
                                                                <div class="mb-3">
                                                                    <label for="logo_header" class="form-label">Logo Header</label>
                                                                    <div>
                                                                        <img src="{{ $config->logo_header }}" alt="Logo Header" style="max-width: 100px; height: auto;">
                                                                    </div>
                                                                    <input type="file" name="logo_header" id="logo_header" class="form-control" accept="image/*">
                                                                </div>
                                            
                                                                <!-- Logo Footer -->
                                                                <div class="mb-3">
                                                                    <label for="logo_footer" class="form-label">Logo Footer</label>
                                                                    <div>
                                                                        <img src="{{ $config->logo_footer }}" alt="Logo Footer" style="max-width: 100px; height: auto;">
                                                                    </div>
                                                                    <input type="file" name="logo_footer" id="logo_footer" class="form-control" accept="image/*">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="main_color" class="form-label">Main Color</label>
                                                                    <input type="text" name="main_color" id="main_color" class="form-control" value="{{ $config->main_color }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="icon" class="form-label">Icon</label>
                                                                    <div>
                                                                        <img src="{{ $config->icon }}" alt="Icon" style="max-width: 100px; height: auto;">
                                                                    </div>
                                                                    <input type="file" name="icon" id="icon" class="form-control" accept="image/*">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="thumbnail" class="form-label">ThumBnail</label>
                                                                    <input type="text" name="thumbnail" id="thumbnail" class="form-control" value="{{ $config->thumbnail }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="logo_admin" class="form-label">logo admin</label>
                                                                    <div>
                                                                        <img src="{{ $config->logo_admin }}" alt="logo_admin" style="max-width: 100px; height: auto;">
                                                                    </div>
                                                                    <input type="file" name="logo_admin" id="logo_admin" class="form-control" accept="image/*">
                                                                </div>
                                                                
                                                                
                                                                <div class="mb-3 form-check">
                                                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $config->is_active ? 'checked' : '' }}>
                                                                    <label for="is_active" class="form-check-label">Is Active</label>
                                                                </div>
                                                                
                                                                <input type="hidden" name="update_by" value="{{ auth()->user()->id }}">
                                                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                        </td>
                                      
                                       
                                      
                                      
                                    </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
   </div>


@endsection
