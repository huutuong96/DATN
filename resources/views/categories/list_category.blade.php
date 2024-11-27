@extends('index')
@section('title', 'List Store')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách danh mục</h4>
                    <!-- Toggle Between Modals -->
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal">Thêm danh mục</button>
                    <!-- First modal dialog -->
                    <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <form id="addBlogForm" action="{{ route('create_category', ['token' => auth()->user()->refesh_token]) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Tên doanh mục</label>
                                                    <input type="text" class="form-control" placeholder="Enter category title" name="title" required>
                                                </div>
                                            </div>
                                            
                                            <input type="hidden" name="back" value="1">
                                            
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="index" class="form-label">Vị trí</label>
                                                    <input type="number" class="form-control" value="1" name="index" required>
                                                </div>
                                            </div>
                                    
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Hình ảnh</label>
                                                    <input type="file" class="form-control" placeholder="Enter image URL" name="image">
                                                </div>
                                            </div>
                                    
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Trạng thái</label>
                                                    <input type="number" class="form-control" value="1" name="status" required>
                                                </div>
                                            </div>
                                            {{-- @dd($categoryTree); --}}
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="parent_id" class="form-label">Chọn danh mục cha</label>
                                                    <select name="parent_id" id="parent_id" class="form-control">
                                                        <option value="0">Không thuộc danh mục nào</option> 
                                                        <option value="0">Danh mục khác (không phân loại rõ ràng)</option>                                                  
                                                        @foreach ($categoryTree as $category)
                                                      
                                                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                                                            @if ($category->children && $category->children->isNotEmpty())
                                                                @foreach ($category->children as $child)
                                                                    <option value="{{ $child->id }}">{{ '-- ' . $child->title }}</option>
                                                                    @if ($child->children && $child->children->isNotEmpty())
                                                                        @foreach ($child->children as $grandchild)
                                                                            <option value="{{ $grandchild->id }}">{{ '--- ' . $grandchild->title }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    
                                                    
                                                    
                                                </div>
                                            </div>
                                    
                                            <div class="col-lg-12 mb-3">
                                                <label for="tax_id" class="form-label">Chọn thuế danh mục</label>
                                                <select name="tax_id" class="form-control js-example-templating">
                                                    @foreach($taxes as $tax)
                                                        <option value="{{$tax->id}}">{{$tax->title}} | {{$tax->rate}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">Thêm</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
                
                <div class="card-body">
                    <div class="live-preview">
                        <div class="table-responsive">
                            <table id="cateall" class="table align-middle table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tên danh mục</th>
                                        <th scope="col">Hình ảnh</th>
                                        <th scope="col">Danh mục con của</th>
                                        <th scope="col">Thuế</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                
                                    @foreach($categories as $category)
                                    <tr>
                                        <th scope="row"><a href="#" class="fw-medium">{{$category->id}}</a></th>
                                        <td>{{$category->title}}</td>
                                        <td><img src="{{$category->image ?? 'assets/images/users/avatar-1.jpg'}}" alt="" class="avatar-xs rounded-circle me-2 material-shadow"></td>
                                        <td>
                                        <!-- {{$category->parent_id ?? "Đây là danh mục cha"}} -->
                                             @if ($category->parent_id === null || $category->parent_id == 0)
                                                <option value="0" {{ old('parent_id', $category->parent_id) === 0 ? 'selected' : '' }}>Doanh mục cha</option>
                                            @else
                                                @foreach($categories as $detail)
                                                    @if ($category->parent_id === $detail->id)
                                                        <option value="{{$detail->id}}">{{$detail->title}}</option>
                                                    @endif
                                                <!-- <option value="{{$category->parent_id}}">đây là danh mục chưa sửa</option> -->
                                                @endforeach 
        
                                            @endif
                                        </td>
                                        <td>
                                        <div class="col-lg-8">
                                            <select name="tax_id" class="form-control js-example-templating">
                                                <option>Danh Mục Chưa Có Thuế</option>
                                                @foreach($taxes as $tax)
                                                    @php
                                                        $selected = false;
                                                    @endphp
                                                    @foreach($tax_category as $tc)
                                                        @if($tc->category_id == $category->id && $tc->tax_id == $tax->id)
                                                            @php
                                                                $selected = true;
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                    <option value="{{$tax->id}}" {{ $selected ? 'selected' : '' }}>{{$tax->title}} | {{$tax->rate}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        </td>
                                        <td>
                                            <ul class="list-inline">
                                                @if ($category->status == 1)
                                                    <li class="list-inline-item">
                                                        <a 
                                                            href="{{ route('change_category', [
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $category->id,
                                                                                                'status' => 2,
                                                                                                ]) }}"
                                                        >
                                                            <button type="button" class="btn btn-warning waves-effect waves-light" title="Khóa">
                                                                <i class="ri-lock-line align-middle"></i>
                                                            </button>
                                                        </a>
                                                    </li>
                                                @elseif ($category->status == 2)
                                                    <li class="list-inline-item">
                                                    <a 
                                                        href="{{ route('change_category', [
                                                                                            'token' => auth()->user()->refesh_token,
                                                                                            'id' => $category->id,
                                                                                            'status' => 1,
                                                                                            ]) }}"
                                                    >
                                                    <button type="button" class="btn btn-success" title="Bật">
                                                        <i class="ri-check-line align-middle"></i>
                                                    </button>
                                                       
                                                    </li>
                                                @endif
                                                <li class="list-inline-item">
                                                   <!-- Toggle Between Modals -->
                                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal{{ $category->id }}" title="Chỉnh sửa"><i class="ri-edit-line align-middle"></i></button>
                                                    <!-- First modal dialog -->
                                                    <div class="modal fade" id="firstmodal{{ $category->id }}" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-body text-center p-5">
                                                                <form id="addBlogForm"
                                                                                    action="{{ route('update_category', [
                                                                                        'token' => auth()->user()->refesh_token,
                                                                                    ]) }}"
                                                                                    method="POST" 
                                                                                    enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input type="hidden" name="_method" value="PUT">
                                                                    <input type="hidden" name="id" value="{{$category->id}}"> <!-- Thêm trường để gửi ID của danh mục -->
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="mb-3">
                                                                                <label for="titleInput" class="form-label">Tên doanh mục</label>
                                                                                <input name="title" type="text" class="form-control" value="{{$category->title}}" id="titleInput" required>
                                                                            </div>
                                                                        </div><!--end col-->            
                                                                        <input type="hidden" name="index" class="form-control" value="{{$category->index}}" id="indexInput" required>
                                                                        <div class="col-12">
                                                                            <div class="mb-3">
                                                                                <label for="imageInput" class="form-label">Hình ảnh</label>
                                                                                <input type="file" name="imageInput" class="form-control" placeholder="Enter image URL" id="imageInput">
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <input type="hidden" name="status" class="form-control" value="{{$category->status}}" id="statusInput" required>
                                                                        <div class="col-12">
                                                                            <div class="mb-3">
                                                                                <label for="parent_id" class="form-label">Chọn danh mục cha</label>
                                                                                <select name="parent_id" id="parent_id" class="form-control">
                                                                                        @if ($category->parent_id === null || $category->parent_id == 0)
                                                                                            <option value="0" {{ old('parent_id', $category->parent_id) === 0 ? 'selected' : '' }}>Doanh mục cha</option>
                                                                                        @else
                                                                                            @foreach($categories as $detail)
                                                                                                @if ($category->parent_id === $detail->id)
                                                                                                    <option value="{{$detail->id}}">{{$detail->title}}</option>
                                                                                                @endif
                                                                                            <!-- <option value="{{$category->parent_id}}">đây là danh mục chưa sửa</option> -->
                                                                                            @endforeach 
                                                                                            
                                                                                        @endif
                                                                                    @foreach ($categoryTree as $category)
                                                                                        
                                                                                        <option value="{{ $category->id }}" {{ old('parent_id', $category->parent_id) == $category->id ? 'selected' : '' }}>
                                                                                            {{ $category->title }}
                                                                                        </option>
                                                                                        @if ($category->children && $category->children->isNotEmpty())
                                                                                            @foreach ($category->children as $child)
                                                                                                <option value="{{ $child->id }}" {{ old('parent_id', $category->parent_id) == $child->id ? 'selected' : '' }}>
                                                                                                    -- {{ $child->title }}
                                                                                                </option>
                                                                                                @if ($child->children && $child->children->isNotEmpty())
                                                                                                    @foreach ($child->children as $grandchild)
                                                                                                        <option value="{{ $grandchild->id }}" {{ old('parent_id', $category->parent_id) == $grandchild->id ? 'selected' : '' }}>
                                                                                                            --- {{ $grandchild->title }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                @endif
                                                                                            @endforeach
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div><!--end col-->
                                                                        </div><!--end row-->
                                                                        
                                                                        <div class="col-lg-12 mb-3">
                                                                            <label for="tax_id" class="form-label">Chọn thuế danh mục</label>
                                                                            <select id="tax_id" name="tax_id" class="form-control js-example-templating">
                                                                                <option>Chọn Thuế Danh Mục</option>
                                                                                @foreach($taxes as $tax)
                                                                                    @php
                                                                                        $selected = false;
                                                                                    @endphp
                                                                                    @foreach($tax_category as $tc)
                                                                                        @if($tc->category_id == $category->id && $tc->tax_id == $tax->id)
                                                                                            @php
                                                                                                $selected = true;
                                                                                            @endphp
                                                                                        @endif
                                                                                    @endforeach
                                                                                    <option value="{{$tax->id}}" {{ $selected ? 'selected' : '' }}>{{$tax->title}} | {{$tax->rate}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                       
                                                                        
                                                                        <div class="col-lg-12">
                                                                            <div class="text-end">
                                                                                <button type="submit"  class="btn btn-primary">Cập nhật</button>
                                                                            </div>
                                                                        </div><!--end col-->
                                                                    </div><!--end row-->
                                                                </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-inline-item">
                                                <a 
                                                        href="{{ route('change_category', [
                                                                                            'token' => auth()->user()->refesh_token,
                                                                                            'id' => $category->id,
                                                                                            'status' => 5,
                                                                                            ]) }}"
                                                >
                                                    <button type="button" class="btn btn-danger waves-effect waves-light" title="xóa"><i class="ri-delete-bin-line align-middle"></i></button>
                                                </a>
                                                </li>
                                            </ul>
                                        </td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <script>
                                        new DataTable('#cateall', {
                                            language: {   
                                                lengthMenu: "Hiển thị _MENU_ Doanh mục bài viết",
                                                search: "Tìm kiếm:"
                                            },
                                           
                                        });
                                    </script>
                                    
                                </div>
                                <a
                                    href="{{ route('trash_category',['token' => auth()->user()->refesh_token]) }}"
                                    class="nav-link text-primary"
                                    style="font-weight: bold;"
                                    data-key="t-ecommerce"
                                >
                                    Danh mục đã xóa
                                </a>
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