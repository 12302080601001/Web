document.addEventListener('DOMContentLoaded', function() {

    // --- Dark Mode Toggle ---
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        // Set initial theme
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }

        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            let theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        });
    }

    // --- AJAX Voting System ---
    document.querySelectorAll('.vote-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const suggestionId = this.dataset.id;
            const voteType = this.dataset.type;

            // Disable buttons to prevent multiple clicks while waiting for response
            document.querySelectorAll(`.vote-btn[data-id='${suggestionId}']`).forEach(btn => btn.disabled = true);

            fetch('/campus/process_vote.php', { // Using the full path for reliability
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `suggestion_id=${suggestionId}&vote_type=${voteType}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById(`upvotes-count-${suggestionId}`).innerText = data.upvotes;
                    document.getElementById(`downvotes-count-${suggestionId}`).innerText = data.downvotes;

                    const totalVotes = data.upvotes + data.downvotes;
                    const upvotePercentage = totalVotes > 0 ? (data.upvotes / totalVotes) * 100 : 0;
                    
                    const voteBar = document.querySelector(`.vote-bar-upvotes[data-id='${suggestionId}']`);
                    if(voteBar) {
                       voteBar.style.width = `${upvotePercentage}%`;
                    }
                    
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('An error occurred. Please check the console for details.');
            })
            .finally(() => {
                // Re-enable buttons after request is complete
                document.querySelectorAll(`.vote-btn[data-id='${suggestionId}']`).forEach(btn => btn.disabled = false);
            });
        });
    });
});