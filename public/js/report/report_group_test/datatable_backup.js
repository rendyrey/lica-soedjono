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
            "url": baseUrl('report/group-test-datatable'),
            "dataSrc": function(jsons) {
                console.log(jsons)
                var return_data = new Array();

                var index = 1;
                jsons.forEach(function(json) {

                    var age = getAgeFull(json.patient_birthdate);
                    var gender = '';
                    if(json.patient_gender == 'M'){
                        gender = 'Laki-laki';
                    }else{
                        gender = 'Perempuan';
                    }
               
                    return_data.push({
                        'no': index,
                        'date': moment(json.created_time).format("DD/MM/YYYY"),
                        'patient_name': json.patient_name,
                        'medrec': json.patient_medrec,
                        'age': age,
                        'gender': gender,
                        'doctor_name': json.doctor_name,
                        'room_name': json.room_name,
                        'group_name': json.room_name,
                        'test_name': json.room_name,
                        'test_result': json.room_name,
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
                'data': 'date'
            },
            {
                'data': 'patient_name'
            },
            {
                'data': 'medrec'
            },
            {
                'data': 'age'
            },
            {
                'data': 'gender'
            },
            {
                'data': 'doctor_name'
            },
            {
                'data': 'room_name'
            },
            {
                'data': 'group_name'
            },
            {
                'data': 'test_name'
            },
            {
                'data': 'test_result'
            },
        ]
    });
}
