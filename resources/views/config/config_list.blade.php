@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Thông tin sàn</h4>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#editModal-{{ $config->id}}">
                                                <button type="button" class="btn btn-primary" title="Chỉnh sửa">
                                                    <i class="ri-edit-line align-middle"></i>
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
                </div><!-- end card header -->
                
                <div class="card-body">
                <div class="container my-4">

                        <!-- Content Section -->
                         <div class="row">
                            <div class="col-8">
                                <div class="card-body contact-info bg-light">
                                         <!-- Header Section -->
                                    <div class="card mb-4">
                                        <div class="row g-0 align-items-center header p-3">
                                            <div class="col-md-2 text-center">
                                                <img src="{{ $config->icon }}" 
                                                    alt="Platform Logo" class="rounded-circle img-fluid" style="max-width: 80px;">
                                            </div>
                                            <div class="col-md-10">
                                                <h3 class="card-title mb-1">VN shop: <span class="text-primary">8</span></h3>
                                                <p class="text-muted mb-0">Màu chủ đạo: <span class="badge d-flex justyfile-content" style="background-color: {{ $config->main_color }}; height: 42px; width: 91px;">{{ $config->main_color }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h5 class="card-title">Thông tin khác</h5>
                                    <p class="mb-1"><strong>Tên Người Đại Diện:</strong> {{$information->name}}</p>
                                    <p class="mb-1"><strong>Email:</strong> <a href="mailto:hoang@gmail.com">{{$information->mail}}</a></p>
                                    <p class="mb-0"><strong>Đường dây nóng:</strong> <a href="tel:01122334455">{{$information->phone}}</a></p>
                                    ...
                                </div>
                            </div>
                            <div class="col-4">
                                    <div>
                                        <h4>logo header</h4>
                                        <div class="card">
                                            <img src="{{ $config->logo_header }}"  
                                                alt="Promotional Banner" class="card-img-top">
                                        </div>
                                    </div>
                                    <div>
                                        <h4>logo footer</h4>
                                        <div class="card">
                                            <img src="{{ $config->logo_footer }}" 
                                                alt="Secondary Banner" class="card-img-top">
                                        </div>
                                    </div>
                            </div>
                         </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
   </div>


@endsection
