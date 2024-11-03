@extends('index')
@section('title', 'Danh sách quyền hạn')

@section('main')

<div class="container-fluid">

<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Cấp quyền cho vai trò: {{ $role->title }}</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="live-preview">
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <p class="text-muted">Cấp quyền truy cập</p>
                                    <!-- Bootstrap Custom Checkboxes color -->
                                    <div>
                                        <form action="{{route('grant_access',['token' => auth()->user()->refesh_token])}}" method="post">
                                            @csrf
                                            @php
                                                $half = ceil(count($permissions) / 2);
                                            @endphp
                                            <div class="row">
                                                <div class="col-md-4">
                                                    @foreach($permissions->slice(0, $half) as $permission)
                                                        <div class="form-check form-check-success mb-3">
                                                        <input name="permissions[]" value="{{$permission->id}}" class="form-check-input" type="checkbox" id="formCheck{{$permission->id}}" 
                                                        @if(in_array($permission->id, $role_premission->pluck('premission_id')->toArray())) checked @endif>
                                                            <label class="form-check-label" for="formCheck{{$permission->id}}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="col-md-4">
                                                    @foreach($permissions->slice($half) as $permission)
                                                        <div class="form-check form-check-success mb-3">
                                                        <input name="permissions[]" value="{{$permission->id}}" class="form-check-input" type="checkbox" id="formCheck{{$permission->id}}"
                                                        @if(in_array($permission->id, $role_premission->pluck('premission_id')->toArray())) checked @endif>
                                                        <label class="form-check-label" for="formCheck{{$permission->id}}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                                            <button type="submit" class="btn btn-primary">Cấp quyền</button>
                                            <a type="submit" href="{{ route('delete_access', ['token' => auth()->user()->refesh_token, 'role_id' => $role->id] ) }}" class="btn btn-danger">Đặt lại</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
</div>


@endsection