jQuery(document).ready(function($) {
    $('.notice-dismiss').click(function(){
    $('.custom-notice-popup').hide()
  });
  })


  document.getElementById('ai-agree').addEventListener('change', function() {
    document.getElementById('ai-activate').disabled = !this.checked;
});

// Show Modal (Example Usage)

    document.getElementById("open-ai-activation-modal").addEventListener("click", function() {
        document.getElementById('ai-activation-modal').classList.add('active');
    });
          
        document.querySelector('.close-modal').addEventListener('click', function() {
          document.getElementById('ai-activation-modal').classList.remove('active');		
});
    
    document.getElementById("ai-activate").addEventListener("click", function() {
        document.getElementById("ai-activate").innerText="Activating";
        let name = document.getElementById("ai-name").value;
        let email = document.getElementById("ai-email").value;
        let apiKey = document.getElementById("ai-api-key").value;
		let activation_nonce = document.getElementById("autoreply_activation_nonce").value;

        if (!name || !email || !apiKey) {
            alert("All fields are required!");
            return;
        }

        let data = new FormData();
        data.append("action", "autocomment_ai_activate");
        data.append("name", name);
		data.append("api_key", apiKey);
        data.append("email", email);
        data.append("autoreply_activation_nonce", activation_nonce);
        
        fetch(ajaxurl, {
            method: "POST",
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.getElementById("ai-activate").innerText="Activated";
                alert("Plugin Activated!");
                location.reload();
            } else {
                alert("Activation failed! Please try again");
            }
        });
    });



   jQuery(document).ready(function ($) {
        if ($(".ai-reply-admin").length) {
            $(".ai-modal").css({
                "display": "flex",
                "opacity": "1"
            });
        }
    });
    