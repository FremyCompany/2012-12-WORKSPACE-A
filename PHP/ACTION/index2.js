/* Module used to load informations about the connected user */
(function () {

    function updateData(ok, data, errMessage) {
        try {
            if (ok) {
                if ((!window.sCredentials) || (!data) || (data.login != window.sCredentials.login)) {
                    window.sCredentials = data;
                    lastId = null;
                    lastPassword = null;
                    sessionStorage.setItem("sCredentials", JSON.stringify(data));
                    raiseCustomEvent('connectedUserChanged', {});
                }
            } else {
                switch (data) {
                    case 1102:
                        // Licence
                        Dialogs.showMessage("Ceci est votre première connexion sur le site web. Vous devez accepter la licence avant de continuer. Vous allez être redirigé vers le site ShareSpace après fermeture de cette boite de dialogue.", "Première connexion", function () {
                            location.href = "http://sharespace.saint-boni.be";
                        });
                        return;
                    case 1105:
                        // Messages
                        var message = errMessage.split("|") // [id, "nom", "date", "sujet", "message"]
                        message[0] = message[0].split(";")[1];
                        Dialogs.open('Message important', [{
                            type: 'x-text',
                            value: 'Veuillez lire ce message important avant de continuer:'
                        },
                        //{type:'x-html',value:'<small>De: '+unescape(message[1])+'<br />Le: '+unescape(message[2])+'<br />SUJET: '+unescape(message[3])+'</small>'},
                        //{type:'x-text',value:'Le: '+unescape(message[2])},
                        //{type:'x-text',value:'SUJET: '+unescape(message[3])},
                        {
                            type: 'x-html',
                            value: '<div style="padding: 5px; background-color: rgb(200,200,200); overflow: auto; max-height: 250px;"><small style="color: white;">' + unescape(message[1]) + ', le ' + unescape(message[2]) + '<br /><b>Concerne: ' + unescape(message[3]) + '</b></small><div style="margin-top: 5px; max-width: 460px; text-align: justify;">' + unescape(message[4]) + '</div></div>'
                        }], [{
                            type: 'button',
                            value: 'Annuler',
                            onclick: function () {
                                Dialogs.close();
                            }
                        }, {
                            type: 'button',
                            value: 'Continuer',
                            onclick: function () {
                                Secloud.connect(lastId, SHA_ENCODE(lastPassword), parseInt(message[0]), updateData);
                                Dialogs.close();
                            }
                        }]);
                        return;
                    case 1104:
                        // special check for session revival
                        if (cookies.getItem("PHPSESSID") != lastSessID) {
                            login(lastId, lastPassword);
                            return;
                        } else {
                            window.sCredentials = null;
                            lastId = null;
                            lastPassword = null;
                            sessionStorage.setItem("sCredentials", "null");
                            raiseCustomEvent('connectedUserChanged', {});
                        }
                    default:
                        Dialogs.showMessage(errMessage + " (Erreur " + data + ")", "Connexion impossible");
                        return;
                }
            }
        } catch (ex) {}
    }

    function fetchData() {
        Secloud.getMyUserInfo(updateData);
    }

    var lastId, lastPassword, lastSessID;

    function login(id, password) {
        lastId = id;
        lastPassword = password;
        lastSessID = cookies.getItem("PHPSESSID");
        Secloud.connect(lastId, SHA_ENCODE(lastPassword), updateData);
    }

    function logout() {
        lastId = null;
        lastPassword = null;
        Secloud.disconnect();
        updateData(true, window.sCredentialsManager.defaultValue);
    }

    window.sCredentialsManager = {
        update: fetchData, // ()
        login: login, // (id,password)
        logout: logout, // ()
        defaultValue: null
    };
    window.sCredentials = window.sCredentialsManager.defaultValue;

    // check that the user has access to this page
    function checkShowLogin() {
        if ((window.location.pathname != "/" || window.location.search != "") && (!sCredentials || !sCredentials.login)) {
            window.location.href = "/";
        }
    };

    watchCustomEvent('connectedUserChanged', checkShowLogin);

    // if cache is empty
    if (!sessionStorage.getItem("sCredentials")) {

        // fetch from server using XMLHTTPRequest
        fetchData();

    } else {

        // else, read data from cache (which takes less time)
        try {
            window.sCredentials = JSON.parse(sessionStorage.getItem("sCredentials"));
            raiseCustomEvent('connectedUserChanged', {});
            if (!cookies.getItem("PHPSESSID")) {
                fetchData();
            }
        } catch (ex) {
            fetchData();
        }

    }

    // Regulary update connection status (keep session on server)
    setInterval(fetchData, 30000);

})();