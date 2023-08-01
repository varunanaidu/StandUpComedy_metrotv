function docReady(fn) {
    console.log(document.readyState);
    // see if DOM is already available
    if (document.readyState === "complete" ||
        document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

docReady(function() {
    var resultContainer = document.getElementById('qr-reader-results');
    var idContainer = document.getElementById('ticket_ids');
    var lastResult = '';
    var countResults = 0;

    function onScanSuccess(decodedText, decodedResult) {
        if ( lastResult != decodedText ) {
            ++countResults;
            lastResult = decodedText;
            // Handle on success condition with the decoded message.
            $('#ticket_ids').val(decodedText);

            $.ajax({
                url: base_url + 'penukaran/find',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'key': decodedText
                },
                success: function(data) {
                    if (data.type == 'done') {
                        $('#ticket_ids').val(data.msg[0].ticket_ids);
                        $('#category').val(data.msg[0].category);
                        $('#ticket_count').val(data.msg[0].ticket_count);
                        $('#name').val(data.msg[0].name);
                        $('#email').val(data.msg[0].email);
                        $('#phone').val(data.msg[0].phone);
                        $('#foto_penukar').val(data.msg[0].foto_penukar);

                        $('#penukaranForm').trigger('submit');
                    } else {
                        Swal.fire('Failed !', data.msg, 'error');
                        $('#ticket_ids').val('');
                    }
                }
            });

        }
    }

    var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", {
            fps: 10,
            qrbox: 250
        });
    html5QrcodeScanner.render(onScanSuccess);
});

$(function() {

    $('#btnFind').on('click', function(event) {
        event.preventDefault();

        var ticket_ids = $('#ticket_ids').val();
        if (ticket_ids == '') {
            alert('Isi id terlebih dahulu');
        }

        $.ajax({
            url: base_url + 'penukaran/find',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'key': ticket_ids
            },
            success: function(data) {
                if (data.type == 'done') {
                        $('#category').val(data.msg.category);
                        $('#ticket_count').val(data.msg.ticket_count);
                        $('#name').val(data.msg.name);
                        $('#email').val(data.msg.email);
                        $('#phone').val(data.msg.phone);
                        $('#foto_penukar').val(data.msg.foto_penukar);
                        // $('#ticket_ids').val(data.msg[0].ticket_ids);
                        $('#ticket_ids').val(ticket_ids);
                } else {
                    Swal.fire('Failed !', data.msg, 'error');
                }
            }
        });
    });

    $('#penukaranForm').on('submit', function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url : base_url + 'penukaran/save_penukaran',
            type : "POST",
            dataType : "JSON",
            cache: false,
            contentType: false,
            processData: false,
            data : formData,
            beforeSend: function () {
                Swal.showLoading(); 
            },
            success : function (data) {
                if (data.type == 'done') {
                    $('#soundSuccess')[0].play();
                    Swal.fire('Success', data.msg , 'success');
                } else {
                    Swal.fire('Failed !', data.msg, 'error');
                }
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            }
        });
    });
});