
import firebase from "firebase/app";
import "firebase/auth";
import firebaseui from 'firebaseui-ja'
import 'firebaseui/dist/firebaseui.css'

var firebaseuiAuthContainer;

if(document.querySelector('#firebaseui-auth-container')){
    firebaseuiAuthContainer = document.querySelector('#firebaseui-auth-container');
} else {
    firebaseuiAuthContainer = null;
}

var firebaseConfig = {
    apiKey: `${process.env.apiKey}`,
    authDomain: `${process.env.authDomain}`,
    projectId: `${process.env.projectId}`,
    storageBucket: `${process.env.storageBucket}`,
    messagingSenderId: `${process.env.messagingSenderId}`,
    appId: `${process.env.appId}`,
    measurementId: `${process.env.measurementId}`
};
firebase.initializeApp(firebaseConfig);
var ui = new firebaseui.auth.AuthUI(firebase.auth());

if(firebaseuiAuthContainer){
    ui.start('#firebaseui-auth-container', {
        callbacks: {
            signInSuccessWithAuthResult: function(authResult, redirectUrl) {

                document.querySelector('#firelate-backdrop').classList.remove('d-none');
                document.querySelector('#firelate-loader').classList.remove('d-none');
                firebase.auth().currentUser.getIdToken(true).then(function(idToken) {

                        fetch('/login/_____firebase_____verifyIdToken/', {
                            headers: {
                                'Authorization': `Bearer ` + idToken
                            },
                        }).then(function(response){
                            if(response.status == 200){
                                return response.json();
                            } else {
                                location.href = '/error/500?code=FatalError';
                            }
                        }).then(function(result_json){
                            location.href = result_json.redirect_url;
                        });

                    }).catch(function(error) {
                        location.href = '/error/login?code=getIdTokenError';
                    }
                );
            },
            uiShown: function() {
                /*
                var modal = document.createElement("div");
                modal.setAttribute('class', 'modal-backdrop fade show');
                document.body.appendChild(modal);
                */
            }
        },
        signInOptions: [
            firebase.auth.GoogleAuthProvider.PROVIDER_ID,
            {
                forceSameDevice: true,
                provider: firebase.auth.EmailAuthProvider.PROVIDER_ID,
                signInMethod: firebase.auth.EmailAuthProvider.EMAIL_LINK_SIGN_IN_METHOD,
            }
        ],
        tosUrl: 'https://',
        privacyPolicyUrl: 'https://'
    });
}