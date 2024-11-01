@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
        <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách tài khoản quản lý</h4>
                        <a
                            href="{{ route('costomer', ['token' => auth()->user()->refesh_token]) }}"
                            class="nav-link text-primary"
                            style="font-weight: bold;"
                            data-key="t-ecommerce"
                        >
                            Danh sách Khách hàng
                        </a>
                        <div style="width: 20px;">/</div> 
                        <a
                            href="{{ route('manager',['token' => auth()->user()->refesh_token]) }}"
                            class="nav-link text-primary"
                            style="font-weight: bold;"
                            data-key="t-ecommerce"
                        >
                            Danh sách user quản lý
                        </a>

                        <div style="width: 20px;">/</div> 
                        <a
                            href="{{ route('trash_user',['token' => auth()->user()->refesh_token]) }}"
                            class="nav-link text-primary"
                            style="font-weight: bold;"
                            data-key="t-ecommerce"
                        >
                            User đã xóa
                        </a>
                        <div style="width: 20px;"></div> 
                        <!-- Toggle Between Modals -->
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal">Thêm tài khoản</button>
                    <!-- First modal dialog -->
                    <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                <form id="addUser">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="firstNameinput" class="form-label">First Name</label>
                                                <input type="text" class="form-control" placeholder="Enter your firstname" id="firstNameinput">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="lastNameinput" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" placeholder="Enter your lastname" id="lastNameinput">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="compnayNameinput" class="form-label">Company Name</label>
                                                <input type="text" class="form-control" placeholder="Enter company name" id="compnayNameinput">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="phonenumberInput" class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" placeholder="+(245) 451 45123" id="phonenumberInput">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="emailidInput" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" placeholder="example@gamil.com" id="emailidInput">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="address1ControlTextarea" class="form-label">Address</label>
                                                <input type="text" class="form-control" placeholder="Address 1" id="address1ControlTextarea">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="citynameInput" class="form-label">City</label>
                                                <input type="email" class="form-control" placeholder="Enter your city" id="citynameInput">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="ForminputState" class="form-label">State</label>
                                                <select id="ForminputState" class="form-select">
                                                    <option selected>Choose...</option>
                                                    <option>...</option>
                                                </select>
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-lg-12">
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </form>

                                <script>
                                document.getElementById('addCategoryForm').addEventListener('submit', async function (event) {
                                    event.preventDefault(); // Ngăn chặn hành vi mặc định của form
                                    await addCategory();
                                });

                                async function addCategory() {
                                    const urlParams = new URLSearchParams(window.location.search);
                                    const token = urlParams.get('token');
                                    console.log(token);
                                    
                                    const title = document.getElementById('title').value;
                                    const index = document.getElementById('index').value;
                                    const image = document.getElementById('image').files; // Nếu bạn có URL của ảnh
                                    const status = document.getElementById('status').value;
                                    const parentId = document.getElementById('parentId').value;
                                    console.log(image);
                                    
                                    const data = {
                                        title: title,
                                        index: index,
                                        image: image,
                                        status: status,
                                        parent_id: parentId,
                                        update_by: {{ auth()->user()->id }}
                                    };

                                    try {
                                        const res = await fetch(`https://vnshop.top/api/categories`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Authorization': `Bearer ${token}` // Gắn Bearer Token vào header
                                            },
                                            body: JSON.stringify(data)});
                                        const payload = await res.json();
                                        if(!res.ok) {
                                            throw new Error('Network response was not ok');
                                        }
                                        alert('Thêm danh mục thành công!');
                                        window.location.reload();
                                        } catch (error) {
                                        console.error('Error:', error);
                                        alert('Đã xảy ra lỗi: ' + error.message);
                                        }
                                }
                                </script>


                                </div>
                            </div>
                        </div>
                    </div>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Ảnh đại diện</th>
                                            <th scope="col">Thông tin tài khoản</th>
                                            <th scope="col">Địa chỉ</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Mức rank và tích điểm</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($users->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có tài khoản khách hàng nào.</td>
                                            </tr>
                                        @else
                                            @foreach($users as $user)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $user->id }}</a></th>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        <img src="{{$user->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="Avatar" class="avatar-xs rounded-circle me-3 material-shadow" style="width: 60px; height: 60px;">
                                                    </td>
                                                    <td style="display: flex; align-items: center;">
                                                        <div style="display: flex; flex-direction: column;">
                                                            <span style="font-weight: bold;">{{$user->fullname ?? 'No Name'}}</span>
                                                            <span style="color: gray;">{{$user->email ?? 'No Email'}}</span>
                                                            <span style="color: gray;">{{$user->phone ?? 'No Phone'}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @foreach($user->address as $address)
                                                            *. {{ $address->district }}_{{ $address->ward }}_{{ $address->address }}<br>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $user->created_at}}</td>
                                                    <td>
                                                        {{$user->rank->title ?? "Vô danh"}}: {{$user->point}} điểm tích lũy
                                                    <td>
                                                    <ul class="list-inline">
                                                        @if ($user->status == 1)
                                                            <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('change_user', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $user->id,
                                                                                                        'status' => 2,
                                                                                                        ]) }}"
                                                                >
                                                                    <button type="button" class="btn rounded-pill btn-warning waves-effect waves-light">Khóa</button>
                                                                </a>
                                                            </li>
                                                        @elseif ($user->status == 2)
                                                            <li class="list-inline-item">
                                                            <a 
                                                                href="{{ route('change_user', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $user->id,
                                                                                                    'status' => 1,
                                                                                                    ]) }}"
                                                            >
                                                                <button type="button" class="btn rounded-pill btn-success waves-effect waves-light">mở</button>
                                                            </li>
                                                        @endif
                                                            
                                                        <li class="list-inline-item">
                                                            <a 
                                                                    href="{{ route('change_user', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $user->id,
                                                                                                        'status' => 0,
                                                                                                        ]) }}"
                                                            >
                                                                <button type="button" class="btn rounded-pill btn-danger waves-effect waves-light">Xóa</button>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                       
                                                    </td>
                                                    
                                                    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Pagination Links -->
                        
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="mt-3">
                                    {{ $users->appends(['token' => auth()->user()->refesh_token])->links() }}
                                </div>
                                <a
                                    href="{{ route('trash_user',['token' => auth()->user()->refesh_token]) }}"
                                    class="nav-link text-primary"
                                    style="font-weight: bold;"
                                    data-key="t-ecommerce"
                                >
                                    User đã xóa
                                </a>
                            </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            
        </div> 


    </div>
   </div>


@endsection
