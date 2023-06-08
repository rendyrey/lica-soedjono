var baseUrl = function(url) {
    return base + url;
}
  
var dataTable = () => {

    $('.datatable-ajax').DataTable().destroy();
    $('.datatable-ajax').DataTable({
        "order": [],
        "dom": 'lrtip',
        responsive: true,
        deferRender: true,
        scrollY: 600,
        scrollCollapse: true,
        scroller: true,
        lengthChange: false,
        pageLength: 250,
       
        'ajax': {
            "type": "GET",
            "url": baseUrl('report/verification-validation-datatable'),
            "dataSrc": function(jsons) {
                console.log(jsons)
                var return_data = new Array();
               
                var index = 1;
                jsons.forEach(function(json) {
                    return_data.push({
                        'no': index,
                        'name': json.analyst_name,
                        'verification': json.jumlah_verifikasi,
                        'validation': json.jumlah_validasi,
                    })
                    index++;
                })
                return return_data;
            },
        },
        "columns": [
            {
                'data': 'no'
            },
            {
                'data': 'name'
            },
            {
                'data': 'verification'
            },
            {
                'data': 'validation'
            },
        ]
    });
}

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    dataTable();
    
});