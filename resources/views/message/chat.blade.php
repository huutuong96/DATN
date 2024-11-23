@extends('index')
@section('title' , 'Nhắn Tin')

@section('main')


<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">

    <!-- Start User chat -->
    <div class="user-chat w-100 overflow-hidden minimal-border">

        <div class="chat-content d-lg-flex">
            <!-- start chat conversation section -->
            <div class="w-100 overflow-hidden position-relative">
                <!-- conversation user -->
                <div class="position-relative">

                    <div class="position-relative" id="channel-chat">
                        <div class="p-3 user-chat-topbar">
                            <div class="row align-items-center">


                                <div class="col-sm-4 col-8">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 d-block d-lg-none me-3">
                                            <a href="javascript: void(0);" class="user-chat-remove fs-18 p-1"><i class="ri-arrow-left-s-line align-bottom"></i></a>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 chat-user-img online user-own-img align-self-center me-3 ms-0">
                                                    <img src="assets/images/users/avatar-2.jpg" class="rounded-circle avatar-xs" alt="">
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h5 class="text-truncate mb-0 fs-16"><a class="text-reset username" data-bs-toggle="offcanvas" href="#userProfileCanvasExample" aria-controls="userProfileCanvasExample">{{ $shop->shop_name }}</a></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-sm-8 col-4">
                                    <ul class="list-inline user-chat-nav text-end mb-0">
                                        <li class="list-inline-item m-0">
                                            <div class="dropdown">
                                                <button class="btn btn-ghost-secondary btn-icon" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i data-feather="search" class="icon-sm"></i>
                                                </button>
                                                <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                                                    <div class="p-2">
                                                        <div class="search-box">
                                                            <input type="text" class="form-control bg-light border-light" placeholder="Search here..." onkeyup="searchMessages()" id="searchMessage">
                                                            <i class="ri-search-2-line search-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="list-inline-item d-none d-lg-inline-block m-0">
                                            <button type="button" class="btn btn-ghost-secondary btn-icon" data-bs-toggle="offcanvas" data-bs-target="#userProfileCanvasExample" aria-controls="userProfileCanvasExample">
                                                <i data-feather="info" class="icon-sm"></i>
                                            </button>
                                        </li>

                                        <li class="list-inline-item m-0">
                                            <div class="dropdown">
                                                <button class="btn btn-ghost-secondary btn-icon" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i data-feather="more-vertical" class="icon-sm"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item d-block d-lg-none user-profile-show" href="#"><i class="ri-user-2-fill align-bottom text-muted me-2"></i> View Profile</a>
                                                    <a class="dropdown-item" href="#"><i class="ri-inbox-archive-line align-bottom text-muted me-2"></i> Archive</a>
                                                    <a class="dropdown-item" href="#"><i class="ri-mic-off-line align-bottom text-muted me-2"></i> Muted</a>
                                                    <a class="dropdown-item" href="#"><i class="ri-delete-bin-5-line align-bottom text-muted me-2"></i> Delete</a>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <!-- end chat user head -->
                        <div class="chat-conversation p-3 p-lg-4" id="chat-conversation" data-simplebar>
                            <ul class="list-unstyled chat-conversation-list" id="channel-conversation">
                                @foreach($message_detail as $detail)
                                    <li class="chat-list {{ $detail->send_by == $user->id ? 'right' : 'left' }}" id="chat-list-{{ $detail->id }}">
                                        <div class="conversation-list">
                                            <div class="user-chat-content">
                                                <div class="ctext-wrap">
                                                    <div class="ctext-wrap-content">
                                                        <p class="mb-0 ctext-content" style="color: black;"> {{$detail->content}} </p>
                                                    </div>
                                                    <div class="dropdown align-self-start message-box-drop"> <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="ri-more-2-fill"></i> </a>
                                                        <div class="dropdown-menu"> <a class="dropdown-item reply-message" href="#"><i class="ri-reply-line me-2 text-muted align-bottom"></i>Reply</a> <a class="dropdown-item" href="#"><i class="ri-share-line me-2 text-muted align-bottom"></i>Forward</a> <a class="dropdown-item copy-message" href="#" "=""><i class=" ri-file-copy-line me-2 text-muted align-bottom"></i>Copy</a> <a class="dropdown-item" href="#"><i class="ri-bookmark-line me-2 text-muted align-bottom"></i>Bookmark</a> <a class="dropdown-item delete-item" href="#"><i class="ri-delete-bin-5-line me-2 text-muted align-bottom"></i>Delete</a> </div>
                                                    </div>
                                                </div>
                                                <div class="conversation-name"> <small class="text-muted time">03:51 am</small> <span class="text-success check-message-icon"><i class="bx bx-check"></i></span> </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <!-- end chat-conversation-list -->

                        </div>
                        <div class="alert alert-warning alert-dismissible copyclipboard-alert px-4 fade show " id="copyClipBoardChannel" role="alert">
                            Message copied
                        </div>
                    </div>

                    <!-- end chat-conversation -->

                    <div class="chat-input-section p-3 p-lg-4">

                        <!-- <form id="chatinput-form" enctype="multipart/form-data"> -->
                            <div class="row g-0 align-items-center">
                                <div class="col-auto">
                                    <div class="chat-input-links me-2">
                                        <div class="links-list-item">
                                            <button type="button" class="btn btn-link text-decoration-none emoji-btn" id="emoji-btn">
                                                <i class="bx bx-smile align-middle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="chat-input-feedback">
                                        Please Enter a Message
                                    </div>
                                    <input id="message" type="text" class="form-control chat-input bg-light border-light" id="chat-input" placeholder="Type your message..." autocomplete="off">
                                    <input type="hidden" id="shop_id" value="{{ $shop->id }}">
                                </div>
                                <div class="col-auto">
                                    <div class="chat-input-links ms-2">
                                        <div class="links-list-item">
                                            <button onclick="sendMessage(event)" type="button" class="btn btn-success chat-send waves-effect waves-light">
                                                <i class="ri-send-plane-2-fill align-bottom"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <!-- </form> -->
                    </div>

                    <div class="replyCard">
                        <div class="card mb-0">
                            <div class="card-body py-3">
                                <div class="replymessage-block mb-0 d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="conversation-name"></h5>
                                        <p class="mb-0"></p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="button" id="close_toggle" class="btn btn-sm btn-link mt-n2 me-n3 fs-18">
                                            <i class="bx bx-x align-middle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        const messagesContainer = document.getElementById('messages');
        const shopId = document.getElementById('shop_id').value;
        // Lắng nghe sự kiện và hiển thị tin nhắn trong giao diện
        window.Echo.channel('chat')
            .listen('.message.sent', (e) => {
                const newMessage = document.createElement('div');
                newMessage.textContent = e.message;
                messagesContainer.appendChild(newMessage);
            });

        // Gửi tin nhắn qua API
        function sendMessage(event) {
            event.preventDefault();
            const message = document.getElementById('message').value;
            if (message.trim() === '') return;

            axios.post(`user_send/${shopId}`, { message })
                .then(() => {
                    document.getElementById('message').value = ''; // Clear input
                })
                .catch(err => console.error(err));
        }
    </script>
<!-- end chat-wrapper -->




@endsection