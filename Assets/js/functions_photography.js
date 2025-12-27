let currReference = 0;

async function openImage(element, title, type, reference_id){

    if(type == 'Opportunity'){
        $('#divLoading').css('display', 'flex');
        const response = await fetch(`${base_url}/audit_Opp/getOpp/${reference_id}`);
        const data = await response.json();

        $('#question_prefix').html(data.question_prefix);
        $('#question_text').html(data.text);
        $('#question_answers').html(data.answers.join(', '));
        $('#divLoading').css('display', 'none');
        $('#photo-action-panel').show();
    }else{
        $('#photo-action-panel').hide();
    }

    const showImagePanel = document.getElementById('show-image-panel');
    let clone = element.cloneNode();
    clone.classList.replace('of-cover', 'of-contain');
    showImagePanel.innerHTML = '';
    showImagePanel.append(clone);
    $('#show-image-title').html(title);
    $('#show-image-modal').modal('show');
}