$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        let searchText = $(this).val();
        
        $.ajax({
            url: '../Backend/search.php',
            method: 'POST',
            data: { search: searchText },
            success: function(response) {
                // แสดงผลใน gallery-container แทน
                $('.gallery-container').html(response);
            }
        });
    });
});