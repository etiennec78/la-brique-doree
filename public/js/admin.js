document.querySelectorAll('.ban-btn').forEach(btn => {
    btn.onclick = function() {
        let id = this.dataset.userId;
        let newStatus = this.dataset.banned == '1' ? 0 : 1;

        fetch('/api_ban_user', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'user_id=' + id + '&banned=' + newStatus
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.dataset.banned = data.banned;
                this.textContent = (data.banned == 1) ? 'Débannir' : 'Bannir';
                document.getElementById('user-row-' + id).classList.toggle('banned', data.banned == 1);
            }
        });
    };
});