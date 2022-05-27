<?php $this->section('head')->begin(); ?>
<meta name="google-signin-scope" content="profile email">
<meta name="google-signin-client_id" content="255340012981-fm80hfumaqh9ahsosb5lokthg0sd6vke.apps.googleusercontent.com">
<script src="https://apis.google.com/js/platform.js" async defer></script>

<style>
    .popover {
        max-width: 800px;
        min-width: 800px;
        width: 800px;
    }
</style>
<?php $this->section('head')->end(); ?>

<div>
    <h3>Bitcoin Podcasts</h3>

    <script>
        function onSignIn(googleUser) {
            // Useful data for your client-side scripts:
            var profile = googleUser.getBasicProfile();
            console.log("ID: " + profile.getId()); // Don't send this directly to your server!
            console.log('Full Name: ' + profile.getName());
            console.log('Given Name: ' + profile.getGivenName());
            console.log('Family Name: ' + profile.getFamilyName());
            console.log("Image URL: " + profile.getImageUrl());
            console.log("Email: " + profile.getEmail());

            // The ID token you need to pass to your backend:
            id_token = googleUser.getAuthResponse().id_token;
            console.log("ID Token: " + id_token);

            $(".g-signin2").hide();
            $(".sign-out").html("Sign out " + profile.getEmail()).show();

            getPods(id_token);
        }

        function onSignInFail(error) {
            console.log('error', error);
        }

        function signOut() {
            var auth2 = gapi.auth2.getAuthInstance();
            auth2.signOut().then(function () {
                console.log('User signed out.');
                $(".sign-out").hide();
                $(".g-signin2").show();
            });
        }

    </script>

    <div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="onSignInFail" data-theme="dark" data-longtitle="true"></div>
    <button type="button" onclick="signOut();" class="sign-out btn btn-primary mt-2" style="display: none;">Sign Out</button>

    <hr>

    <div class="podcast-player">
        <div class="add-podcast mt-4">
            <a href="#" class="add-pod"><img src="/images/orange-plus.png" width="30" height="30" alt="Add Podcast"></a>
        </div>

        <div class="podcasts mt-4">
        </div>
    </div>
</div>

<?php $this->section('scripts')->begin(); ?>
<script>
    function getPods(id_token) {
        if (id_token) {
            $.ajax({
                "url": "/api/pods",
                "method": "POST",
                "dataType": "json",
                "data": {"id_token": id_token}
            }).done(function(data) {
                if (data.pods.length === 0) {
                    $(".podcasts").html("You have not added any podcasts yet.");
                } else {
                    
                }
            });
        }
    }

    function validURL(str) {
        var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
            '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        return !!pattern.test(str);
    }

    $(".podcast-player").each(function(){
        var $this = $(this);

        $this.on("click", ".xadd-pod-button", function(e) {
            alert('adding...');
            $(".add-pod", $this).popover("hide");
        });

        $(".add-pod", $this).popover({
            'trigger': 'click',
            'placement': 'right',
            'html': true,
            'content': function() {
                var self = $(this);
                return $("<div/>", {"class": "input-group"})
                    .append($("<input/>", {"type": "text", "class": "rss-feed form-control", "placeholder": "Podcast RSS Feed Url"}))
                    .append($("<div/>", {"class": "input-group-append"})
                        .append($("<button/>", {"type": "button", "class": "add-pod-button btn btn-outline-secondary"}).html("Add").on("click", function(e){
                            var input = $(this).closest(".input-group").find(".rss-feed");
                            var rssFeed = input.val();
                            if (validURL(rssFeed)) {
                                self.popover("hide");
                                alert(rssFeed);
                            } else {
                                input.addClass("is-invalid");
                            }
                        })))
                   .append($("<div/>", {"class": "invalid-feedback"}).html("Please enter a valid URL (https://example.com/podcast/rss)."));
            }
        }).on("click", function(e) {
            e.preventDefault();
        });
    });
</script>
<?php $this->section('scripts')->end(); ?>
