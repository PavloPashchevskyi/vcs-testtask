function outputInterviewResult() {
    $('form[name="forminterview"]').ajaxForm({
        dataType:"json",
        success: function(response) {
            var resptext = '',
                i = 0;
            for(clause in response) {
                resptext += response[clause];
                if(i<Object.keys(response).length-1) resptext += ', ';
                i++;
            }
            if(i==0) resptext += 'No one conclusion found by that conditions!';
            $('#divresponse').text(resptext);
        },
        error: function() {
            $('#divresponse').text('Getting data from server has been failed! May be you have not sent query to the server');
        }
    });
}

$(document).ready(
            function() {
                $('input[name="submitinterview"]').click(outputInterviewResult);
            }
        );

