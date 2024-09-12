<style>
    .card {
        border: none;
        box-shadow: none;
    }

    .card-send {
        display: flex;
        justify-content: end;
    }

    .whatsapp-card-send {
        background-color: #DCF8C6;
        border-radius: 12px;
        padding: 10px 15px;
        margin-bottom: 10px;
        max-width: 70%;
    }

    .whatsapp-card {
        background-color: #e4e0e0;
        border-radius: 12px;
        padding: 10px 15px;
        margin-bottom: 10px;
        max-width: 70%;
    }

    .whatsapp-message {
        font-size: 16px;
        line-height: 1.5;
    }

    .whatsapp-timestamp {
        text-align: right;
        font-size: 12px;
        color: #777;
    }

    .fa-check {
        color: blue;
    }

    .whatsapp-date {
        padding: 5px;
        background: #e7e7e7;
        border-radius: 10px;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .modal-content {
        max-height: 630px;
        overflow-x: auto;
    }

    .modal-header {
        position: sticky;
        top: 0;
        z-index: 99;
        background: #fff;
        padding: 10px 1rem;
    }
</style>
@php
$guards = ['admin'];
$userName = 'Dev';
    foreach ($guards as $guard) {
    if (auth()->guard($guard)->check()) {
        $userName = auth()->guard($guard)->user()->name;
        break;
    }
}
@endphp
<div class="modal fade" id="wa_msg" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="form_title_modal"></h4>
                <button type="button" class="btn text-secondary" id="close-whatsapp-chatmodal" data-bs-dismiss="modal"
                    aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <div id="loadingMessages" style="display: none; text-align: center;">
                <img src="https://i.postimg.cc/NF6hL0rM/giphy.webp" alt="Loading..." />
            </div>
            <div class="modal-body whatsapp_msg text-sm">
            </div>
            <form action="" method="post" id="wa_msg_form">
                @csrf
                <div class="modal-body text-sm">
                    <div class="form-group d-none">
                        <label for="">Put image</label>
                        <input type="text" name="whatsapp_img" class="form-control" id="wha_img_input">
                    </div>
                    <div class="form-group">
                        <label for="">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="what_msg_send" placeholder="Enter Message" name="msg" required></textarea>
                        <input type="number" class="form-control" id="phone_inp_id" name="phone_number_id"
                            style="display: none">
                    </div>
                </div>
                <div class="modal-footer text-sm">
                    <a href="javascript:void(0);" class="btn btn-sm btn-secondary m-1" data-bs-dismiss="modal">Close</a>
                    <a href="javascript:void(0);" class="btn btn-sm text-light m-1"
                        style="background-color: var(--wb-dark-red);" id="sendMessageBtn">Submit</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script>
    let lastMessageTimestamp = '';
    var currentPage = 1;
    var isFetchingMessages = false;
    var hasMoreMessages = true;
    var messageFetchInterval;
    var newmessageFetchInterval;
    var displayedMessages = [];
    var messageSendElement = document.getElementById("what_msg_send");
    $('#close-whatsapp-chatmodal').on('click', function() {
        resetChat();
    });

    $("#sendMessageBtn").click(function() {
        var $btn = $(this);
    var originalText = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i>');
    $btn.prop('disabled', true);

        var message = $("#what_msg_send").val();
        var recipient = $("#phone_inp_id").val();
        var img = $('#wha_img_input').val();
        var data = {
            message: message,
            recipient: recipient,
        };
        $.ajax({
            url: '{{ route('whatsapp_chat.send') }}',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                $('#message').val('');
                messageSendElement.value = '';
                $btn.html(originalText);
            $btn.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $btn.html(originalText);
            $btn.prop('disabled', false);
            }
        });
    });

    function resetChat() {
        currentPage = 1;
        $('.whatsapp_msg').empty();
        hasMoreMessages = true;
        isFetchingMessages = false;
        clearInterval(messageFetchInterval);
        clearInterval(newmessageFetchInterval);
        displayedMessages = [];
        lastMessageTimestamp = '';
    }

    function wamsg(num) {
        resetChat();
        let id = num;
        $('#phone_inp_id').val(num);

        function fetchMessages(page) {
            if (isFetchingMessages || !hasMoreMessages) return;
            $('#loadingMessages').show();
            isFetchingMessages = true;
            const data_url = `{{ route('whatsapp_chat.get', '') }}/${id}?page=${page}`;
            $.ajax({
                url: data_url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#loadingMessages').hide();
                    if (response.data.length > 0) {
                        const groupedMessages = groupMessagesByDate(response.data);
                        updateHTML(groupedMessages);
                        if (currentPage == 1) {
                            lastMessageTimestamp = response.data[0].time;
                        }
                        if (currentPage >= response.last_page) {
                            hasMoreMessages = false;
                        } else {
                            currentPage++;
                        }

                    }
                    if (currentPage > response.last_page) {
                        hasMoreMessages = false;
                        clearInterval(messageFetchInterval);
                    }
                    isFetchingMessages = false;
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    isFetchingMessages = false;
                }
            });
        }

        fetchMessages(currentPage);

        messageFetchInterval = setInterval(function() {
            fetchMessages(currentPage);
        }, 4000);

        function fetchNewMessages() {
            if(lastMessageTimestamp != ''){
                const data_url = `{{ route('whatsapp_chat.get_new', '') }}/${id}?lastTimestamp=${lastMessageTimestamp}`;
            $.ajax({
                url: data_url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(message => {
                            const messageHTML = buildMessageHTML(message);
                            $('.whatsapp_msg').append(messageHTML);
                        });
                        lastMessageTimestamp = response[response.length - 1].time;
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching new messages:", xhr.responseText);
                }
            });
            }
        }
        newmessageFetchInterval = setInterval(fetchNewMessages, 5000);


        function groupMessagesByDate(messages) {
            const groupedMessages = {};
            messages.forEach(message => {
                const date = formatTimestamp(message.timestamp);
                if (!groupedMessages[date]) {
                    groupedMessages[date] = [];
                }
                groupedMessages[date].push(message);
            });
            return groupedMessages;
        }

        function formatTimestamp(timestamp) {
            const messageDate = new Date(timestamp * 1000).toDateString();
            const today = new Date().toDateString();
            if (messageDate === today) {
                return 'Today';
            } else if (messageDate === new Date(Date.now() - 864e5).toDateString()) {
                return 'Yesterday';
            } else {
                return new Date(timestamp * 1000).toLocaleDateString();
            }
        }

        function updateHTML(groupedMessages) {
            console.log(groupedMessages)
            Object.entries(groupedMessages).forEach(([date, messages]) => {
                messages.forEach(message => {
                    if (!displayedMessages.includes(message.id)) {
                        const msgHTML = buildMessageHTML(message);
                        $('.whatsapp_msg').prepend(msgHTML);
                        displayedMessages.push(message.id);
                    }
                });
            });
        }
        function buildMessageHTML(message) {
            let bodyContent = '';
            const msgClass = message.is_sent ? 'whatsapp-card-send' : 'whatsapp-card';
            switch (message.type) {
                case 'text':
                    bodyContent = message.body;
                    break;
                case 'contact':
                case 'vcard': {
                    const contactInfo = JSON.parse(message.doc);
                    bodyContent = `Name: ${contactInfo.name}, Mobile: ${contactInfo.mobile}`;
                    break;
                }
                case 'image':
                    bodyContent = `<img src="${message.doc}" alt="image" style="max-width: 100%; height: auto;">`;
                    break;
                case 'audio':
                    bodyContent =
                        `<audio controls src="${message.doc}">Your browser does not support the audio element.</audio>`;
                    break;
                    case 'button':
                    bodyContent = message.body;
                    break;
                case 'location':
                    bodyContent = `<a href="${message.doc}" target="_blank">View Location</a>`;
                    break;
                case 'video':
                    bodyContent =
                        `<video controls style="max-width: 100%; height: auto;"><source src="${message.doc}" type="video/mp4">Your browser does not support the video tag.</video>`;
                    break;
                default:
                    bodyContent = 'Unsupported message type';
            }
            let html =
                `<div class="${msgClass}"><p class="whatsapp-message">${bodyContent}</p><p class="whatsapp-timestamp">${new Date(message.time).toLocaleString()} </p></div>`;
            if (message.is_sent == '1') {
                html = `<div class="card-send">${html}</div>`;
            }
            return html;
        }
    }
    $('#wa_msg').on('shown.bs.modal', function() {
        var modalBody = $(this).find('.modal-body');
        modalBody.scrollTop(modalBody.prop('scrollHeight'));
    });
</script>
