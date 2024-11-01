@extends('index')
@section('title', 'Danh sách quyền hạn')

@section('main')

<div class="container-fluid">

<div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Custom Checkboxes</h4>
                                    <div class="flex-shrink-0">
                                        <div class="form-check form-switch form-switch-right form-switch-md">
                                            <label for="custom-checkboxes-showcode" class="form-label text-muted">Show Code</label>
                                            <input class="form-check-input code-switcher" type="checkbox" id="custom-checkboxes-showcode">
                                        </div>
                                    </div>
                                </div><!-- end card header -->
                                <div class="card-body">
                                    <div class="live-preview">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div>

                                                    <p class="text-muted">Use <code>form-check-</code> class with below-mentioned color variation to set a color checkbox.</p>
                                                    <!-- Bootstrap Custom Checkboxes color -->
                                                    <div>
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck6" checked>
                                                            <label class="form-check-label" for="formCheck6">
                                                                Checkbox Primary
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-secondary mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck7" checked>
                                                            <label class="form-check-label" for="formCheck7">
                                                                Checkbox Secondary
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-success mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck8" checked>
                                                            <label class="form-check-label" for="formCheck8">
                                                                Checkbox Success
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-warning mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck9" checked>
                                                            <label class="form-check-label" for="formCheck9">
                                                                Checkbox Warning
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-danger mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck10" checked>
                                                            <label class="form-check-label" for="formCheck10">
                                                                Checkbox Danger
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-info mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck11" checked>
                                                            <label class="form-check-label" for="formCheck11">
                                                                Checkbox Info
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-dark">
                                                            <input class="form-check-input" type="checkbox" id="formCheck12" checked>
                                                            <label class="form-check-label" for="formCheck12">
                                                                Checkbox Dark
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end col-->

                                            <div class="col-md-6">
                                                <div class="mt-4 mt-md-0">
                                                    <p class="text-muted">Use <code>form-check-outline</code> class and <code>form-check-</code> class with below-mentioned color variation to set a color checkbox with outline.</p>
                                                    <!-- Bootstrap Custom Outline Checkboxes -->
                                                    <div>
                                                        <div class="form-check form-check-outline form-check-primary mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck13" checked>
                                                            <label class="form-check-label" for="formCheck13">
                                                                Checkbox Outline Primary
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-outline form-check-secondary mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck14" checked>
                                                            <label class="form-check-label" for="formCheck14">
                                                                Checkbox Outline Secondary
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-outline form-check-success mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck15" checked>
                                                            <label class="form-check-label" for="formCheck15">
                                                                Checkbox Outline Success
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-outline form-check-warning mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck16" checked>
                                                            <label class="form-check-label" for="formCheck16">
                                                                Checkbox Outline Warning
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-outline form-check-danger mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck17" checked>
                                                            <label class="form-check-label" for="formCheck17">
                                                                Checkbox Outline Danger
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-outline form-check-info mb-3">
                                                            <input class="form-check-input" type="checkbox" id="formCheck18" checked>
                                                            <label class="form-check-label" for="formCheck18">
                                                                Checkbox Outline Info
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-outline form-check-dark">
                                                            <input class="form-check-input" type="checkbox" id="formCheck19" checked>
                                                            <label class="form-check-label" for="formCheck19">
                                                                Checkbox Outline Dark
                                                            </label>
                                                        </div>
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