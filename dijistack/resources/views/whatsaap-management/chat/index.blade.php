@extends('partials.master')
@section('main')
<div class="whatsapp-clone-container">
    <div class="chat-list">
        <div class="chat-list-header">
            WhatsApp Görüşmeleri
        </div>
        <div class="chat-search">
            <input type="text" id="searchChat" placeholder="Ara veya yeni sohbet başlat">
        </div>
        <div class="chat-items" id="chat-items">
            @foreach($latestMessages as $msg)
            <div class="chat-item" data-session="{{ $msg->session_id }}">
                <div class="chat-avatar">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($msg->customer_name) }}&background=random&color=fff" alt="avatar">
                </div>
                <div class="chat-info">
                    <div class="chat-name">{{ $msg->customer_name }}</div>
                    <div class="chat-last-message">{{ Str::limit($msg->last_message, 30) }}</div>
                </div>
                <div class="chat-time">{{ \Carbon\Carbon::parse($msg->last_message_time)->format('H:i') }}</div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="chat-panel" id="chat-panel">
        <div class="chat-header" id="chat-header">
            <div class="chat-header-info">
                <img src="https://via.placeholder.com/40" alt="avatar" class="chat-header-avatar">
                <div>
                    <strong>Mesaj Seçin</strong>
                    <small>Sol panelden bir sohbet seçin</small>
                </div>
            </div>
        </div>
        <div class="chat-messages" id="chat-messages">
            <div class="placeholder">Sol taraftaki listeden bir sohbet seçin.</div>
        </div>
        <div class="chat-input" id="chat-input" style="display:none;">
            <form id="chatForm">
                @csrf
                <input type="hidden" name="session_id" id="session_id">
                <input type="text" name="message" placeholder="Mesaj yaz..." autocomplete="off" required>
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>

<style>
/* Genel Container */
.whatsapp-clone-container {
    display: flex;
    height: 90vh;
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    background-color: #f0f0f0;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

/* Sol Panel */
.chat-list {
    width: 32%;
    background-color: #f8f9fa;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #ddd;
}

.chat-list-header {
    padding: 16px;
    font-weight: bold;
    font-size: 1.2em;
    text-align: center;
    background-color: #ededed;
    border-bottom: 1px solid #ccc;
}

.chat-search {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

.chat-search input {
    width: 100%;
    padding: 8px 12px;
    border-radius: 20px;
    border: 1px solid #ccc;
    outline: none;
}

.chat-items {
    flex: 1;
    overflow-y: auto;
}

.chat-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background 0.2s;
}

.chat-item:hover, .chat-item.active {
    background-color: #e6f7ff;
}

.chat-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 12px;
}

.chat-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.chat-name {
    font-weight: bold;
}

.chat-last-message {
    color: gray;
    font-size: 0.9em;
    margin-top: 2px;
}

.chat-time {
    font-size: 0.75em;
    color: gray;
}

/* Sağ Panel */
.chat-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    background-color: #e5ddd5;
}

.chat-header {
    padding: 12px 16px;
    background-color: #ededed;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #ccc;
}

.chat-header-info {
    display: flex;
    align-items: center;
}

.chat-header-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.chat-header-info strong {
    display: block;
    font-size: 1em;
}

.chat-header-info small {
    font-size: 0.75em;
    color: gray;
}

.chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.message {
    max-width: 65%;
    padding: 10px 14px;
    margin-bottom: 12px;
    border-radius: 7px;
    word-wrap: break-word;
    line-height: 1.4em;
    position: relative;
}

.incoming {
    background-color: #fff;
    align-self: flex-start;
}

.outgoing {
    background-color: #dcf8c6;
    align-self: flex-end;
}

.message-time {
    font-size: 0.7em;
    color: gray;
    margin-top: 2px;
    text-align: right;
}

/* Chat Input */
.chat-input {
    padding: 10px 16px;
    border-top: 1px solid #ccc;
    background-color: #f0f0f0;
    display: flex;
}

.chat-input input[type=text] {
    flex: 1;
    padding: 10px 14px;
    border-radius: 20px;
    border: 1px solid #ccc;
    outline: none;
}

.chat-input button {
    background-color: #128c7e;
    border: none;
    color: white;
    padding: 0 16px;
    margin-left: 8px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1em;
}

.chat-input button:hover {
    background-color: #075e54;
}

/* Scrollbar */
.chat-items::-webkit-scrollbar, .chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-items::-webkit-scrollbar-thumb, .chat-messages::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 3px;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // Chat açma
    $('.chat-item').on('click', function(){
        var sessionId = $(this).data('session');

        // Aktif efekti
        $('.chat-item').removeClass('active');
        $(this).addClass('active');

        $('#chat-messages').html('<div class="placeholder">Yükleniyor...</div>');
        $('#chat-input').show();
        $('#session_id').val(sessionId);

        $.getJSON("/whatsapp/chat/" + sessionId, function(data){
            if(!data.contact){
                $('#chat-header').html('<div class="chat-header-info"><strong>Sohbet bulunamadı</strong></div>');
                $('#chat-messages').html('<div class="placeholder">Mesaj yok</div>');
                return;
            }

            $('#chat-header').html(`
                <div class="chat-header-info">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(data.contact.name)}&background=random&color=fff" class="chat-header-avatar">
                    <div>
                        <strong>${data.contact.name}</strong>
                        <small>${data.contact.phone}</small>
                    </div>
                </div>
            `);

            var chatMessages = $('#chat-messages');
            chatMessages.empty();

            $.each(data.messages, function(i, msg){
                var msgDiv = $(`
                    <div class="message ${msg.type === 'outgoing' ? 'outgoing' : 'incoming'}">
                        <div class="message-text">${msg.message}</div>
                        <div class="message-time">${new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</div>
                    </div>
                `);
                chatMessages.append(msgDiv);
            });

            chatMessages.scrollTop(chatMessages.prop("scrollHeight"));
        });
    });

    // Mesaj gönderme
    $('#chatForm').submit(function(e){
    e.preventDefault();

    var message = $('#chatForm input[name="message"]').val();
    var sessionId = $('#session_id').val();

    if(!message) return;

    $.ajax({
        url: '/whatsapp/chat/send',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: JSON.stringify({
            session_id: sessionId,
            message: message
        }),
        contentType: 'application/json',
        success: function(data){
            var div = $('<div class="message outgoing">')
                .html('<div class="message-text">'+message+'</div><div class="message-time">'+new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})+'</div>');
            $('#chat-messages').append(div);
            $('#chatForm input[name="message"]').val('');
            $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
        },
        error: function(err){
            alert('Mesaj gönderilemedi!');
            console.error(err);
        }
    });
});


});
</script>
@endsection
