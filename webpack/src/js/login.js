
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

    showLoader();

    fetch('/token/', {}).then(function(response){
        if(response.status == 200){
            return response.json();
        } else {
            location.href = '/error/500?code=FatalErrorToken';
        }
    }).then(function(result_json){
        hideLoader();
        firebaseAuthentication(result_json.request_token);
    });

}
function firebaseAuthentication(request_token)
{
    ui.start('#firebaseui-auth-container', {
        callbacks: {
            signInSuccessWithAuthResult: function(authResult, redirectUrl) {
                showLoader();
                firebase.auth().currentUser.getIdToken(true).then(function(idToken) {
                        fetch('/login/_____firebase_____verifyIdToken/?request_token='+request_token, {
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
                hideLoader();
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
function showLoader(){
    document.querySelector('#wpfp-backdrop').classList.remove('d-none');
    document.querySelector('#wpfp-loader').classList.remove('d-none');
}
function hideLoader(){
    document.querySelector('#wpfp-backdrop').classList.add('d-none');
    document.querySelector('#wpfp-loader').classList.add('d-none');
}
