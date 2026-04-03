document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ff-einsatz-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const messageDiv = document.getElementById('ff-form-message');
        const submitBtn = form.querySelector('.ff-submit-btn');
        
        messageDiv.innerHTML = 'Speichere...';
        messageDiv.className = '';
        submitBtn.disabled = true;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch(ffEinsatz.rest_url + 'ff/v1/einsatz', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': ffEinsatz.nonce
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            submitBtn.disabled = false;
            
            if (res.status === 200 && res.body.success) {
                messageDiv.innerHTML = res.body.message;
                messageDiv.className = 'ff-success';
                form.reset();
            } else {
                messageDiv.innerHTML = res.body.message || 'Ein Fehler ist aufgetreten.';
                messageDiv.className = 'ff-error';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            messageDiv.innerHTML = 'Netzwerkfehler.';
            messageDiv.className = 'ff-error';
        });
    });
});
